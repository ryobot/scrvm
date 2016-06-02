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
	 * favtracks by album_id and user_id
	 * @param integer $album_id
	 * @param integer $user_id
	 * @return resultSet
	 */
	public function favtracks( $album_id, $user_id )
	{
		$result = getResultSet();
		try{
			$search_result = $this->_Dao->select(
				 "SELECT t1.*, t2.user_id FROM tracks t1 "
				."INNER JOIN favtracks t2 ON (t1.id=t2.track_id) "
				."WHERE t1.album_id=:album_id AND t2.user_id=:user_id",
				array("album_id" => $album_id, "user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $search_result;
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
		try{
			$search_result = $this->_Dao->select(
				 "SELECT t1.*,t3.artist,t3.title,t3.img_file,t3.year "
				."FROM tracks t1 "
				."INNER JOIN favtracks t2 ON (t1.id=t2.track_id) "
				."LEFT  JOIN albums    t3 ON (t1.album_id=t3.id) "
				."WHERE t2.user_id=:user_id "
				."ORDER BY t2.created DESC LIMIT {$offset},{$limit}",
				array("user_id" => $user_id,)
			);
			$result["status"] = true;
			$result["data"] = $search_result;
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
		$this->_Dao->beginTransaction();
		try{
			// sync point (track)
			$add_point = 2;
			$oparation = "insert";

			// 存在したらdeleteでpoint減算, しなければinsert or updateで加算
			$params = array("tid" => $track_id,"uid" => $user_id,);
			$search_result = $this->_Dao->select(
				"SELECT * FROM favtracks WHERE track_id=:tid AND user_id=:uid"
				,$params
			);
			if ( count($search_result) > 0 ) {
				$this->_Dao->delete("DELETE FROM favtracks WHERE track_id=:tid AND user_id=:uid",$params);
				$add_point = -2;
				$oparation = "delete";
			} else {
				$this->_Dao->insert("INSERT INTO favtracks(favtype,track_id,user_id,created) VALUES('alltime',:tid,:uid,now())",$params);
			}

			// add sync point
			$this->_addSyncPoints($track_id, $user_id, $add_point);

			$result["status"] = true;
			$result["data"] = array(
				"operation" => $oparation,
			);
			$this->_Dao->commit();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
			$this->_Dao->rollBack();
		}
		return $result;
	}

	private function _addSyncPoints($track_id, $user_id, $add_point)
	{
		$params = array("tid" => $track_id,"uid" => $user_id,);
		$fav_user_id_list = $this->_Dao->select("SELECT user_id FROM favtracks WHERE track_id=:tid AND user_id<>:uid",$params);
		if (count($fav_user_id_list) === 0) {
			return true;
		}
		// syncs.sync_pointが存在するか確認
		foreach ($fav_user_id_list as $fav_user_id) {
			$syncs_params = array(
				"id1" => $fav_user_id["user_id"],
				"id2" => $user_id,
				"id3" => $fav_user_id["user_id"],
				"id4" => $user_id,
			);
			$sync_list = $this->_Dao->select(
				"SELECT id,sync_point FROM syncs WHERE user_id IN(:id1,:id2) AND user_com_id IN(:id3,:id4)",
				$syncs_params
			);
			if ( count($sync_list) === 0 ){
				// 2行insert
				$this->_Dao->insert(
					"INSERT INTO syncs(user_id,user_com_id,sync_point)VALUES(:id1,:id2,{$add_point}),(:id4,:id3,{$add_point})",
					$syncs_params
				);
			} else {
				// 加算してupdate,加算した数値が0未満の場合は0に丸める
				foreach($sync_list as $sync) {
					$add_sync_point = $sync["sync_point"] + $add_point < 0 ? 0 : $sync["sync_point"] + $add_point;
					$this->_Dao->update(
						"UPDATE syncs SET sync_point=:add_sync_point WHERE id=:id",
						array("add_sync_point" => $add_sync_point,"id" => $sync["id"],)
					);
				}
			}
		}
		return true;
	}

	/**
	 * favCount
	 * @param int $track_id
	 * @return resultSet
	 */
	public function favCount($track_id)
	{
		$result = getResultSet();
		try{
			$search_result = $this->_Dao->select("
				SELECT count(id) AS cnt FROM favtracks WHERE track_id=:tid",
				array("tid" => $track_id,)
			);
			$result["status"] = true;
			$result["data"] = array(
				"count" => $search_result[0]["cnt"],
			);
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

}
