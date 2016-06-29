<?php
/**
 * /lib/Scrv/Action/Admin/EditUsers.php
 * @author mgng
 */

namespace lib\Scrv\Action\Admin;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;
use lib\Util\Pager as Pager;

/**
 * Edit Users
 * @author mgng
 */
class EditUsers extends Base
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

		// page 設定
		$page = Server::get("page", "1");
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		$limit = (int)self::$_common_ini["search"]["limit"];
		$offset = ((int)$page-1) * $limit;

		// 会員情報を取得
		$DaoUsers = new DaoUsers();
		$lists = $DaoUsers->lists($offset,$limit, null, "id", "asc");
		if ( ! $lists["status"] ) {
			header("Content-type:text/plain; charset=utf-8");
			Server::send404Header(print_r($lists["messages"], true));
			exit;
		}

		// pager リンク用
		$Pager = new Pager();
		$pager = $Pager->getPager((int)$page, $lists["data"]["lists_count"], $limit, 5);
		$_base_url = "{$this->_BasePath}Admin/EditUsers";
		$_base_params = array();
		$most_prev_link = "{$_base_url}/" . hbq2(array_merge($_base_params, array(
			"page" => "1",
		)));
		$prev_link = "{$_base_url}/" . hbq2(array_merge($_base_params, array(
			"page" => $pager["now_page"]-1,
		)));
		$next_link = "{$_base_url}/" . hbq2(array_merge($_base_params, array(
			"page" => $pager["now_page"]+1,
		)));
		$most_next_link = "{$_base_url}/" . hbq2(array_merge($_base_params, array(
			"page" => $pager["max_page"],
		)));
		$nav_list = array();
		foreach($pager["nav_list"] as $nav) {
			$nav_list[] = array(
				"active" => $nav["active"],
				"page" => $nav["page"],
				"link" => "{$_base_url}/" . hbq2(array_merge($_base_params, array(
					"page" => $nav["page"],
				))),
			);
		}

		$this->_Template->assign(array(
			"users" => $lists["data"]["lists"],
			"max_invited_count" => self::$_common_ini["invites"]["max_invited_count"],
			"pager" => $pager,
			"most_prev_link" => $most_prev_link,
			"prev_link" => $prev_link,
			"next_link" => $next_link,
			"most_next_link" => $most_next_link,
			"nav_list" => $nav_list,
		))->display("Admin/EditUsers.tpl.php");
		return true;
	}

}
