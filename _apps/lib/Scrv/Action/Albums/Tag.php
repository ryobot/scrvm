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
		$limit = (int)$this->_common_ini["search"]["limit"];
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

		$this->_Template->assign(array(
			"tag" => $tag,
			"artist" => $artist,
			"sort" => $sort,
			"order" => $order,
			"lists" => $albums_result["data"]["lists"],
			"pager" => $Pager->getPager((int)$page, $albums_result["data"]["lists_count"], $limit, 5),
		))->display("Albums/Tag.tpl.php");
		return true;
	}
}
