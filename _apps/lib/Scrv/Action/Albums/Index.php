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

		// page 設定
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		$limit = (int)$this->_common_ini["search"]["limit"];
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

		$this->_Template->assign(array(
			"q" => $q,
			"type" => $type,
			"index" => $index,
			"stype" => $stype,
			"sort" => $sort,
			"order" => $order,
			"lists" => $albums_result["data"]["lists"],
			"pager" => $Pager->getPager((int)$page, $albums_result["data"]["lists_count"], $limit, 5),
		))->display("Albums/Index.tpl.php");
		return true;
	}
}
