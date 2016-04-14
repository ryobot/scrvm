<?php
/**
 * /lib/Scrv/Action/Albums/SearchArtist.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Util\Gracenote as Gracenote;
use lib\Util\Server as Server;

/**
 * albums search artist class
 * @author mgng
 */
class SearchArtist extends Base
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
		$Gracenote = new Gracenote(
			$this->_common_ini["gracenote"]["api_url"],
			$this->_common_ini["gracenote"]["client_id"],
			$this->_common_ini["gracenote"]["user_id"]
		);
		$res = $Gracenote->searchAlbums($artist, $title);
		$xml = new \SimpleXMLElement($res);
		$albums = $xml->xpath("RESPONSE/ALBUM");
		$results = array();
		foreach($albums as $album) {
			$tmp = array(
				"artist" => (string)$album->ARTIST,
				"title" => (string)$album->TITLE,
				"year" => (string)$album->DATE,
				"tracks" => array(),
			);
			foreach($album->TRACK as $track) {
				$tmp["tracks"][] = (string)$track->TITLE;
			}
			$results[] = $tmp;
		}
		header("Content-Type:application/json; charset=UTF-8");
		echo json_encode($results, true);
		return true;
	}
}
