<?php
/**
 * /lib/Scrv/Action/Users/DisconnectTwitter.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;

/**
 * Disconnect Twitter class
 * @author mgng
 */
class DisconnectTwitter extends Base
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

		// ユーザのtwitter情報をクリア
		$current_user_result = $DaoUsers->clearTwitter($this->_login_user_data["id"]);
		if ( !$current_user_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $current_user_result["messages"]);
		} else {
			$this->_Session->regenerate();
			$this->_Session->set(Scrv\SessionKeys::LOGIN_USER_DATA, $current_user_result["data"]["user_data"]);
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, array("twitter連携をクリアしました。"));
		}
		Server::redirect($this->_BasePath . "Users/ConnectTwitter");
		return true;
	}

}
