<?php
/**
 * /lib/Scrv/Action/Itunes/GooglePlayMusic.php
 * @author mgng
 */

namespace lib\Scrv\Action\GooglePlayMusic;
use lib\Scrv\Action\Base as Base;
use lib\Util\GoogleMusicSearch as GoogleMusicSearch;
use lib\Util\Server as Server;

/**
 * Google Music Search class
 * @author mgng
 */
class Search extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$q = mb_trim(Server::get("q", ""));
		if ( $q === "" ) {
			Server::send404Header();
			return false;
		}

		$GMS = new GoogleMusicSearch(self::$_common_ini["googleplaymusic"]["api_url"]);
		$result = $GMS->searchAlbum($q);

		header("Content-Type:application/json; charset=utf-8");
		echo json_encode($result);

		return true;
	}
}
