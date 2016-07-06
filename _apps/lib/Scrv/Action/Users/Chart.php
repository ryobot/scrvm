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
		$type = Server::get("type", "reviews");
		$start = Server::get("start", "0");
		$is_json = Server::get("json");
		if ( !ctype_digit($user_id)
			|| preg_match("/\A(reviews|)\z/", $type) !== 1
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
		$lists_result = $DaoUsersChart->get($user_id, $type, (int)$start);
		if ( ! $lists_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}

		if ( $is_json === "1" ) {
			header("Content-Type:application/json; charset=UTF-8");
			echo json_encode($lists_result);
			return true;
		}

		// start- end の配列を作成
		$now = self::$_nowTimestamp;
		$start_dt = new \DateTime(date("Y-m-d", $now));
		$end_dt = new \DateTime(date("Y-m-d", $now));
		$start_dt->sub(new \DateInterval('P'.((int)$start).'M'));
		$end_dt->sub(new \DateInterval('P'.(1+(int)$start).'M'));
		$_start = $start_dt->getTimestamp();
		$_end = $end_dt->getTimestamp();
		$date_list = array();
		for( $i=$_end; $i<=$_start; $i += 60*60*24 ){
			$date_list[date("Y-m-d", $i)] = array();
		}

		foreach($lists_result["data"]["list"] as $list) {
			if (array_key_exists($list["date"], $date_list) ) {
				$date_list[$list["date"]] = $list;
			}
		}

//		// Etag用ハッシュ取得
//		$etag = $this->_Template->getEtag(print_r($lists_result, 1));
//		// キャッシュヘッダとETagヘッダ出力
//		header("Cache-Control: max-age=60");
//		header("ETag: {$etag}");
//		// etagが同じなら304
//		$client_etag = Server::env("HTTP_IF_NONE_MATCH");
//		if ( $etag ===  $client_etag) {
//			header( 'HTTP', true, 304 );
//			return true;
//		}

		$this->_Template->assign(array(
			"user_id" => (int)$user_id,
			"user" => $user_result["data"],
			"from" => date("Y年n月j日", $_end),
			"to" => date("Y年n月j日", $_start),
			"type" => $type,
			"list" => $date_list,
			"chartjs_json_data" => $this->_convertChartJsJsonData($date_list, $type),
		))->display("Users/Chart.tpl.php");

		return true;
	}

	private function _convertChartJsJsonData($data_list, $type)
	{
		$labels = array();
		$_data = array();
		foreach($data_list as $key => $row) {
			$labels[] = date("n月j日", strtotime($key));
			$_data[] = isset($row["count"]) ? $row["count"] : 0;
		}
		return array(
			"labels" => $labels,
			"datasets" => array(
				array(
					"label" => $type,
					"data" => $_data,
				),
			),
		);
	}

}
