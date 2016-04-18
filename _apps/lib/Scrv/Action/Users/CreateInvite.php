<?php
/**
 * /lib/Scrv/Action/Users/CreateInvite.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Util\Password as Password;

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

		$this->_Template->assign(array(
			"token" => $token,
		))->display("Users/CreateInvite.tpl.php");

		return true;
	}

}
