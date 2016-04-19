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

		// XXX pointの計算...
		$UtilSyncs = new UtilSyncs();
		foreach($sync_reviews_result["data"] as $album_id => &$reviews){
			$own_review = null;
			$you_review = null;
			$you_review_idx = 0;
			foreach( $reviews as $idx => $review ) {
				// 自分のIDの最古レビューを格納(上書き)
				// 相手のIDの最新レビューを格納(あればスルー)
				if ( $login_user_id === $review["user_id"] ) {
					$own_review = $review;
				} else {
					if ( $you_review === null ) {
						$you_review = $review;
						$you_review_idx = $idx;
					}
				}
			}
			// calc sync point
			$sync_point = $UtilSyncs->calcPoint($own_review["created"], $you_review["created"]);
			if ($sync_point["point"] > 0 ) {
				$reviews[$you_review_idx]["sync_point"] = $sync_point;
			}
		} unset($reviews);

		// XXX 自分と相手のidだけ格納...
		$tpl_reviews = array();
		$syncs_reviews_point_total = 0;
		foreach($sync_reviews_result["data"] as $album_id => &$reviews){
			$tpl_reviews[$album_id] = array("point" => 0, "data" => array());
			foreach( $reviews as $idx => $review ) {
				if ( in_array($review["user_id"], array($login_user_id, $user_id), true) ) {
					$syncs_point = isset($review["sync_point"]) ? $review["sync_point"]["point"] : 0;
					$tpl_reviews[$album_id]["data"][] = $review;
					$tpl_reviews[$album_id]["point"] += $syncs_point;
					$syncs_reviews_point_total += $syncs_point;
				}
			}
			if ($tpl_reviews[$album_id]["point"] === 0){
				unset($tpl_reviews[$album_id]);
			}
		}

		$syncs = array(
			"reviews" => $tpl_reviews,
			"albums" => $sync_albums_result["data"],
			"tracks" => $sync_tracks_result["data"],
			"albums_point" => count($sync_albums_result["data"]) * 5,
			"tracks_point" => count($sync_tracks_result["data"]) * 2,
		);

		$this->_Template->assign(array(
			"user_id" => (int)$user_id,
			"user" => $user_result["data"],
			"syncs" => $syncs,
			"syncs_reviews_point_total" => $syncs_reviews_point_total,
		))->display("Users/Syncs.tpl.php");
		return true;
	}

}
