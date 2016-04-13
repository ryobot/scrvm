<?php
/**
 * /lib/Scrv/Action/Users/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;

/**
 * Users Index class
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
		// セッションクリア
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);

		// offset設定
		$offset = Server::get("offset", "0");
		if ( ! ctype_digit($offset) ) {
			$offset = "0";
		}
		$limit = $this->_common_ini["search"]["limit"];

		// 一覧取得
		$login_user_id = isset($this->_login_user_data["id"]) ? $this->_login_user_data["id"] : null;
		$DaoUsers = new DaoUsers();
		$lists_result = $DaoUsers->lists((int)$offset, (int)$limit, $login_user_id);
		if ( ! $lists_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}

		$this->_Template->assign(array(
			"lists" => $lists_result["data"],
		))->display("Users/Index.tpl.php");

		return true;
	}

}
