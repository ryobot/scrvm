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
	 * 一覧取得
	 * @param integer $offset
	 * @param integer $limit
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
				 "SELECT t1.*,t2.username FROM posts t1 "
				."LEFT JOIN users t2 ON (t1.user_id=t2.id) "
				."ORDER BY t1.created DESC "
				."LIMIT {$offset},{$limit}",
				array()
			);
			$data_count = $Dao->select("SELECT count(id) AS cnt FROM posts");
			$result["status"] = true;
			$result["data"] = array(
				"lists" => $data,
				"lists_count" => $data_count[0]["cnt"],
			);
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
	 * @param integer $user_id
	 * @param integer $album_id
	 * @return resultSet
	 */
	public function add( $title, $body, $user_id, $album_id )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}

		$Dao->beginTransaction();
		try{
			$row_count = $Dao->insert(
				 "INSERT INTO posts (title,body,user_id,album_id,created) "
				."VALUES(:title,:body,:user_id,:album_id,now())",
				array(
					"title" => $title,
					"body" => $body,
					"user_id" => $user_id,
					"album_id" => $album_id,
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
