<?php
/**
 * /lib/Scrv/Action/Users/Syncs.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Scrv\Dao\Syncs as DaoSyncs;
use lib\Util\Server as Server;
use lib\Util\Syncs as UtilSyncs;

/**
 * Users Syncs class
 * @author mgng
 */
class Syncs extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログイン時はエラー
		if ( ! $this->_is_login ) {
			Server::send404Header("404 not found.");
			return false;
		}

		// id取得
		$user_id = Server::get("id");
		if ( ! isset($user_id) || ! ctype_digit($user_id)) {
			Server::send404Header("404 not found.");
			return false;
		}
		$user_id = (int)$user_id;

		// 自分のsyncsは表示できない
		if ($user_id === $this->_login_user_data["id"]) {
			Server::send404Header("404 not found..");
			return false;
		}

		// ユーザ情報取得
		$login_user_id = $this->_login_user_data["id"];
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view($user_id, $login_user_id);
		if ( ! $user_result["status"] || count($user_result["data"]) === 0){
			Server::send404Header("404 not found..");
			return false;
		}

		// sync一覧取得
		$DaoSyncs = new DaoSyncs();
		$sync_artists_result = $DaoSyncs->artists($user_id, $login_user_id);
		if ( !$sync_artists_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}
		$sync_reviews_result = $DaoSyncs->reviews($user_id, $login_user_id);
		if ( !$sync_reviews_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}
		$sync_albums_result = $DaoSyncs->albums($user_id, $login_user_id);
		if ( !$sync_albums_result["status"] ) {
			Server::send404Header("db error..");
			return false;
		}
		$sync_tracks_result = $DaoSyncs->tracks($user_id, $login_user_id);
		if ( !$sync_tracks_result["status"] ) {
			Server::send404Header("db error...");
			return false;
		}

		$UtilSyncs = new UtilSyncs();

		// 計算実行。地獄…
		$reviews_list = $sync_reviews_result["data"];
		$reviews_with_point = array();
		$syncs_reviews_point_total = 0;
		foreach($reviews_list as $album_id => $reviews){
			for($i=0,$len=count($reviews);$i<$len;$i++){
				$current = $reviews[$i];
				$next = isset($reviews[$i+1]) ? $reviews[$i+1] : null;
				if ($next === null) {
					continue;
				}
				$calc = $UtilSyncs->calcReviewsPoint(array($current, $next));
				$next["sync_point"] = $calc[0]["sync"];
				$syncs_reviews_point_total += $calc[0]["sync"]["point"];
				$reviews_with_point[$album_id] = array(
					"point" => $calc[0]["sync"]["point"],
					"diff" => $calc[0]["sync"]["diff"],
					"data" => array(
						$current,
						$next,
					),
				);
			}
		}

		$this->_Template->assign(array(
			"user_id" => (int)$user_id,
			"user" => $user_result["data"],
			"syncs_reviews_point_total" => $syncs_reviews_point_total,
			"syncs" => array(
				"artists" => $sync_artists_result["data"],
				"reviews" => $reviews_with_point,
				"albums" => $sync_albums_result["data"],
				"tracks" => $sync_tracks_result["data"],
				"albums_point" => count($sync_albums_result["data"]) * 5,
				"tracks_point" => count($sync_tracks_result["data"]) * 2,
			),
		))->display("Users/Syncs.tpl.php");
		return true;
	}

}
