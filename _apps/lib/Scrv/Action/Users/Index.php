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
use lib\Util\Pager as Pager;

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
		$page = Server::get("page", "1");
		$sort = Server::get("sort", "sync_point");
		$order = Server::get("order", "desc");
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		$limit = (int)$this->_common_ini["search"]["limit"];
		$offset = ((int)$page-1) * $limit;

		// sort, order設定
		if ( preg_match("/\A(username|review_count|sync_point)\z/", $sort) !== 1 ) {
			$sort = "sync_point";
		}
		if ( preg_match("/\A(asc|desc)\z/", $order) !== 1 ) {
			$order = "desc";
		}
		// loginしていないのに sync_point が設定されている場合は review_countにする
		if ( ! $this->_is_login && $sort === "sync_point" ) {
			$sort = "review_count";
		}

		// 一覧取得
		$login_user_id = isset($this->_login_user_data["id"]) ? $this->_login_user_data["id"] : null;
		$DaoUsers = new DaoUsers();
		$lists_result = $DaoUsers->lists((int)$offset, (int)$limit, $login_user_id, $sort, $order);
		if ( ! $lists_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}

		$Pager = new Pager();

		$this->_Template->assign(array(
			"sort" => $sort,
			"order" => $order,
			"lists" => $lists_result["data"]["lists"],
			"lists_count" => $lists_result["data"]["lists_count"],
			"pager" => $Pager->getPager((int)$page, $lists_result["data"]["lists_count"], $limit, 5),
		))->display("Users/Index.tpl.php");

		return true;
	}

}
