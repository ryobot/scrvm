<?php
/**
 * /lib/Scrv/Action/Users/Add.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Util\Password as Password;
use lib\Util\Server as Server;

/**
 * Users Add class
 * @author mgng
 */
class Add extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
//		// セッション値取得
//		$post_params = $this->_Session->get(Scrv\SessionKeys::POST_PARAMS);
//		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
//		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);
//
//		// token生成、セッションに保持
//		$Password = new Password();
//		$token = $Password->makeRandomHash($this->_Session->id());
//		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);
//
//		$this->_Template->assign(array(
//			"post_params" => $post_params,
//			"error_messages" => $error_messages,
//			"token" => $token,
//		))->display("Users/Add.tpl.php");
//
		Server::send404Header("404 not found.");
		return true;
	}

}
