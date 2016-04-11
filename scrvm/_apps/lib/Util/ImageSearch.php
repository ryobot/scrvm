<?php
/**
 * ImageSearch.php
 * @author mgng
 */

namespace lib\Util;

/**
 * ImageSearch class
 * @author mgng
 * @package lib\Util
 */
class ImageSearch
{
	/**
	 * google 画像検索
	 * @param string $search_word
	 * @param integer $start
	 * @return array
	 */
	public function google( $search_word, $start = 0 )
	{
		$query = http_build_query( array(
			"q" => $search_word,
			"um" => "1",
			"sa" => "N",
			"hl" => "ja",
			"tbm" => "isch",
			"ijn" => "1",
			"start" => $start,
		) );
		$src = file_get_contents("https://www.google.co.jp/search?{$query}");
		if ( $src === false ) {
			return array();
		}
		preg_match_all("/img\s+data\-src=\"(.+?)\"/is", $src, $m);
		if ( ! isset( $m[1] ) || count($m[1]) === 0 ) {
			return array();
		}
		return $m[1];
	}
}
