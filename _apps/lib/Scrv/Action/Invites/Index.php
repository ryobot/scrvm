<?php
/**
 * /lib/Scrv/Action/Users/Invites.php
 * @author mgng
 */

namespace lib\Scrv\Action\Invites;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Invites as DaoInvites;
use lib\Util\Server as Server;
use lib\Util\Password as Password;

/**
 * Invites Index class
 * @author mgng
 */
class Index extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// ログインしていたらエラー
		if($this->_is_login){
			Server::send404Header("404 not found");
			return false;
		}

		$hash = Server::get("hash");
		if ( ! isset($hash) ) {
			Server::send404Header("404 not found.");
			return false;
		}

		// session clear
		$this->_Session->clear(Scrv\SessionKeys::INVITATIONS_DATA);

		// セッション値取得
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// 検索. あればセッションに保持
		$DaoInvites = new DaoInvites();
		$result = $DaoInvites->findHash($hash);
		if ( !$result["status"]){
			Server::send404Header();
			$this->_Template->assign(array())->display("Invites/NotFound.tpl.php");
			return false;
		}
		$this->_Session->set(Scrv\SessionKeys::INVITATIONS_DATA, $result["data"]);

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		$this->_Template->assign(array(
			"data" => $result["data"],
			"error_messages" => $error_messages,
			"token" => $token,
		))->display("Invites/Index.tpl.php");

		return true;
	}

}
