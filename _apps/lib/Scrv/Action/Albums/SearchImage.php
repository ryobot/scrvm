<?php
/**
 * /lib/Scrv/Action/Albums/SearchImage.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Util\ImageSearch as ImageSearch;
use lib\Util\Server as Server;

/**
 * albums SearchImage class
 * @author mgng
 */
class SearchImage extends Base
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

		$q = Server::post("q");
		$ImageSearch = new ImageSearch();
		$results = $ImageSearch->google($q);
		header("Content-Type:application/json; charset=UTF-8");
		echo json_encode($results, true);
		return true;
	}
}
