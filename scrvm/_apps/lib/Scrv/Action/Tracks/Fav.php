<?php
/**
 * /lib/Scrv/Action/Tracks/Fav.php
 * @author mgng
 */

namespace lib\Scrv\Action\Tracks;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Tracks as DaoTracks;
use lib\Util\Server as Server;

/**
 * Tracks fav class
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

		$track_id = Server::post("track_id", "");
		if (!ctype_digit($track_id)) {
			Server::send404Header();
			return false;
		}

		// favtracks更新
		$DaoTracks = new DaoTracks();
		$fav_result = $DaoTracks->fav((int)$track_id, $this->_login_user_data["id"]);

		header("Content-Type:application/json; charset=utf-8");
		echo json_encode($fav_result);

		return true;
	}
}
