<?php
/**
 * lib/Scrv/Dao/Activity.php
 * @author mgng
 */

namespace lib\Scrv\Dao;
use lib\Scrv\Dao\Base as Dao;

/**
 * Activity class
 * @author tomita
 */
class Activity extends Dao
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
	 * 件数取得
	 * @return resultSet
	 */
	public function getCount()
	{
		$result = getResultSet();
		try{
			$count_list = $this->_Dao->select("SELECT
				(SELECT count(*) FROM reviews) AS Reviews,
				(SELECT count(*) FROM albums) AS Albums,
				(SELECT count(*) FROM users) AS Users,
				(SELECT count(*) FROM posts) AS Posts
			");
			$result["status"] = true;
			$result["data"] = $count_list[0];
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * fav reviews lists
	 * @return string
	 */
	public function getLists($day = 1)
	{
		$result = getResultSet();
		try{
			// 取得する期間(日数)
			$favreviews = $this->_favReviews($day);
			$favtracks = $this->_favTracks($day);
			$favalbums = $this->_favAlbums($day);
			$users = $this->_users($day);

			$result["status"] = true;
			$result["data"] = array_merge($favreviews, $favtracks, $favalbums, $users);
		} catch( \Exception $ex ) {
			$result["messages"][] = $ex->getMessage();
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	private function _getDateBetween($day)
	{
		return array(
			"day1" => date("Y-m-d H:i:s", self::$_nowTimestamp + (($day-1)*60*60*24) ),
			"day2" => date("Y-m-d H:i:s", self::$_nowTimestamp + (($day)*60*60*24) - 1 ),
		);
	}

	/**
	 * fav reviews list
	 * @return array
	 */
	private function _favReviews($day)
	{
		return $this->_Dao->select("
			SELECT
				'fav_reviews' as action,
				t1.id AS review_id,
				t4.id AS user_id,
				t4.username AS username,
				t3.artist,
				t3.title,
				t5.id AS faved_user_id,
				t5.username AS faved_username,
				t5.img_file,
				t2.created
			FROM reviews t1
			INNER JOIN favreviews t2 ON(t1.id=t2.review_id)
			INNER JOIN albums t3 ON (t1.album_id=t3.id)
			INNER JOIN users t4 ON (t1.user_id=t4.id)
			INNER JOIN users t5 ON (t2.user_id=t5.id)
			WHERE t2.created > (now() - interval :day day)
			ORDER BY t2.created DESC",
			array("day" => $day),
			array(
				"enabled" => true,
				"expire" => 5,
				"index" => "Activity_fav_reviews",
			)
		);
	}

	/**
	 * fav tracks list
	 * @return array
	 */
	private function _favTracks($day)
	{
		return $this->_Dao->select("
			SELECT
				'fav_tracks' as action,
				t1.album_id,
				t1.artist,
				t3.title,
				t1.track_num,
				t1.track_title,
				t4.id as faved_user_id,
				t4.username as faved_username,
				t4.img_file,
				t2.created
			from tracks t1
			INNER JOIN favtracks t2 ON(t1.id=t2.track_id)
			INNER JOIN albums t3 ON (t1.album_id=t3.id)
			INNER JOIN users t4 ON (t2.user_id=t4.id)
			WHERE t2.created > (now() - interval :day day)
			ORDER BY t2.created DESC",
			array("day" => $day),
			array(
				"enabled" => true,
				"expire" => 5,
				"index" => "Activity_fav_tracks",
			)
		);
	}

	/**
	 * fav albums list
	 * @return array
	 */
	private function _favAlbums($day)
	{
		return $this->_Dao->select("
			SELECT
				'fav_albums' as action,
				t1.id AS album_id,
				t1.artist,
				t1.title,
				t4.id AS faved_user_id,
				t4.username AS faved_username,
				t4.img_file,
				t2.created
			from albums t1
			INNER JOIN favalbums t2 ON(t1.id=t2.album_id)
			INNER JOIN users t4 ON (t2.user_id=t4.id)
			WHERE t2.created > (now() - interval :day day)
			ORDER BY t2.created DESC",
			array("day" => $day),
			array(
				"enabled" => true,
				"expire" => 5,
				"index" => "Activity_fav_albums",
			)
		);
	}

	/**
	 * fav albums list
	 * @return array
	 */
	private function _users($day)
	{
		return $this->_Dao->select("
			select
				'new_user' as action,
				t1.*
			FROM users t1
			WHERE t1.created > (now() - interval :day day)
			ORDER BY created DESC",
			array("day" => $day),
			array(
				"enabled" => true,
				"expire" => 5,
				"index" => "Activity_users",
			)
		);
	}

}
