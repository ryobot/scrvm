<?php
/**
 * /lib/Scrv/Action/Albums/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Server as Server;
use lib\Util\Pager as Pager;

/**
 * albums Index class
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
		// 各パラメータ取得
		$stype = mb_trim(Server::get("stype", ""));
		$type = mb_trim(Server::get("type", ""));
		$q = mb_trim(Server::get("q", ""));
		$index = mb_trim(Server::get("index", ""));
		$sort = Server::get("sort", "reviews"); // defalut reviews
		$order = Server::get("order", "desc");  // default desc
		$page = Server::get("page", "1");
		$ajax = Server::get("ajax", "");

		// page 設定
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		$limit = (int)self::$_common_ini["search"]["limit"];
		$offset = ((int)$page-1) * $limit;

		// sort, order設定
		if ( preg_match("/\A(artist|title|year|reviews)\z/", $sort) !== 1 ) {
			$sort = "reviews";
		}
		if ( preg_match("/\A(asc|desc)\z/", $order) !== 1 ) {
			$order = "desc";
		}

		// stype check
		if ( preg_match("/\A(search|index)\z/", $stype) !== 1 ) {
			$stype = "search";
		}
		// type check
		if ( preg_match("/\A(artist|title)\z/", $type) !== 1 ) {
			$type = "artist";
		}
		// index check
		if (preg_match("/\A[a-z0-9日]\z/ui", $index) !== 1 ) {
			$index = "";
		}

		// album一覧取得
		$DaoAlbums = new DaoAlbums();
		$albums_result = $DaoAlbums->lists($offset, $limit, $stype, $type, $q, $index, $sort, $order);
		if ( ! $albums_result["status"] ) {
			Server::send404Header("not found.");
			return false;
		}

		// pager
		$Pager = new Pager();
		$pager = $Pager->getPager((int)$page, $albums_result["data"]["lists_count"], $limit, 5);

		// pager リンク用
		$_base_url = "{$this->_BasePath}Albums";
		$_base_params = array(
			"q" => $q,
			"type" => $type,
			"stype" => $stype,
			"index" => $index,
		);
		$most_prev_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"page" => "1",
			"sort"   => $sort,
			"order"  => $order,
		)));
		$prev_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"page" => $pager["now_page"]-1,
			"sort"   => $sort,
			"order"  => $order,
		)));
		$next_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"page" => $pager["now_page"]+1,
			"sort"   => $sort,
			"order"  => $order,
		)));
		$most_next_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"page" => $pager["max_page"],
			"sort"   => $sort,
			"order"  => $order,
		)));
		$nav_list = array();
		foreach($pager["nav_list"] as $nav) {
			$nav_list[] = array(
				"active" => $nav["active"],
				"page" => $nav["page"],
				"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
					"page" => $nav["page"],
					"sort"   => $sort,
					"order"  => $order,
				))),
			);
		}

		// ソート用リンク
		$order_type = $order === "asc" ? "desc" : "asc";
		$sort_links = array(
			"reviews" => array(
				"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
					"sort"   => "reviews",
					"order"  => $order_type,
				))),
				"text" => $sort === "reviews" ? "[Reviews]" : "Reviews",
			),
			"artist" => array(
				"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
					"sort"   => "artist",
					"order"  => $order_type,
				))),
				"text" => $sort === "artist" ? "[Artist]" : "Artist",
			),
			"title" => array(
				"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
					"sort"   => "title",
					"order"  => $order_type,
				))),
				"text" => $sort === "title" ? "[Title]" : "Title",
			),
			"year" => array(
				"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
					"sort"   => "year",
					"order"  => $order_type,
				))),
				"text" => $sort === "year" ? "[Year]" : "Year",
			),
		);

		$assign_data = array(
			"q" => $q,
			"type" => $type,
			"index" => $index,
			"stype" => $stype,
			"sort" => $sort,
			"order" => $order,
			"lists" => $albums_result["data"]["lists"],
			"pager" => $pager,
			"most_prev_link" => $most_prev_link,
			"prev_link" => $prev_link,
			"next_link" => $next_link,
			"most_next_link" => $most_next_link,
			"nav_list" => $nav_list,
			"sort_links" => $sort_links,
		);

		// ajaxの場合
		if ($ajax === "1") {
			header("Content-Type:text/plain; charset=utf-8");
			$this->_Template->assign($assign_data)->display("Albums/Index_Ajax.tpl.php");
			return true;
		}

		$this->_Template->assign($assign_data)->display("Albums/Index.tpl.php");
		return true;
	}
}
