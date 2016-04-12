<?php
/**
 * Itunes.php
 * @author mgng
 */

namespace lib\Util;

/**
 * Itunes API class
 * @author mgng
 * @package lib\Util
 */
class Itunes
{
	/**
	 * API url
	 * @var string
	 */
	private $_api_url = null;

	/**
	 * コンストラクタ
	 * @param string $api_url
	 * @return boolean
	 */
	public function __construct($api_url)
	{
		$this->_api_url = $api_url;
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
	 * itunes search albums
	 * @param string $term
	 * @return string
	 */
	public function searchAlbums($term)
	{
		$query = http_build_query(array(
			"term" => $term,
			"country" => "JP",
			"entity" => "album",
		));
		$url = $this->_api_url . "?" . $query;
		$result = file_get_contents($url);
		return $result === false ? "" : trim($result);
	}

}
