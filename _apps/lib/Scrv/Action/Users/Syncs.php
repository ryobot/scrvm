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

		// ユーザ情報取得
		$login_user_id = $this->_login_user_data["id"];
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view((int)$user_id, $login_user_id);
		if ( ! $user_result["status"] || count($user_result["data"]) === 0){
			Server::send404Header("404 not found..");
			return false;
		}

		// TODO sync一覧取得
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

		$syncs = array(
			"reviews" => $sync_reviews_result["data"],
			"albums" => $sync_albums_result["data"],
			"tracks" => $sync_tracks_result["data"],
		);

		$this->_Template->assign(array(
			"user_id" => (int)$user_id,
			"user" => $user_result["data"],
			"syncs" => $syncs,
		))->display("Users/Syncs.tpl.php");
		return true;
	}

}
