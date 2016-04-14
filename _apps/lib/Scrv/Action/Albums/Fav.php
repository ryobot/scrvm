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

		// json header
		header("Content-Type:application/json; charset=utf-8");

		$DaoAlbums = new DaoAlbums();

		// fav.albums更新
		$fav_result = $DaoAlbums->fav((int)$album_id, $this->_login_user_data["id"]);
		if ( ! $fav_result["status"] ) {
			echo json_encode($fav_result);
			return false;
		}

		// 再取得
		$favcount_result = $DaoAlbums->favCount((int)$album_id);
		if ( ! $favcount_result["status"] ) {
			echo json_encode($favcount_result);
			return false;
		}

		echo json_encode(array(
			"status" => true,
			"data" => array(
				"operation" => $fav_result["data"]["operation"],
				"fav_count" => $favcount_result["data"]["count"],
			),
		));

		return true;
	}
}
