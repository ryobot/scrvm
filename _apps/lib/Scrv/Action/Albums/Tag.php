<?php
/**
 * /lib/Scrv/Action/Albums/Tag.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Pager as Pager;

/**
 * albums tag class
 * @author mgng
 */
class Tag extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$tag = Server::get("tag");
		if ( ! isset($tag) || ! $tag === "" ) {
			Server::send404Header("not found.");
			return false;
		}

		// 各パラメータ取得
		$page = Server::get("page", "1");
		$artist = mb_trim(Server::get("artist", ""));
		$sort = Server::get("sort", "artist");
		$order = Server::get("order", "asc");
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		$limit = (int)self::$_common_ini["search"]["limit"];
		$offset = ((int)$page-1) * $limit;
		// sort, order設定
		if ( preg_match("/\A(artist|title|year)\z/", $sort) !== 1 ) {
			$sort = "artist";
		}
		if ( preg_match("/\A(asc|desc)\z/", $order) !== 1 ) {
			$order = "asc";
		}

		// tagによるalbum情報取得
		$DaoAlbums = new DaoAlbums();
		$albums_result = $DaoAlbums->tag($tag,$artist, $sort, $order, $offset, $limit);
		if (!$albums_result["status"]) {
			Server::send404Header("not found..");
			print_r($albums_result);
			return false;
		}

		$Pager = new Pager();
		$pager = $Pager->getPager((int)$page, $albums_result["data"]["lists_count"], $limit, 5);

		// pager 関連
		$_base_url = "{$this->_BasePath}Albums/Tag";
		$page_params = array(
			"tag" => $tag,
			"artist" => $artist,
			"sort"   => $sort,
			"order"  => $order,
		);
		$most_prev_link = "{$_base_url}?" . hbq(array_merge($page_params, array("page" => "1")));
		$prev_link      = "{$_base_url}?" . hbq(array_merge($page_params, array("page" => $pager["now_page"]-1)));
		$next_link      = "{$_base_url}?" . hbq(array_merge($page_params, array("page" => $pager["now_page"]+1)));
		$most_next_link = "{$_base_url}?" . hbq(array_merge($page_params, array("page" => $pager["max_page"])));
		$nav_list = array();
		foreach($pager["nav_list"] as $nav) {
			$nav_list[] = array(
				"active" => $nav["active"],
				"page" => $nav["page"],
				"link" => "{$_base_url}?" . hbq(array_merge($page_params, array("page" => $nav["page"]))),
			);
		}

		// ソート用リンク
		$order_type = $order === "asc" ? "desc" : "asc";
		$sort_params = array(
			"tag" => $tag,
			"artist" => $artist,
			"order"  => $order_type,
		);
		$sort_links = array(
			"artist" => array(
				"link" => "{$_base_url}?" . hbq(array_merge($sort_params,array("sort"=>"artist"))),
				"text" => $sort === "artist" ? "[Artist]" : "Artist",
			),
			"title" => array(
				"link" => "{$_base_url}?" . hbq(array_merge($sort_params,array("sort"=>"title"))),
				"text" => $sort === "title" ? "[Title]" : "Title",
			),
			"year" => array(
				"link" => "{$_base_url}?" . hbq(array_merge($sort_params,array("sort"=>"year"))),
				"text" => $sort === "year" ? "[Year]" : "Year",
			),
		);

		$this->_Template->assign(array(
			"tag" => $tag,
			"artist" => $artist,
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
		))->display("Albums/Tag.tpl.php");
		return true;
	}
}
