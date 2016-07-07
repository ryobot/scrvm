<?php
/**
 * lib/Scrv/Dao/Users/Chart.php
 * @author mgng
 */

namespace lib\Scrv\Dao\Users;
use lib\Scrv\Dao\Base as Dao;

/**
 * chart class
 * @author tomita
 */
class Chart extends Dao
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
	 *
	 * @param int $user_id
	 * @param string $type
	 * @param int $start
	 * @return string
	 */
	public function get($user_id, $start)
	{
		$result = getResultSet();
		try{
			$list = array(
				"reviews_artist" => $this->_reviewsArtist($user_id),
				"reviews" => $this->_reviews($user_id, $start),
				"reviews_hourly" => $this->_reviewsHourly($user_id, $start),
			);
			$result["status"] = true;
			$result["data"] = $list;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
	}

	/**
	 * top 15 artist chart
	 * @param int $user_id
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 */
	private function _reviewsArtist($user_id, $offset = 0, $limit = 15)
	{
		return $this->_Dao->select("
			SELECT t2.artist, count(t1.id) AS count
			FROM reviews t1
			INNER JOIN albums t2 ON (t1.album_id=t2.id)
			WHERE t1.user_id=:uid
			GROUP BY artist
			ORDER BY count DESC, t1.created
			LIMIT :offset, :limit",
			array(
				"uid" => $user_id,
				"offset" => $offset,
				"limit" => $limit,
			)
		);
	}

	/**
	 * 1ヵ月間のレビュー件数を取得する
	 * @param int $user_id
	 * @param int $start default 0
	 * @return string
	 */
	private function _reviews($user_id, $start = 0)
	{
		return $this->_Dao->select("
			SELECT
				count(id) AS count,
				DATE_FORMAT(created, '%Y-%m-%d') AS date,
				unix_timestamp(created) AS timestamp
			FROM reviews
			WHERE user_id=:uid
				AND created BETWEEN
					(from_unixtime(:now1) - INTERVAL :itv1 MONTH) AND
					(from_unixtime(:now2) - INTERVAL :itv2 MONTH)
			GROUP BY date
			ORDER BY created ASC",
		array(
			"uid" => $user_id,
			"now1" => self::$_nowTimestamp,
			"now2" => self::$_nowTimestamp,
			"itv1" => $start + 1,
			"itv2" => $start,
		));
	}

	/**
	 * 1ヵ月間のレビュー投稿の時間帯を取得する
	 * @param int $user_id
	 * @param int $start default 0
	 * @return string
	 */
	private function _reviewsHourly($user_id, $start = 0)
	{
		return $this->_Dao->select("
			SELECT
				DATE_FORMAT(created, '%k') AS hour,
				count(id) as count
			FROM reviews
			WHERE user_id=:uid
				AND created BETWEEN
					(from_unixtime(:now1) - INTERVAL :itv1 MONTH) AND
					(from_unixtime(:now2) - INTERVAL :itv2 MONTH)
			GROUP BY hour
			ORDER BY hour ASC",
		array(
			"uid" => $user_id,
			"now1" => self::$_nowTimestamp,
			"now2" => self::$_nowTimestamp,
			"itv1" => $start + 1,
			"itv2" => $start,
		));
	}



}
