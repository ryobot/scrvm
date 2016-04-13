<?php
/**
 * /lib/Scrv/Action/Users/FavAlbums.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Server as Server;
use lib\Util\Pager as Pager;

/**
 * Users FavAlbums class
 * @author mgng
 */
class FavAlbums extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// id取得
		$user_id = Server::get("id");
		if ( ! isset($user_id) || ! ctype_digit($user_id)) {
			Server::send404Header("404 not found.");
			return false;
		}

		// ユーザ情報取得
		$login_use_id = isset($this->_login_user_data["id"]) ? $this->_login_user_data["id"] : null;
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view((int)$user_id, $login_use_id);
		if ( ! $user_result["status"] || count($user_result["data"]) === 0){
			Server::send404Header("404 not found..");
			return false;
		}

		// offset設定
		$offset = Server::get("offset", "0");
		if ( ! ctype_digit($offset) ) {
			$offset = "0";
		}
		$limit = $this->_common_ini["search"]["limit"];

		// album情報取得
		$DaoAlbums = new DaoAlbums();
		$favalbums_result = $DaoAlbums->favalbumsByUserId((int)$user_id, (int)$offset, (int)$limit);
		if ( !$favalbums_result["status"] ) {
			Server::send404Header("404 not found....");
			return false;
		}

		// pager
		$Pager = new Pager();

		$this->_Template->assign(array(
			"user_id" => (int)$user_id,
			"user" => $user_result["data"],
			"favalbums" => $favalbums_result["data"],
			"pager" => $Pager->getPager($offset, $limit, $user_result["data"]["favalbums_count"]),
		))->display("Users/FavAlbums.tpl.php");
		return true;
	}

}
