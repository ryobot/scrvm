<?php
/**
 * /lib/Scrv/Action/Users/Chart.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Scrv\Dao\Users\Chart as DaoUsersChart;
use lib\Util\Server as Server;

/**
 * Users Chart class
 * @author mgng
 */
class Chart extends Base
{
	/**
	 * 自分の投稿数の日別チャートなど表示
	 * @return boolean
	 */
	public function run()
	{
		// セッションクリア
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);

		$user_id = Server::get("id", "");
		$start = Server::get("start", "0");
		$is_json = Server::get("json");
		if ( !ctype_digit($user_id)
			|| !ctype_digit($start) || (int)$start < 0
		) {
			Server::send404Header("404 not found.");
			return false;
		}

		// ユーザ情報取得
		$login_use_id = isset($this->_login_user_data["id"]) ? $this->_login_user_data["id"] : null;
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view((int)$user_id, $login_use_id);
		if ( ! $user_result["status"] || count($user_result["data"]) === 0){
			Server::send404Header("404 not found.");
			return false;
		}

		// 一覧取得
		$DaoUsersChart = new DaoUsersChart();
		$lists_result = $DaoUsersChart->get($user_id, (int)$start);
		if ( ! $lists_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}

		$now = self::$_nowTimestamp;
		$start_dt = new \DateTime(date("Y-m-d", $now));
		$end_dt = new \DateTime(date("Y-m-d", $now));
		$_start = $start_dt->sub(new \DateInterval('P'.((int)$start).'M'))->getTimestamp();
		$_end = $end_dt->sub(new \DateInterval('P'.(1+(int)$start).'M'))->getTimestamp();

		$params = array(
			"user_id" => (int)$user_id,
			"user" => $user_result["data"],
			"from" => date("Y年n月j日", $_end),
			"to" => date("Y年n月j日", $_start),
			"chart_data" => array(
				"reviews_artist" => $this->_makeReviewsArtist($lists_result["data"]["reviews_artist"]),
				"reviews" => $this->_makeReviews($lists_result["data"]["reviews"], $_start, $_end),
				"reviews_hourly" => $this->_makeReviewsHourly($lists_result["data"]["reviews_hourly"]),
			),
		);

		if ( $is_json === "1" ) {
			header("Content-Type:application/json; charset=UTF-8");
			echo json_encode($params);
			return true;
		}

		$this->_Template->assign($params)->display("Users/Chart.tpl.php");
		return true;
	}

	private function _makeReviewsArtist($data_list)
	{
		$labels = array();
		$_data = array();
		foreach($data_list as $row) {
			$labels[] = $row["artist"];
			$_data[] = $row["count"];
		}
		return array(
			"labels" => $labels,
			"datasets" => array(
				array(
					"label" => "reviews artist",
					"data" => $_data,
				),
			),
		);
	}

	private function _makeReviews($data, $start, $end)
	{
		$date_list = array();
		for( $i=$end; $i<=$start; $i += 86400 ){
			$date_list[date("Y-m-d", $i)] = array();
		}
		foreach($data as $list) {
			if (array_key_exists($list["date"], $date_list) ) {
				$date_list[$list["date"]] = $list;
			}
		}

		$labels = array();
		$_data = array();
		foreach($date_list as $date => $row) {
			$labels[] = date("n月j日", strtotime($date));
			$_data[] = isset($row["count"]) ? $row["count"] : 0;
		}
		return array(
			"labels" => $labels,
			"datasets" => array(
				array(
					"label" => "reviews",
					"data" => $_data,
				),
			),
		);
	}

	private function _makeReviewsHourly($data_list)
	{
		// 0から23までの配列を作成、マージ
		$hour_list = array();
		for($i=0;$i<=23;$i++){
			$hour_list[$i] = array("hour" => "{$i}", "count" => 0);
		}
		foreach($data_list as $list){
			if ( isset( $hour_list[(int)$list["hour"]] ) ) {
				$hour_list[(int)$list["hour"]] = $list;
			}
		}

		$labels = array();
		$_data = array();
		foreach($hour_list as $hour => $row) {
			$labels[] = "{$hour}時";
			$_data[] = $row["count"];
		}
		return array(
			"labels" => $labels,
			"datasets" => array(
				array(
					"label" => "reviews hourely",
					"data" => $_data,
				),
			),
		);
	}

}
