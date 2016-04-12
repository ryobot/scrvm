<?php
/**
 * /lib/Scrv/Action/Itunes/Search.php
 * @author mgng
 */

namespace lib\Scrv\Action\Itunes;
use lib\Scrv\Action\Base as Base;
use lib\Util\Itunes as Itunes;
use lib\Util\Server as Server;

/**
 * Itunes Search class
 * @author mgng
 */
class Search extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$term = mb_trim(Server::get("term", ""));
		if ( $term === "" ) {
			Server::send404Header();
			return false;
		}

		$Itunes = new Itunes($this->_common_ini["itunes"]["api_url"]);
		$result = $Itunes->searchAlbums($term);

		header("Content-Type:application/json; charset=utf-8");
		echo $result;

		return true;
	}
}
