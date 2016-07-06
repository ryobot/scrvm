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
	public function get($user_id, $type, $start)
	{
		$result = getResultSet();
		try{
			$list = array();
			if ( $type === "reviews" ) {
				$list = $this->_reviews($user_id, $start);
			}
			$result["status"] = true;
			$result["data"]["type"] = $type;
			$result["data"]["list"] = $list;
		} catch( \PDOException $e ) {
			$result["messages"][] = "db error - " . $e->getMessage();
		}
		return $result;
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

}
