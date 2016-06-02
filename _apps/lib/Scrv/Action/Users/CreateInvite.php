<?php
/**
 * /lib/Scrv/Action/Users/CreateInvite.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Password as Password;
use lib\Util\Server as Server;


/**
 * CreateInvite class
 * @author mgng
 */
class CreateInvite extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインの場合はログイン画面にリダイレクト
		$this->isNotLogined($this->_BasePath . "Auth");

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		// 招待可能数はセッションではなくDBから取得
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view($this->_login_user_data["id"]);
		if ( ! $user_result["status"] ) {
			Server::send404Header("not found.");
			return false;
		}
		$invited_count = $user_result["data"]["invited_count"];

		$this->_Template->assign(array(
			"token" => $token,
			"can_be_invited_count" => (int)self::$_common_ini["invites"]["max_invited_count"] - $invited_count,
		))->display("Users/CreateInvite.tpl.php");

		return true;
	}

}
