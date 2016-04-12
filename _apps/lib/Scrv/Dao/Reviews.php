<?php
/**
 * lib/Scrv/Dao/Reviews.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

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
	 * resultSet
	 * @var array
	 */
	private $_result = null;

	/**
	 * construct
	 * @return boolean
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_result = getResultSet();
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
		try{
			$data = $this->_Dao->select("SELECT * FROM reviews WHERE id=:review_id",array("review_id" => $review_id,));
			if ( count($data) !== 1 ) {
				throw new \Exception("not found");
			}
			$this->_result["status"] = true;
			$this->_result["data"] = $data[0];
		} catch( \Exception $ex ) {
			$this->_result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$this->_result["messages"][] = "db error - " . $e->getMessage();
		}
		return $this->_result;
	}

	/**
	 * lists
	 * @param int $offset
	 * @param int $limit
	 * @return resultSet
	 */
	public function lists( $offset, $limit )
	{
		try{
			$data = $this->_Dao->select(
				"SELECT "
				."t1.*, t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks,"
				."t3.username,t3.img_file as user_img_file "
				."FROM reviews t1 "
				."INNER JOIN albums t2 ON (t1.album_id=t2.id) "
				."INNER JOIN users t3 ON (t1.user_id=t3.id) "
				."ORDER BY t1.created DESC LIMIT {$offset},{$limit}"
			);
			$data_count = $this->_Dao->select("SELECT count(id) cnt FROM reviews");
			$this->_result["status"] = true;
			$this->_result["data"] = array(
				"reviews" => $data,
				"reviews_count" => $data_count[0]["cnt"],
			);
		} catch( \PDOException $e ) {
			$this->_result["messages"][] = "db error - " . $e->getMessage();
		}
		return $this->_result;
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
		try{
			$data = $this->_Dao->select(
				 "SELECT t1.*, t2.artist,t2.title,t2.img_url,t2.img_file,t2.year,t2.favalbum_count,t2.tracks,"
				."t3.username,t3.img_file as user_img_file "
				."FROM reviews t1 "
				."INNER JOIN albums t2 ON (t1.album_id=t2.id) "
				."INNER JOIN users t3 ON (t1.user_id=t3.id) "
				."WHERE user_id=:user_id "
				."ORDER BY t1.created DESC LIMIT {$offset},{$limit}",
				array("user_id" => $user_id,)
			);
			$this->_result["status"] = true;
			$this->_result["data"] = $data;
		} catch( \PDOException $e ) {
			$this->_result["messages"][] = "db error - " . $e->getMessage();
		}
		return $this->_result;
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
				."VALUES(:album_id,:user_id,:body,:listening_last,:listening_system,now())",
				array(
					"album_id" => $album_id,
					"user_id" => $user_id,
					"body" => $body,
					"listening_last" => $listening_last,
					"listening_system" => $listening_system,
				)
			);
			$this->_result["status"] = true;
			$this->_result["data"]["rowcount"] = $row_count;
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$this->_result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$this->_result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $this->_result;
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
			// 削除処理
			$row_count = $this->_Dao->delete(
				"DELETE FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
			);
			$this->_result["status"] = true;
			$this->_result["data"]["rowcount"] = $row_count;
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$this->_result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$this->_result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $this->_result;
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
		$this->_Dao->beginTransaction();
		try{
			// レビューが存在するかチェック
			$album_result = $this->_Dao->select(
				"SELECT * FROM reviews WHERE id=:review_id AND user_id=:user_id",
				array("review_id" => $review_id, "user_id" => $user_id,)
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
			$this->_result["status"] = true;
			$this->_result["data"]["rowcount"] = $row_count;
			$this->_Dao->commit();
		} catch( \Exception $ex ) {
			$this->_result["messages"][] = $ex->getMessage();
			$this->_Dao->rollBack();
		} catch( \PDOException $e ) {
			$this->_result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $this->_result;
	}

}
