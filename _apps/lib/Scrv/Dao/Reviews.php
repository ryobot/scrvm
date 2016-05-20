<?php
/**
 * lib/Scrv/Dao/Reviews.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;
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
		if ( ! $this->_Dao->connect($this->_common_ini["db"]) ) {
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
	 * @return resultSet
	 */
	public function lists( $offset, $limit, $user_id = null )
	{
		$result = getResultSet();
		try{
			$my_fav_select = "";
			$my_fav_sql = "";
			$params = array();
			if ( isset($user_id) ) {
				$my_fav_select = ",t6.id as my_fav_id";
				$my_fav_sql = "LEFT JOIN favreviews t6 ON(t1.id=t6.review_id AND t6.user_id=:uid)";
				$params = array("uid" => $user_id);
			}
			$data = $this->_Dao->select("
				SELECT
				t1.*
				,t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks
				,t3.username,t3.img_file AS user_img_file
				,count(t4.id) as reviews_count
				,count(t5.id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON(t1.album_id=t2.id)
				INNER JOIN users t3 ON(t1.user_id=t3.id)
				LEFT JOIN reviews t4 ON(t1.album_id = t4.album_id)
				LEFT JOIN favreviews t5 ON(t1.id=t4.id AND t1.id=t5.review_id)
				{$my_fav_sql}
				GROUP BY t1.id
				ORDER BY t1.created DESC
				LIMIT {$offset},{$limit}",
				$params
			);
			$data_count = $this->_Dao->select("SELECT count(id) cnt FROM reviews");
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
				,count(t4.id) as reviews_count
				,count(t5.id) as fav_reviews_count
				{$my_fav_select}
				FROM reviews t1
				INNER JOIN albums t2 ON(t1.album_id=t2.id)
				INNER JOIN users t3 ON(t1.user_id=t3.id)
				LEFT JOIN reviews t4 ON(t1.album_id = t4.album_id)
				LEFT JOIN favreviews t5 ON(t1.id=t4.id AND t1.id=t5.review_id)
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
	 * view
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
			// 登録
			$row_count = $this->_Dao->insert(
				 "INSERT INTO reviews (album_id,user_id,body,listening_last,listening_system,created) "
				."VALUES(:album_id,:user_id,:body,:listening_last,:listening_system,:now)",
				array(
					"album_id" => $album_id,
					"user_id" => $user_id,
					"body" => $body,
					"listening_last" => $listening_last,
					"listening_system" => $listening_system,
					"now" => date("Y-m-d H:i:s", $this->_nowTimestamp),
				)
			);

			// point加算処理
			$this->syncReviewUpdate($user_id, $album_id, true);

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

			// point減算処理
			$this->syncReviewUpdate($user_id, $review_result[0]["album_id"], false);

			// 削除処理
			$row_count = $this->_Dao->delete(
				"DELETE FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
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
	 *
	 * @param int $user_id
	 * @param int $album_id
	 * @param boolean $is_add 加算時true,減算時false
	 * @return boolean
	 */
	public function syncReviewUpdate($user_id, $album_id, $is_add = true)
	{
		// 自分以外のレヴューの一番新しいもの取得, なければ何もしない
		$reviews = $this->_Dao->select(
			"SELECT * FROM reviews WHERE album_id=:aid AND user_id<>:uid ORDER BY created DESC LIMIT 0,1",
			array("aid" => $album_id, "uid" => $user_id)
		);
		if (count($reviews) === 0) {
			return false;
		}

		// 自分のレビューを古い順に取得、1件じゃなかったら(レビュー済みor未レビューの場合)何もしない
		$my_reviews = $this->_Dao->select(
			"SELECT * FROM reviews WHERE album_id=:aid AND user_id=:uid ORDER BY created ASC",
			array("aid" => $album_id, "uid" => $user_id)
		);
		if (count($my_reviews) !== 1){
			return false;
		}

		// sync point 計算処理
		$Syncs = new Syncs();
		$sync_ponts = array();
		foreach($reviews as $review) {
			foreach($my_reviews as $my_review){
				$point = $Syncs->calcPoint($review["created"], $my_review["created"], $my_review["listening_last"]);
				$sync_ponts[] = array(
					"user_id" => $review["user_id"],
					"user_com_id" => $user_id,
					"diff" => $point["diff"],
					"point" => $point["point"] * ($is_add ? 1 : -1),	// レビュー追加は加算、削除は減算
				);
			}
		}

		// 加算実行
		foreach($sync_ponts as $sync_point) {
			// syncsにデータがあるか
			$syncs = $this->_Dao->select(
				"SELECT * FROM syncs WHERE user_id=:uid AND user_com_id=:ucid",
				array("uid"=>$sync_point["user_id"], "ucid"=>$sync_point["user_com_id"])
			);
			// あればupdate、なければinsert
			if ( count($syncs) > 0 ) {
				$point = $syncs[0]["sync_point"] + $sync_point["point"] < 0 ? 0 : $syncs[0]["sync_point"] + $sync_point["point"];
				$this->_Dao->update(
					"UPDATE syncs SET sync_point=:sp WHERE user_id IN(:id1,:id2) AND user_com_id IN(:id3,:id4)",
					array(
						"sp" => $point,
						"id1" => $syncs[0]["user_id"],
						"id2" => $syncs[0]["user_com_id"],
						"id3" => $syncs[0]["user_id"],
						"id4" => $syncs[0]["user_com_id"],
					)
				);
			} else {
				$point = $sync_point["point"] < 0 ? 0 : $sync_point["point"];
				$this->_Dao->insert(
					"INSERT INTO syncs (user_id,user_com_id,sync_point)VALUES(:id1,:id2,:sp1),(:id3,:id4,:sp2)",
					array(
						"id1" => $sync_point["user_id"],
						"id2" => $sync_point["user_com_id"],
						"sp1" => $point,
						"id3" => $sync_point["user_com_id"],
						"id4" => $sync_point["user_id"],
						"sp2" => $point,
					)
				);
			}
		}

		return true;
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
