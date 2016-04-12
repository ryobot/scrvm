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
		$offset = Server::get("offset", "0");
		$artist = Server::get("artist", "");
		$sort = Server::get("sort", "artist");
		$order = Server::get("order", "asc");

		// offset設定
		if ( ! ctype_digit($offset) ) {
			$offset = "0";
		}
		$limit = $this->_common_ini["search"]["limit"];

		// sort, order設定
		if ( preg_match("/\A(artist|title|year)\z/", $sort) !== 1 ) {
			$sort = "artist";
		}
		if ( preg_match("/\A(asc|desc)\z/", $order) !== 1 ) {
			$order = "asc";
		}

		// album一覧取得
		$DaoAlbums = new DaoAlbums();
		$albums_result = $DaoAlbums->lists($offset, $limit, $artist, $sort, $order);
		if ( ! $albums_result["status"] ) {
			Server::send404Header("not found.");
			return false;
		}

		// pager
		$Pager = new Pager();

		$this->_Template->assign(array(
			"artist" => $artist,
			"sort" => $sort,
			"order" => $order,
			"lists" => $albums_result["data"]["lists"],
			"pager" => $Pager->getPager($offset, $limit, $albums_result["data"]["lists_count"]),
		))->display("Albums/Index.tpl.php");
		return true;
	}
}
