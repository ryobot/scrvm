<?php
/**
 * lib/Scrv/Dao/Albums.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Tracks class
 * @author mgng
 */
class Tracks extends Dao
{
	/**
	 * favtracks by album_id and user_id
	 * @param integer $album_id
	 * @param integer $user_id
	 * @return resultSet
	 */
	public function favtracks( $album_id, $user_id )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$search_result = $Dao->select(
				 "SELECT t1.*, t2.user_id FROM tracks t1 "
				."INNER JOIN favtracks t2 ON (t1.id=t2.track_id) "
				."WHERE t1.album_id=:album_id AND t2.user_id=:user_id",
				array("album_id" => $album_id, "user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $search_result;
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * favtracks by user_id
	 * @param int $user_id
	 * @return resultSet
	 */
	public function favtracksByUserId( $user_id, $offset, $limit )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		try{
			$search_result = $Dao->select(
				 "SELECT t1.*,t3.artist,t3.title,t3.img_file,t3.year "
				."FROM tracks t1 "
				."INNER JOIN favtracks t2 ON (t1.id=t2.track_id) "
				."LEFT  JOIN albums    t3 ON (t1.album_id=t3.id) "
				."WHERE t2.user_id=:user_id "
				."ORDER BY t2.created DESC "
				."LIMIT {$offset},{$limit}",
				array("user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $search_result;
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * fav update
	 * @param integer $track_id
	 * @param integer $user_id
	 * @return string
	 */
	public function fav( $track_id, $user_id )
	{
		$result = getResultSet();
		$Dao = new Dao();
		if ( ! $Dao->connect($this->_common_ini["db"]) ) {
			$result["messages"][] = "db connect error - " . $Dao->getErrorMessage();
			return $result;
		}
		$Dao->beginTransaction();
		try{
			// 存在したらdelete
			// 存在しなければinsert
			$params = array("track_id" => $track_id,"user_id" => $user_id,);
			$search_result = $Dao->select(
				"SELECT * FROM favtracks WHERE track_id=:track_id AND user_id=:user_id",
				$params
			);
			if ( count($search_result) > 0 ) {
				$Dao->delete(
					"DELETE FROM favtracks WHERE track_id=:track_id AND user_id=:user_id",
					$params
				);
			} else {
				$Dao->insert(
					"INSERT INTO favtracks (favtype,track_id,user_id,created) "
					."VALUES('alltime',:track_id,:user_id,now())",
					$params
				);
			}
			$result["status"] = true;
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
