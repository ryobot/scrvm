<?php
/**
 * lib/Scrv/Dao/Reviews.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Reviews class
 * @author mgng
 */
class Reviews extends Dao
{
	/**
	 * review
	 * @param int $review_id
	 * @return resultSet
	 */
	public function review( $review_id )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$data = $Dao->select(
				"SELECT t1.* FROM reviews t1 WHERE t1.id=:review_id",
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
	public function lists( $offset, $limit )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$data = $Dao->select(
				"SELECT "
				."t1.*, t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks,"
				."t3.username,t3.img_file as user_img_file "
				."FROM reviews t1 "
				."INNER JOIN albums t2 ON (t1.album_id=t2.id) "
				."INNER JOIN users t3 ON (t1.user_id=t3.id) "
				."ORDER BY t1.created DESC "
				."LIMIT {$offset},{$limit}"
			);
			$data_count = $Dao->select("SELECT count(id) cnt FROM reviews");
			$result["status"] = true;
			$result["data"] = array(
				"reviews" => $data,
				"reviews_count" => $data_count[0]["cnt"],
			);
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
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
	public function view( $user_id, $offset, $limit )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$data = $Dao->select(
				 "SELECT "
				."t1.*, t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks,"
				."t3.username,t3.img_file as user_img_file "
				."FROM reviews t1 "
				."INNER JOIN albums t2 ON (t1.album_id=t2.id) "
				."INNER JOIN users t3 ON (t1.user_id=t3.id) "
				."WHERE user_id=:user_id "
				."ORDER BY t1.created DESC "
				."LIMIT {$offset},{$limit}",
				array("user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $data;
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
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
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		$Dao->beginTransaction();
		try{
			// アルバムが存在するかチェック
			$album_result = $Dao->select(
				"SELECT * FROM albums WHERE id=:album_id",
				array("album_id" => $album_id,)
			);
			if ( count($album_result) !== 1 ) {
				throw new \Exception("not found.");
			}
			// 登録
			$row_count = $Dao->insert(
				 "INSERT INTO reviews (album_id,user_id,body,listening_last,listening_system,created) "
				."VALUES(:album_id,:user_id,:body,:listening_last,:listening_system,now())",
				array(
					"album_id" => $album_id,
					"user_id" => $user_id,
					"body" => $body,
					"listening_last" => $listening_last,
					"listening_system" => $listening_system,
				)
			);
			$result["status"] = true;
			$result["data"]["rowcount"] = $row_count;
			$Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$Dao->rollBack();
		}
		return $result;
	}

	public function del( $user_id, $review_id )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		$Dao->beginTransaction();
		try{
			// reviewが存在するかチェック
			$review_result = $Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			if ( count($review_result) !== 1 ) {
				throw new \Exception("not found.");
			}
			// 削除処理
			$row_count = $Dao->delete(
				"DELETE FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"]["rowcount"] = $row_count;
			$Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$Dao->rollBack();
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
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		$Dao->beginTransaction();
		try{
			// レビューが存在するかチェック
			$album_result = $Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			if ( count($album_result) !== 1 ) {
				throw new \Exception("not found.");
			}
			// 登録
			$row_count = $Dao->insert(
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
			$result["data"]["rowcount"] = $row_count;
			$Dao->commit();
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
			$Dao->rollBack();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$Dao->rollBack();
		}
		return $result;
	}

}
