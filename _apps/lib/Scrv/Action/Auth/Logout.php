<?php
/**
 * /lib/Scrv/Action/Auth/Logout.php
 * @author mgng
 */

namespace lib\Scrv\Action\Auth;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;

/**
 * ログアウト処理クラス
 * @author mgng
 */
class Logout extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		if ( Server::env("REQUEST_METHOD") !== "POST" ) {
			Server::send404Header();
			return false;
		}

		$this->_Session->destroy();
		Server::redirect($this->_BasePath);
		return true;
	}

}
