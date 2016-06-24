<?php
/**
 * /lib/Scrv/Action/Admin/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Admin;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;

/**
 * Admin Index
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
		// 未ログインまたは 管理者以外は404で終了
		if ( !$this->_is_login || $this->_login_user_data["role"] !== "admin" ) {
			Server::send404Header("404 not found");
			return false;
		}

		$this->_Template->assign(array())->display("Admin/Index.tpl.php");
		return true;
	}

}
