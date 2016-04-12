<?php
/**
 * /lib/Scrv/Action/Albums/Fav.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Server as Server;

/**
 * Albums fav class
 * @author mgng
 */
class Fav extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはエラー
		if ( ! $this->_is_login ) {
			Server::send404Header();
			return false;
		}

		$album_id = Server::post("album_id", "");
		if (!ctype_digit($album_id)) {
			Server::send404Header();
			return false;
		}

		// fav.albums更新
		$DaoAlbums = new DaoAlbums();
		$fav_result = $DaoAlbums->fav((int)$album_id, $this->_login_user_data["id"]);

		header("Content-Type:application/json; charset=utf-8");
		echo json_encode($fav_result);

		return true;
	}
}
