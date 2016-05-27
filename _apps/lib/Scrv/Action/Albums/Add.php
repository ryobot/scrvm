<?php
/**
 * /lib/Scrv/Action/Albums/Add.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;

/**
 * albums add class
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
		// 未ログインはログイン画面へ
		$this->isNotLogined($this->_BasePath . "Auth");

		$type = Server::get("type", "");
		$q = Server::get("q", "");
		if ( preg_match("/\A(artist|title)\z/", $type) !== 1 ) {
			$q = "";
			$type = "";
		}

		$this->_Template->assign(array(
			"type" => $type,
			"q" => $q,
		))->display("Albums/Add.tpl.php");
		return true;
	}
}
