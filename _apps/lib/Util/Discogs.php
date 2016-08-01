<?php
/**
 * Discogs.php
 * @author mgng
 */

namespace lib\Util;

/**
 * Discogs API class
 * @author mgng
 * @package lib\Util
 */
class Discogs
{
	/**
	 * API url
	 * @var string
	 */
	private $_api_url = null;

	/**
	 * consumer_key
	 * @var string
	 */
	private $_consumer_key = null;

	/**
	 * consumer_secret
	 * @var string
	 */
	private $_consumer_secret = null;

	/**
	 * constructer
	 * @param string $api_url
	 * @param string $consumer_key
	 * @param string $consumer_secret
	 * @return boolean
	 */
	public function __construct($api_url, $consumer_key, $consumer_secret)
	{
		$this->_api_url = $api_url;
		$this->_consumer_key = $consumer_key;
		$this->_consumer_secret = $consumer_secret;
		return true;
	}

	/**
	 * destructor
	 * @return boolean
	 */
	public function __destruct()
	{
		return true;
	}

	/**
	 * search
	 * @param string $artist
	 * @param string $title
	 * @param string $track
	 * @return array
	 */
	public function search($artist, $title, $track)
	{
		$q = array(
			"artist" => $artist,
			"release_title" => $title,
			"track" => $track,
		);

		$url = $this->_api_url . "/database/search?" . http_build_query($q);
		$res = $this->_request($url);
		$res_json = json_decode($res);

		// apiエラーや検索結果0件の場合
		if ( !$res_json || ! isset($res_json->results) || count($res_json->results) === 0 ) {
			return array();
		}

		$urls = array();
		foreach($res_json->results as $row){
			$urls[] = $row->resource_url;
		}

		// resource_url が大量の場合、マルチリクエストで重くなるため、
		// フロントには resource_url の配列を返し、
		// ブラウザ側でAjaxを使い resource_url にアクセスする
		// ※ resource_url は Access-Control-Allow-Origin: * ヘッダがついているので クロスドメイン処理可能
		return $urls;

//		$responses = $this->_requestMulti($urls);
//
//		$results = array();
//		// 各結果から詳細情報を取得する
//		foreach($responses as $res){
//			$detail = $this->getDetail($res);
//			if (!$detail) {
//				continue;
//			}
//			$results[] = $detail;
//		}
//		return $results;
	}

	public function getDetail($res)
	{
		$res_json = json_decode($res);
		if ( !$res_json || ! isset($res_json->tracklist) || count($res_json->tracklist) === 0 ) {
			return false;
		}
		$tracks = array();
		foreach($res_json->tracklist as $track) {
			$tracks[] = $track->title;
		}
		$result = array(
			"artist" => $res_json->artists[0]->name,
			"title" => $res_json->title,
			"year" => $res_json->year,
			"tracks" => $tracks,
		);
		return $result;
	}

	/**
	 * POST request
	 * @param string $url
	 * @return string
	 */
	private function _request($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT,        10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
			"Authorization: Discogs key={$this->_consumer_key}, secret={$this->_consumer_secret}",
			"User-Agent: syncreview/1.0",
		));
		return curl_exec($ch);
	}

	private function _requestMulti($urls,$timeout = 0)
	{
		$mh = curl_multi_init();
		$conn = array();
		foreach( $urls as $i => $url) {
			$conn[$i] = curl_init();
			curl_setopt( $conn[$i], CURLOPT_URL,            $url);
			curl_setopt( $conn[$i], CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $conn[$i], CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $conn[$i], CURLOPT_RETURNTRANSFER, true);
			curl_setopt( $conn[$i], CURLOPT_FOLLOWLOCATION, true);
			curl_setopt( $conn[$i], CURLOPT_HTTPHEADER,     array(
				"Authorization: Discogs key={$this->_consumer_key}, secret={$this->_consumer_secret}",
				"User-Agent: syncreview/1.0",
			));

			if ( $timeout ){
				curl_setopt( $conn[$i], CURLOPT_TIMEOUT, $timeout );
			}
			curl_multi_add_handle( $mh, $conn[$i]);
		}
		$running = null;
		do {
			usleep( 10000 );
			curl_multi_exec( $mh, $running );
		} while( $running > 0 );
		$res = array();
		foreach( $urls as $i => $url ){
			$res[] = curl_multi_getcontent( $conn[$i] );
			curl_multi_remove_handle( $mh, $conn[$i] );
		}
		curl_multi_close($mh);
		return $res;
	}
}
