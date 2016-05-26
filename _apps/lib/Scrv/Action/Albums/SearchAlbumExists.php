<?php
/**
 * /lib/Scrv/Action/Albums/SearchArtist.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Server as Server;


/**
 * albums search artist class
 * @author mgng
 */
class SearchAlbumExists extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはnot found
		if ( ! $this->_is_login ) {
			Server::send404Header("404 not found");
			return false;
		}
		$artist = mb_trim(Server::post("artist"));
		$title = mb_trim(Server::post("title"));
		$DaoAlbums = new DaoAlbums();
		$results = $DaoAlbums->exists($artist, $title);
		header("Content-Type:application/json; charset=UTF-8");
		echo json_encode($results, true);
		return true;
	}

}
