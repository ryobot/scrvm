<?php
/**
 * /lib/Scrv/Helper/Reviews/ParseHashTags.php
 * @author mgng
 */

namespace lib\Scrv\Helper\Reviews;

/**
 * Parse
 * @author mgng
 */
class Parse
{
	/**
	 * ハッシュタグ抽出用正規表現
	 * @var string
	 */
	private $_reg_hashtags = "/(^|\s)#([^\W]+)/u";

	/**
	 * body 内のハッシュタグを抜き出して配列で返す。
	 * @param string $body
	 * @return array
	 */
	public function hashTags($body)
	{
		preg_match_all($this->_reg_hashtags, $body, $match);
		return $match[2];
	}

	public function replaceHashTagsToLink($body, $base_path)
	{
		return preg_replace_callback($this->_reg_hashtags, function($match) use ($base_path){
			return ' <a href="'.h($base_path).'Reviews/Index/hash/'.urlencode($match[2]).'">#'.h($match[2]).'</a>';
		}, $body);
	}

}
