<?php
/**
 * lib/Scrv/Dao/Syncs.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Syncs class
 * @author tomita
 */
class Syncs extends Dao
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
	 * syncs albums
	 * @param int $user_id
	 * @param int $login_user_id
	 * @return resultSet
	 * @throws \Exception
	 */
	public function reviews($user_id, $login_user_id)
	{
		$result = getResultSet();
		try{
			// XXX 共通のアルバムレビューを取得…うーん…
			$common_sql = "
				SELECT t1.*, t2.artist, t2.title, t2.img_file, t2.year, t3.username, t3.img_file as user_img_file
				FROM reviews t1
				LEFT JOIN albums t2 ON(t1.album_id=t2.id)
				LEFT JOIN users t3 ON(t1.user_id=t3.id)
			";
			$sql = "
				SELECT * FROM(
					{$common_sql}
					WHERE t1.user_id=:uid1 AND album_id IN(SELECT album_id FROM reviews WHERE user_id=:uid2)
					UNION
					{$common_sql}
					WHERE t1.user_id=:uid3 AND album_id IN(SELECT album_id FROM reviews WHERE user_id=:uid4)
				) u1 ORDER BY u1.album_id, u1.created DESC";
			$params = array(
				"uid1" => $user_id,
				"uid2" => $login_user_id,
				"uid3" => $login_user_id,
				"uid4" => $user_id,
			);
			$merge_lists = $this->_Dao->select($sql, $params);

			// album_idごとにまとめて返す
			$sync_reviews_result = array();
			foreach($merge_lists as $merge_list) {
				$album_id = $merge_list["album_id"];
				if ( ! isset( $sync_reviews_result[$album_id] ) ) {
					$sync_reviews_result[$album_id] = array();
				}
				$sync_reviews_result[$album_id][] = $merge_list;
			}
			$result["status"] = true;
			$result["data"] = $sync_reviews_result;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * sync albums
	 * @param int $user_id
	 * @param int $login_user_id
	 * @return resultSet
	 */
	public function albums($user_id, $login_user_id)
	{
		$result = getResultSet();
		try{
			$sql = "
				SELECT t1.*, t2.artist, t2.title, t2.img_file, t2.year
				FROM favalbums t1
				LEFT JOIN albums t2 ON(t1.album_id=t2.id)
				WHERE t1.user_id=:uid1 AND album_id IN(SELECT album_id FROM favalbums WHERE user_id=:uid2)
				ORDER BY t1.album_id
			";
			$params = array("uid1" => $user_id,"uid2" => $login_user_id,);
			$albums = $this->_Dao->select($sql, $params);
			$result["status"] = true;
			$result["data"] = $albums;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * sync tracks
	 * @param int $user_id
	 * @param int $login_user_id
	 * @return resultSet
	 */
	public function tracks($user_id, $login_user_id)
	{
		$result = getResultSet();
		try{
			$sql = "
				SELECT t1.*, t2.track_title, t2.track_num, t3.id as album_id, t3.artist, t3.title, t3.img_file, t3.year
				FROM favtracks t1
				LEFT JOIN tracks t2 ON(t1.track_id=t2.id)
				LEFT JOIN albums t3 ON(t2.album_id=t3.id)
				WHERE t1.user_id=:uid1 AND track_id IN(SELECT track_id FROM favtracks WHERE user_id=:uid2)
				ORDER BY t2.album_id, t2.track_num
			";
			$params = array("uid1" => $user_id,"uid2" => $login_user_id,);
			$albums = $this->_Dao->select($sql, $params);
			$result["status"] = true;
			$result["data"] = $albums;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

}
