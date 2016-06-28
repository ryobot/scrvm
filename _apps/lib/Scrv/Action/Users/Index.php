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
		$limit = (int)self::$_common_ini["search"]["limit"];
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

		// Etag用ハッシュ取得
		$etag = $this->_Template->getEtag(print_r($lists_result, 1));
		// キャッシュヘッダとETagヘッダ出力
		header("Cache-Control: max-age=60");
		header("ETag: {$etag}");
		// etagが同じなら304
		$client_etag = Server::env("HTTP_IF_NONE_MATCH");
		if ( $etag ===  $client_etag) {
			header( 'HTTP', true, 304 );
			return true;
		}

		// pager関連
		$Pager = new Pager();
		$pager = $Pager->getPager((int)$page, $lists_result["data"]["lists_count"], $limit, 5);

		$_base_url = $this->_BasePath . "Users/Index";
		$most_prev_link = "{$_base_url}/" . hbq2(array(
			"page"  => "1",
			"sort"  => $sort,
			"order" => $order,
		));
		$prev_link = "{$_base_url}/" . hbq2(array(
			"page"  => $pager["now_page"]-1,
			"sort"  => $sort,
			"order" => $order,
		));
		$next_link = "{$_base_url}/" . hbq2(array(
			"page"  => $pager["now_page"]+1,
			"sort"  => $sort,
			"order" => $order,
		));
		$most_next_link = "{$_base_url}/" . hbq2(array(
			"page"  => $pager["max_page"],
			"sort"  => $sort,
			"order" => $order,
		));
		$nav_list = array();
		foreach($pager["nav_list"] as $nav) {
			$nav_list[] = array(
				"active" => $nav["active"],
				"page" => $nav["page"],
				"link" => "{$_base_url}/" . hbq2(array(
					"page" => $nav["page"],
					"sort"   => $sort,
					"order"  => $order,
				)),
			);
		}

		// ソート用リンク
		$order_type = $order === "asc" ? "desc" : "asc";
		$sort_links = array(
			"username" => array(
				"link" => "{$_base_url}/" . hbq2(array(
					"sort"   => "username",
					"order"  => $order_type,
				)),
				"text" => $sort === "username" ? "[Name]" : "Name",
			),
			"review_count" => array(
				"link" => "{$_base_url}/" . hbq2(array(
					"sort"   => "review_count",
					"order"  => $order_type,
				)),
				"text" => $sort === "review_count" ? "[Reviews]" : "Reviews",
			),
			"sync_point" => array(
				"link" => "{$_base_url}/" . hbq2(array(
					"sort"   => "sync_point",
					"order"  => $order_type,
				)),
				"text" => $sort === "sync_point" ? "[SyncPoint]" : "SyncPoint",
			),
		);

		$this->_Template->assign(array(
			"sort" => $sort,
			"order" => $order,
			"lists" => $lists_result["data"]["lists"],
			"lists_count" => $lists_result["data"]["lists_count"],
			"pager" => $pager,
			"most_prev_link" => $most_prev_link,
			"prev_link" => $prev_link,
			"next_link" => $next_link,
			"most_next_link" => $most_next_link,
			"nav_list" => $nav_list,
			"order_type" => $order_type,
			"sort_links" => $sort_links,
		))->display("Users/Index.tpl.php");

		return true;
	}

}
