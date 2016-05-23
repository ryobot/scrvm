<?php
/**
 * /lib/Scrv/Action/Users/ConnectTwitter.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;

/**
 * Connect Twitter class
 * @author mgng
 */
class ConnectTwitter extends Base
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

		// twitter連携処理済みか
		$sess_twitter_access_token = $this->_Session->get(Scrv\SessionKeys::TWITTER_ACCESS_TOKEN);

		$this->_Template->assign(array(
			"error_messages" => $error_messages,
			"sess_twitter_access_token" => $sess_twitter_access_token,
		))->display("Users/ConnectTwitter.tpl.php");

		return true;
	}

}
