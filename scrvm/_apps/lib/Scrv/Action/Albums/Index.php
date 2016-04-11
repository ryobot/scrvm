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
		$artist = Server::get("artist");

		// offset設定
		$offset = Server::get("offset", "0");
		if ( ! ctype_digit($offset) ) {
			$offset = "0";
		}
		$limit = $this->_common_ini["search"]["limit"];

		// album一覧取得
		$DaoAlbums = new DaoAlbums();
		$albums_result = $DaoAlbums->lists($offset, $limit, $artist);
		if ( ! $albums_result["status"] ) {
			Server::send404Header("not found.");
			return false;
		}

		// pager
		$Pager = new Pager();

		$this->_Template->assign(array(
			"artist" => $artist,
			"lists" => $albums_result["data"]["lists"],
			"pager" => $Pager->getPager($offset, $limit, $albums_result["data"]["lists_count"]),
		))->display("Albums/Index.tpl.php");
		return true;
	}
}
