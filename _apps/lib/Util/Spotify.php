<?php
/**
 * Spotify.php
 * @author mgng
 */

namespace lib\Util;

/**
 * Spotify API class
 * @author mgng
 * @package lib\Util
 */
class Spotify
{
	/**
	 * client id
	 * @var string
	 */
	private $_client_id = null;

	/**
	 * client secret
	 * @var string
	 */
	private $_client_secret = null;

	/**
	 * コンストラクタ
	 * @return boolean
	 */
	public function __construct()
	{
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
	 * set config
	 * @param array $p
	 * @return boolean
	 */
	public function setConfig(array $p)
	{
		$this->_client_id = $p["client_id"];
		$this->_client_secret = $p["client_secret"];
		return true;
	}

	/**
	 * get access token
	 * @return array
	 */
	public function getAccessToken()
	{
		$endpoint_url = "https://accounts.spotify.com/api/token";
		$base64 = base64_encode("{$this->_client_id}:{$this->_client_secret}");
		$params = array("grant_type" => "client_credentials",);
		$headers = array("Authorization: Basic {$base64}",);
		return $this->_request($endpoint_url, "POST", $headers, $params);
	}

	/**
	 *
	 * @param string $access_token
	 * @param string $q
	 * @param string $type
	 * @return array
	 */
	public function search($access_token, $q, $type)
	{
		$endpoint_url = "https://api.spotify.com/v1/search";
		$headers = array("Authorization: Bearer {$access_token}",);
		$params = array(
			"q" => $q,
			"type" => $type,
		);
		return $this->_request($endpoint_url, "GET", $headers, $params);
	}

	/**
	 * request
	 * @param string $url
	 * @param strin $method
	 * @param array $headers
	 * @param array $params
	 * @return array
	 */
	private function _request($url, $method="GET", array $headers = array(), array $params = array())
	{
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 20,
			CURLOPT_TIMEOUT => 30,
		);

		if (count($headers) > 0) {
			$options[CURLOPT_HTTPHEADER] = $headers;
		}

		switch($method) {
			case "GET":
				$url .= "?" . http_build_query($params);
				break;
			case "POST":
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = http_build_query($params);
				break;
			default:
				break;
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		$response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		return array(
			"response" => $response,
			"error" => $error,
		);
	}

}
