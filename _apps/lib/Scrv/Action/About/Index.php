<?php
/**
 * /lib/Scrv/Action/About/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\About;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;

/**
 * About Index class
 * @author mgng
 */
class Index extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// キャッシュ設定
		$cache_setting = array(
			"created" => self::$_nowTimestamp,
			"expire" => 10, // 10秒キャッシュ
			"request_uri" => Server::env("REQUEST_URI"),
		);
		$cache_contents = $this->_Template->getCache( $cache_setting["request_uri"]);
		if ( $cache_contents ) {
			header("X-Cached-Contents: " . $cache_setting["expire"]);
			echo $cache_contents;
			return true;
		}

		$this->_Template->setCache($cache_setting)->assign(array())->display("About/Index.tpl.php");
		return true;
	}

}
