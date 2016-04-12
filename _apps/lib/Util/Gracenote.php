<?php
/**
 * Gracenote.php
 * @author mgng
 */

namespace lib\Util;

/**
 * Gracenote API class
 * @author mgng
 * @package lib\Util
 */
class Gracenote
{
	/**
	 * API url
	 * @var string
	 */
	private $_api_url = null;

	/**
	 * client_id
	 * @var string
	 */
	private $_client_id = null;

	/**
	 * user_id (Gracenote登録後に発行される)
	 * @var string
	 */
	private $_user_id = null;

	/**
	 * コンストラクタ
	 * @param string $api_url
	 * @param string $client_id
	 * @param string $user_id
	 * @return boolean
	 */
	public function __construct($api_url, $client_id, $user_id)
	{
		$this->_api_url = $api_url;
		$this->_client_id = $client_id;
		$this->_user_id = $user_id;
		return true;
	}

	/**
	 * デストラクタ
	 * @return boolean
	 */
	public function __destruct()
	{
		return true;
	}

	/**
	 * 検索処理
	 * @param string $artist
	 * @param string $title default null
	 */
	public function searchAlbums($artist, $title=null)
	{
		$xml = $this->_makeAlbumsQuerys($artist, $title);
		$res = $this->_post($xml);
		return $res;
	}

	/**
	 * 検索用XMLを返す
	 * @param string $artist
	 * @param string $title
	 * @return string
	 */
	private function _makeAlbumsQuerys($artist, $title)
	{
		$tag_artist = "<TEXT TYPE=\"ARTIST\">".h($artist)."</TEXT>";
		$tag_title = isset($title) ? "<TEXT TYPE=\"ALBUM_TITLE\">".h($title)."</TEXT>" : "";
		return
		"<QUERIES>
			<AUTH>
				<CLIENT>{$this->_client_id}</CLIENT>
				<USER>{$this->_user_id}</USER>
			</AUTH>
			<LANG>jpn</LANG>
			<QUERY CMD=\"ALBUM_SEARCH\">
				{$tag_artist}
				{$tag_title}
			</QUERY>
		</QUERIES>"
		;
	}

	/**
	 * POST request
	 * @param string $body
	 * @return string
	 */
	private function _post($body)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $this->_api_url );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST,           true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $body );
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
		return curl_exec ($ch);
	}
}
