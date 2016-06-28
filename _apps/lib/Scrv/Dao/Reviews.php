<?php
/**
 * lib/Scrv/Dao/Reviews.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
use lib\Util\Syncs as Syncs;

/**
 * Reviews class
 * @author tomita
 */
class Reviews extends Dao
{
	/**
	 * Dao object
	 * @var Dao
	 */
	private $_Dao = null;

	/**
	 * construct
	 * @return boolean
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_Dao = new Dao();
		if ( ! $this->_Dao->connect(self::$_common_ini["db"]) ) {
			echo $this->_Dao->getErrorMessage();
			exit;
		}
		return true;
	}

	/**
	 * review
	 * @param int $review_id
	 * @return resultSet
	 */
	public function review( $review_id )
	{
		$result = getResultSet();
		try{
			$data = $this->_Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id",
				array("review_id" => $review_id,)
			);
			if ( count($data) !== 1 ) {
				throw new \Exception("not found");
			}
			$result["status"] = true;
			$result["data"] = $data[0];
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * lists
	 * @param int $offset
	 * @param int $limit
	 * @param int $user_id null OK
	 * @param string $hashtag default null
	 * @return resultSet
	 */
	public function lists( $offset, $limit, $user_id, $hashtag = null )
	{
		$result = getResultSet();
		try{
			$my_fav_select = "";
			$my_fav_sql = "";
			$hashtags_sql = "";
			$params = array();
			$params_count = array();
			if ( isset($user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:uid)";
				$params = array("uid" => $user_id);
			}
			if ( isset($hashtag) ) {
				$hashtags_sql = "INNER JOIN hashtags t7 ON(t1.id=t7.review_id AND t7.tag=:hashtag)";
				$params["hashtag"] = $hashtag;
				$params_count["hashtag"] = $hashtag;
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks
				,t3.username,t3.img_file AS user_img_file
				,(SELECT count(t4.id) FROM reviews t4 WHERE t1.album_id=t4.album_id) as reviews_count
				,(SELECT count(t5.id) FROM favreviews t5 WHERE t1.id=t5.review_id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON(t1.album_id=t2.id)
				INNER JOIN users t3 ON(t1.user_id=t3.id)
				{$hashtags_sql}
				{$my_fav_sql}
				ORDER BY t1.created DESC
				LIMIT {$offset},{$limit}",
				$params
			);
			$data_count = $this->_Dao->select("
				SELECT count(t1.id) cnt
				FROM reviews t1
				{$hashtags_sql}",
				$params_count
			);
			$result["status"] = true;
			$result["data"] = array(
				"reviews" => $data,
				"reviews_count" => $data_count[0]["cnt"],
			);
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * view by id
	 * @param int $id
	 * @return resultSet
	 */
	public function viewById( $id, $user_id = null )
	{
		$result = getResultSet();
		try{
			$my_fav_select = "";
			$my_fav_sql = "";
			$params = array("id" => $id,);
			if ( isset($user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:uid)";
				$params["uid"] = $user_id;
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks
				,t3.username,t3.img_file AS user_img_file
				,(SELECT count(t4.id) FROM reviews t4 WHERE t1.album_id=t4.album_id) as reviews_count
				,(SELECT count(t5.id) FROM favreviews t5 WHERE t1.id=t5.review_id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON(t1.album_id=t2.id)
				INNER JOIN users t3 ON(t1.user_id=t3.id)
				{$my_fav_sql}
				WHERE t1.id=:id
				GROUP BY t1.id",
				$params
			);
			if ( count($data) === 1 ) {
				$result["status"] = true;
				$result["data"] = $data[0];
			}
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * fav_reviews user lists
	 * @param int $review_id
	 * @return resultSet
	 */
	public function favReviewsUserLists($review_id)
	{
		$result = getResultSet();
		try{
			$params = array("rid" => $review_id,);
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.username,t2.img_file AS user_img_file
				FROM favreviews t1
				INNER JOIN users t2 ON (t1.user_id=t2.id)
				WHERE t1.review_id=:rid
				ORDER BY t1.created",
				$params
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * view
	 * @param integer $user_id
	 * @param integer $offset
	 * @param integer $limit
	 * @param int $own_user_id
	 * @return resultSet
	 */
	public function view( $user_id, $offset, $limit, $own_user_id=null )
	{
		$result = getResultSet();
		try{

			$my_fav_select = "";
			$my_fav_sql = "";
			$params = array("user_id" => $user_id,);
			if ( isset($own_user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:ouid)";
				$params["ouid"] = $own_user_id;
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks
				,t3.username,t3.img_file as user_img_file
				,(SELECT count(t4.id) FROM reviews t4 WHERE t1.album_id=t4.album_id) as reviews_count
				,count(t5.id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON (t1.album_id=t2.id)
				INNER JOIN users t3 ON (t1.user_id=t3.id)
				LEFT JOIN favreviews t5 ON(t1.id=t5.review_id)
				{$my_fav_sql}
				WHERE t1.user_id=:user_id
				GROUP BY t1.id
				ORDER BY t1.created DESC
				LIMIT {$offset},{$limit}",
				$params
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * view by date
	 * @param integer $user_id
	 * @param integer $offset
	 * @param integer $limit
	 * @param int $own_user_id
	 * @return resultSet
	 */
	public function viewByDate( $user_id, $offset, $limit, $own_user_id=null, $date=null )
	{
		$result = getResultSet();
		try{
			$my_fav_select = "";
			$my_fav_sql = "";
			$date_sql = "";
			$params = array("user_id" => $user_id,);
			if ( isset($own_user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:ouid)";
				$params["ouid"] = $own_user_id;
			}
			if ( isset($date) ) {
				$year = substr($date, 0, 4);
				$month = substr($date, 4, 2);
				$Datetime = new \DateTime("{$year}-{$month}-01");
				$date_sql = "AND :date_from <= t1.created AND t1.created < :date_to";
				$params["date_from"] = $Datetime->format("Y-m-d 00:00:00");
				$params["date_to"] = $Datetime->add(new \DateInterval("P1M"))->format("Y-m-d 00:00:00");
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks
				,t3.username,t3.img_file as user_img_file
				,(SELECT count(t4.id) FROM reviews t4 WHERE t1.album_id=t4.album_id) as reviews_count
				,count(t5.id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON (t1.album_id=t2.id)
				INNER JOIN users t3 ON (t1.user_id=t3.id)
				LEFT JOIN favreviews t5 ON(t1.id=t5.review_id)
				{$my_fav_sql}
				WHERE t1.user_id=:user_id
				{$date_sql}
				GROUP BY t1.id
				ORDER BY t1.created DESC
				LIMIT {$offset},{$limit}",
				$params
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * favReviews
	 * @param integer $user_id
	 * @param integer $offset
	 * @param integer $limit
	 * @return resultSet
	 */
	public function favReviews( $user_id, $offset, $limit, $own_user_id=null )
	{
		$result = getResultSet();
		try{

			$my_fav_select = "";
			$my_fav_sql = "";
			$params = array("user_id" => $user_id,);
			if ( isset($own_user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:ouid)";
				$params["ouid"] = $own_user_id;
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks
				,t3.username,t3.img_file as user_img_file
				,count(t5.id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON (t1.album_id=t2.id)
				INNER JOIN users t3 ON (t1.user_id=t3.id)
				INNER JOIN favreviews t5 ON(t1.id=t5.review_id)
				{$my_fav_sql}
				WHERE t5.user_id=:user_id
				GROUP BY t1.id
				ORDER BY t1.created DESC
				LIMIT {$offset},{$limit}",
				$params
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * add
	 * @param int $user_id
	 * @param int $album_id
	 * @param string $listening_last
	 * @param string $listening_system
	 * @param string $body
	 * @return resultSet
	 * @throws \Exception
	 */
	public function add( $user_id, $album_id, $listening_last, $listening_system, $body )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// アルバムが存在するかチェック
			$album_result = $this->_Dao->select("SELECT * FROM albums WHERE id=:album_id",array("album_id" => $album_id,));
			if ( count($album_result) !== 1 ) {
				throw new \Exception("not found.");
			}

			// 登録前レビューリスト
			$pre_review_list = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid GROUP BY user_id ORDER BY created DESC",
				array("aid" => $album_id)
			);

			// 登録, 登録した最後のidを取得
			$row_count = $this->_Dao->insert(
				 "INSERT INTO reviews (album_id,user_id,body,listening_last,listening_system,created) "
				."VALUES(:album_id,:user_id,:body,:listening_last,:listening_system,:now)",
				array(
					"album_id" => $album_id,
					"user_id" => $user_id,
					"body" => $body,
					"listening_last" => $listening_last,
					"listening_system" => $listening_system,
					"now" => date("Y-m-d H:i:s", self::$_nowTimestamp),
				)
			);
			$posted_review_id = $this->_Dao->lastInsertId("id");

			// ハッシュタグ更新処理
			$this->_updateHashTags($posted_review_id, $user_id, $body);

			// 登録後レビューリスト
			$post_review_list = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid GROUP BY user_id ORDER BY created DESC",
				array("aid" => $album_id)
			);

			// 件数が異なる場合は計算処理
			if ( count($pre_review_list) !== count($post_review_list) ){
				$this->_syncPointAdd($pre_review_list, $post_review_list, "+");
			}

			$result["status"] = true;
			$result["data"] = array(
				"row_count" => $row_count,
				"album_data" => $album_result[0],
				"posted_review_id" => $posted_review_id,
			);
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

	/**
	 * レビュー内ハッシュタグ更新処理（delete insert）
	 * @param int $review_id
	 * @param int $user_id
	 * @param string $body
	 * @return boolean
	 */
	private function _updateHashTags($review_id, $user_id, $body)
	{
		$this->_Dao->delete(
			"DELETE FROM hashtags WHERE review_id=:rid AND create_user_id=:uid",
			array("rid" => $review_id, "uid" => $user_id,)
		);
		$ReviewsParse = new ReviewsParse();
		$hashTags = $ReviewsParse->hashTags($body);
		if ( count($hashTags) === 0 ) {
			return true;
		}
		foreach($hashTags as $tag) {
			$row_count = $this->_Dao->insert("
				INSERT INTO hashtags (review_id,create_user_id,tag,created)
				VALUES(:rid,:uid,:tag,:now)",
				array(
					"rid" => $review_id,
					"uid" => $user_id,
					"tag" => $tag,
					"now" => date("Y-m-d H:i:s", self::$_nowTimestamp),
				)
			);
		}
		return true;
	}

	private function _syncPointAdd( $pre_review_list, $post_review_list, $operator = "+")
	{
		// 変更前変更後のすべての組み合わせを取得
		$Syncs = new Syncs();
		$pre_sync_point_result = $Syncs->calcReviewsPoint($pre_review_list);
		$post_sync_point_result = $Syncs->calcReviewsPoint($post_review_list);

		// 差分を取得
		$sync_point_diff = $Syncs->calcReviewDiff($pre_sync_point_result, $post_sync_point_result);

		// add point
		foreach( $sync_point_diff as $row ) {
			$user_id = $row["user_id"];
			$user_com_id = $row["user_com_id"];
			$point = $row["sync"]["point"];
			// 存在したらupdate、しなければinsert
			$sync_list = $this->_Dao->select(
				"SELECT * FROM syncs WHERE user_id=:uid AND user_com_id=:ucid"
				,array("uid" => $user_id, "ucid" => $user_com_id,)
			);
			$sql = "UPDATE syncs SET sync_point = sync_point {$operator} :sp WHERE user_id=:uid AND user_com_id=:ucid";
			$params = array("uid"=>$user_id, "ucid"=>$user_com_id, "sp"=>$point,);
			if ( count($sync_list) === 0 ) {
				$sql = "INSERT INTO syncs (user_id,user_com_id,sync_point) VALUES(:uid,:ucid,:sp)";
			}
			$this->_Dao->update($sql, $params);
		}

		return true;
	}

	/**
	 * delete
	 * @param int $user_id
	 * @param int $review_id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function del( $user_id, $review_id )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// reviewが存在するかチェック
			$review_result = $this->_Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			if ( count($review_result) !== 1 ) {
				throw new \Exception("not found.");
			}
			$album_id = $review_result[0]["album_id"];

			// 削除前レビューリスト
			$pre_review_list = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid GROUP BY user_id ORDER BY created DESC",
				array("aid" => $album_id)
			);

			// 削除処理
			$row_count = $this->_Dao->delete(
				"DELETE FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);

			// 削除後レビューリスト
			$post_review_list = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid GROUP BY user_id ORDER BY created DESC",
				array("aid" => $album_id)
			);

			// 件数が異なる場合は計算処理
			if ( count($pre_review_list) !== count($post_review_list) ){
				$this->_syncPointAdd($pre_review_list, $post_review_list, "-");
			}

			$result["status"] = true;
			$result["data"]["rowcount"] = $row_count;
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

	/**
	 * edit
	 * @param int $user_id
	 * @param int $review_id
	 * @param string $listening_last
	 * @param string $listening_system
	 * @param string $body
	 * @return resultSet
	 * @throws \Exception
	 */
	public function edit( $user_id, $review_id, $listening_last, $listening_system, $body )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// レビューが存在するかチェック
			$review_result = $this->_Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			if ( count($review_result) !== 1 ) {
				throw new \Exception("not found.");
			}

			// アルバムが存在するかチェック
			$album_result = $this->_Dao->select(
				"SELECT * FROM albums WHERE id=:album_id",
				array("album_id" => $review_result[0]["album_id"],)
			);
			if ( count($album_result) !== 1 ) {
				throw new \Exception("not found.");
			}

			// 登録
			$row_count = $this->_Dao->insert(
				 "UPDATE reviews "
				."SET body=:body,listening_last=:listening_last,listening_system=:listening_system "
				."WHERE id=:review_id",
				array(
					"review_id" => $review_id,
					"body" => $body,
					"listening_last" => $listening_last,
					"listening_system" => $listening_system,
				)
			);

			// ハッシュタグ更新処理
			$this->_updateHashTags($review_id, $user_id, $body);

			$result["status"] = true;
			$result["data"] = array(
				"row_count" => $row_count,
				"album_data" => $album_result[0],
			);
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

	/**
	 * review fav
	 * @param integer $review_id
	 * @param integer $user_id
	 * @return string
	 * @throws \Exception
	 */
	public function fav($review_id, $user_id)
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			// レヴューそのものがなければエラー
			$review_result = $this->_Dao->select("SELECT * FROM reviews WHERE id=:rid",array("rid" => $review_id,));
			if ( count($review_result) !== 1 ) {
				throw new \Exception("review not found.");
			}

			// favreview取得、存在したらdel,なければinsert
			$fav_reviews_result = $this->_Dao->select(
				"SELECT * FROM favreviews WHERE review_id=:rid and user_id=:uid",
				array("rid" => $review_id, "uid" => $user_id,)
			);
			$is_fav_exist = count($fav_reviews_result) > 0;
			$operation = "off";
			if ( $is_fav_exist ) {
				$row_count = $this->_Dao->insert(
					"DELETE FROM favreviews WHERE review_id=:rid and user_id=:uid",
					array("rid" => $review_id, "uid" => $user_id,)
				);
				$operation = "off";
			} else {
				$row_count = $this->_Dao->insert(
					"INSERT INTO favreviews (review_id,user_id,created) VALUES(:rid,:uid,now())",
					array("rid" => $review_id, "uid" => $user_id,)
				);
				$operation = "on";
			}

			// 該当reviewのfav数を取得
			$fav_reviews_count_result = $this->_Dao->select(
				"SELECT count(*) AS fav_count FROM favreviews WHERE review_id=:rid",
				array("rid" => $review_id)
			);

			$result["status"] = true;
			$result["data"] = array(
				"operation" => $operation,
				"fav_count" => $fav_reviews_count_result[0]["fav_count"],
			);
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

}
