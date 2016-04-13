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
	 * @param array $country_list
	 * @return type
	 */
	public function searchAlbums($term, $country_list = array("jp","us"))
	{
		$result = "";
		foreach( $country_list as $country ){
			$res = $this->_search($term, $country);
			if ( $res !== "" ) {
				$result = $res;
				break;
			}
		}
		return $result;
	}

	/**
	 * search itunes api
	 * @param string $term
	 * @param string $country
	 * @return string
	 */
	public function _search($term, $country)
	{
		$query = http_build_query(array(
			"term" => $term,
			"country" => $country,
			"entity" => "album",
			"media"  => "music",
			"limit" => 10,
		));
		$url = $this->_api_url . "?" . $query;
		$result = $this->_get($url);
		return $result === false ? "" : trim($result);
	}

	/**
	 * GET request
	 * @param string $url
	 * @return string
	 */
	private function _get($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		return curl_exec ($ch);
	}

}
