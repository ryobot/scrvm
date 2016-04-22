<?php
/**
 * GoogleMusicSearch.php
 * @author mgng
 */

namespace lib\Util;

/**
 * GoogleMusicSearch API class
 * @author mgng
 * @package lib\Util
 */
class GoogleMusicSearch
{
	/**
	 * API url
	 * @var string
	 */
	private $_api_url = "https://play.google.com";

	/**
	 * コンストラクタ
	 * @param string $api_url
	 * @return boolean
	 */
	public function __construct($api_url = null)
	{
		if ( isset($api_url) ) {
			$this->_api_url = $api_url;
		}
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
	 * search
	 * @param string $q
	 */
	public function searchAlbum($q)
	{
		$results = array();

		$query = http_build_query(array(
			"q" => $q,
			"c" => "music",
			"docType" => "2",	// アルバム検索は 2
		));
		$html = $this->_request("/store/search?{$query}");

		// remove tag
		$html = preg_replace('/<script.*?>.*?<\/script>/umis', '', $html);
		$html = preg_replace('/<style.*?>.*?<\/style>/umis', '', $html);
		$html = preg_replace('/<(meta|link).+?>/umis', '', $html);

		// load dom
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true );
		$status =$dom->loadHTML( mb_convert_encoding( $html, "HTML-ENTITIES", "utf-8" ) );
		libxml_clear_errors();
		if (!$status) {
			return $results;
		}

		// xpath search
		$xpath = new \DOMXPath($dom);
		$nodes = $xpath->query( '//div[contains(@class,"id-card-list")]/div[contains(@class,"card")]' );
		foreach($nodes as $node){
			$details = $this->_getElementsByClassName($node, "details", "div");
			foreach($details as $detail) {
				$_a = $this->_getElementsByClassName($detail, "card-click-target", "a");
				$href= $_a[0]->getAttribute("href");
				$_title = $this->_getElementsByClassName($detail, "title", "a");
				$title= $_title[0]->getAttribute("title");
				$_artist = $this->_getElementsByClassName($detail, "subtitle", "a");
				$artist= $_artist[0]->getAttribute("title");
				$results[] = array(
					"artist" => $artist,
					"title" => $title,
					"url" => $this->_api_url . $href,
				);
			}
		}

		return $results;
	}

	/**
	 * request
	 * @param string $query
	 * @return string
	 */
	private function _request($query)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $this->_api_url . "{$query}" );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		return curl_exec ($ch);
	}

	/**
	 * getElementsByClassName
	 * @param DOMDocument|DOMNode $dom
	 * @param string $class_name
	 * @param type $tag_name
	 * @return DOMNode
	 */
	private function _getElementsByClassName( $dom, $class_name, $tag_name = "*" )
	{
		$elements = $dom->getElementsByTagName( $tag_name );
		$matched = array();
		foreach( $elements as $node ) {
			if( ! $node->hasAttributes() ) {
				continue;
			}
			$classAttribute = $node->attributes->getNamedItem('class');
			if( ! $classAttribute) {
				continue;
			}
			$classes = explode(' ', $classAttribute->nodeValue);
			if ( in_array( $class_name, $classes ) ) {
				$matched[] = $node;
			}
		}
		return $matched;
	}

}
