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

			// 該当アルバムレビューの先頭2件取得。2件かつ、先頭が自分のIDであれば加算処理
			$review_list = $this->_Dao->select(
				"SELECT * FROM reviews WHERE album_id=:aid ORDER BY created DESC LIMIT 0,2",
				array("aid" => $album_id)
			);
			if (count($review_list) === 2 || $review_list[0]["user_id"] === $user_id) {
				$this->_syncPointAdd($review_list, "+");
			}

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

	private function _syncPointAdd($review_list, $operator = "+")
	{
		// calc sync_point
		$Syncs = new Syncs();
		$sync_points = array();
		$sync_point_result = $Syncs->calcReviewsPoint($review_list);
		foreach($sync_point_result as $sync_point) {
			$idx = "{$sync_point["user_com_id"]}_{$sync_point["user_id"]}";
			if ( !isset( $sync_points[$idx] ) ) {
				$sync_points[$idx] = 0;
			}
			$sync_points[$idx] += $sync_point["sync"]["point"];
		}

		// add point
		foreach( $sync_points as $key=>$point ) {
			$tmp = explode("_", $key);
			$_user_id = (int)$tmp[0];
			$_user_com_id = (int)$tmp[1];
			// 存在したらupdate、しなければinsert
			$sync_list = $this->_Dao->select(
				"SELECT * FROM syncs WHERE user_id=:uid AND user_com_id=:ucid"
				,array("uid" => $_user_id, "ucid" => $_user_com_id,)
			);
			$sql = "UPDATE syncs SET sync_point = sync_point {$operator} :sp WHERE user_id=:uid AND user_com_id=:ucid";
			$params = array("uid"=>$_user_id, "ucid"=>$_user_com_id, "sp"=>$point,);
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


			// 減算処理
			// 削除対象レビューが含まれる同一アルバムレビュー一覧を取得
			$reviews_list = $this->_Dao->select("
				SELECT t1.* FROM reviews t1
				INNER JOIN reviews t2 ON (t1.album_id = t2.album_id AND t2.id=:rid)
				ORDER BY t1.created DESC
			",
				array("rid" => $review_id,)
			);
			// 該当レビューの前後のデータを取得する
			$new = null;
			$current = null;
			$old = null;
			for($i=0,$len=count($reviews_list); $i<$len; $i++) {
				if ( $reviews_list[$i]["id"] === $review_id ) {
					$new = isset( $reviews_list[$i-1] ) ? $reviews_list[$i-1] : null;
					$current = $reviews_list[$i];
					$old = isset( $reviews_list[$i+1] ) ? $reviews_list[$i+1] : null;
					break;
				}
			}
			// 減算実行
			if ( $new !== null ) {
				$this->_syncPointAdd(array($new, $current), "-");
			}
			if ( $old !== null) {
				$this->_syncPointAdd(array($current, $old), "-");
			}


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
