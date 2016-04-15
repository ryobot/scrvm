<?php
/**
 * /lib/Scrv/Action/Users/EditPassword.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;
use lib\Util\Password as Password;

/**
 * Users Edit class
 * @author mgng
 */
class EditPassword extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインの場合はログイン画面にリダイレクト
		$this->isNotLogined($this->_BasePath . "Auth");

		// ユーザ情報取得
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view($this->_login_user_data["id"]);
		if ( ! $user_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}

		// セッション値取得
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		$this->_Template->assign(array(
			"error_messages" => $error_messages,
			"token" => $token,
		))->display("Users/EditPassword.tpl.php");

		return true;
	}

}
