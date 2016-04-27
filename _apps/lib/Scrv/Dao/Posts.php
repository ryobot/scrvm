<?php
/**
 * lib/Scrv/Dao/Posts.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Posts class
 * @author mgng
 */
class Posts extends Dao
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
	 * 一覧取得
	 * @param integer $offset
	 * @param integer $limit
	 * @return resultSet
	 */
	public function lists( $offset, $limit )
	{
		$result = getResultSet();
		try{
			$data = $this->_Dao->select(
				 "SELECT t1.*,t2.username FROM posts t1 "
				."LEFT JOIN users t2 ON (t1.user_id=t2.id) "
				."ORDER BY t1.created DESC LIMIT {$offset},{$limit}"
			);
			$data_count = $this->_Dao->select("SELECT count(id) AS cnt FROM posts");
			$result["status"] = true;
			$result["data"] = array(
				"lists" => $data,
				"lists_count" => $data_count[0]["cnt"],
			);
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * view
	 * @param int $id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function view($id)
	{
		$result = getResultSet();
		try{
			$data = $this->_Dao->select("
				SELECT t1.*,t2.username FROM posts t1
				LEFT JOIN users t2 ON (t1.user_id=t2.id)
				WHERE t1.id=:id",
				array("id" => $id,)
			);
			if ( count($data) !== 1 ) {
				throw new \Exception("post not found.");
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
	 * add 処理
	 * @param string $title
	 * @param string $body
	 * @param integer $reply_id
	 * @param integer $user_id
	 * @param integer $album_id
	 * @return resultSet
	 */
	public function add( $title, $body, $reply_id, $user_id, $album_id )
	{
		$result = getResultSet();
		$this->_Dao->beginTransaction();
		try{
			$row_count = $this->_Dao->insert(
				 "INSERT INTO posts (title,body,reply_id,user_id,album_id,created) "
				."VALUES(:title,:body,:reply_id,:user_id,:album_id,now())",
				array(
					"title" => $title,
					"body" => $body,
					"reply_id" => $reply_id,
					"user_id" => $user_id,
					"album_id" => $album_id,
				)
			);
			$result["status"] = true;
			$result["data"]["rowcount"] = $row_count;
			$this->_Dao->commit();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

}
