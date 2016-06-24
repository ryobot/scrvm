<?php
/**
 * /lib/Scrv/Action/Admin/EditUsers.php
 * @author mgng
 */

namespace lib\Scrv\Action\Admin;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;

/**
 * Edit Users
 * @author mgng
 */
class EditUsers extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインまたは 管理者以外は404で終了
		if ( !$this->_is_login || $this->_login_user_data["role"] !== "admin" ) {
			Server::send404Header("404 not found");
			return false;
		}

		// 会員情報を取得
		$DaoUsers = new DaoUsers();
		$lists = $DaoUsers->lists(0,100, null, "id", "asc");
		if ( ! $lists["status"] ) {
			header("Content-type:text/plain; charset=utf-8");
			Server::send404Header(print_r($lists["messages"], true));
			exit;
		}

		$this->_Template->assign(array(
			"users" => $lists["data"]["lists"],
			"max_invited_count" => self::$_common_ini["invites"]["max_invited_count"],
		))->display("Admin/EditUsers.tpl.php");
		return true;
	}

}
