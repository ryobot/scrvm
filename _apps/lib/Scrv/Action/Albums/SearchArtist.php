<?php
/**
 * /lib/Scrv/Action/Albums/SearchArtist.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Util\Gracenote as Gracenote;
use lib\Util\Discogs as Discogs;
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
		$track = mb_trim(Server::post("track"));
		$search_type = mb_trim(Server::post("search_type"));
		$results = array();
		if ( $search_type === "discogs" ) {
			$results = $this->_discogs($artist, $title, $track);
		} else {
			$results = $this->_gracenote($artist, $title, $track);
		}
		header("Content-Type:application/json; charset=UTF-8");
		echo json_encode($results, true);
		return true;
	}

	private function _discogs($artist, $title, $track)
	{
		$Discogs = new Discogs(
			self::$_common_ini["discogs"]["api_url"],
			self::$_common_ini["discogs"]["consumer_key"],
			self::$_common_ini["discogs"]["consumer_secret"]
		);
		return $Discogs->search($artist, $title, $track);
	}

	private function _gracenote($artist, $title, $track)
	{
		$Gracenote = new Gracenote(
			self::$_common_ini["gracenote"]["api_url"],
			self::$_common_ini["gracenote"]["client_id"],
			self::$_common_ini["gracenote"]["user_id"]
		);
		$res = $Gracenote->searchAlbums($artist, $title, $track);
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
		return $results;
	}

}
