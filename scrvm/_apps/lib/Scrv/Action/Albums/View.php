<?php
/**
 * /lib/Scrv/Action/Albums/View.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Scrv\Dao\Tracks as DaoTracks;

/**
 * albums View class
 * @author mgng
 */
class View extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$id = Server::get("id");
		if ( ! isset($id) || ! ctype_digit($id) ) {
			Server::send404Header("not found.");
			return false;
		}

		// アルバム情報取得
		$DaoAlbums = new DaoAlbums();
		$view_result = $DaoAlbums->view((int)$id);
		if (!$view_result["status"]) {
			Server::send404Header("not found..");
			return false;
		}

		// ログインしている場合は自身がfavしたid一覧を取得
		$own_favtracks = array();
		if ( $this->_is_login ) {
			$DaoTracks = new DaoTracks();
			$favtracks_result = $DaoTracks->favtracks((int)$id, $this->_login_user_data["id"]);
			if (!$favtracks_result["status"]) {
				Server::send404Header("not found...");
				return false;
			}
			foreach($favtracks_result["data"] as $data) {
				$own_favtracks[] = $data["id"];
			}
		}

		$this->_Template->assign(array(
			"album_id" => $id,
			"album" => $view_result["data"]["album"],
			"tracks" => $view_result["data"]["tracks"],
			"reviews" => $view_result["data"]["reviews"],
			"own_favtracks" => $own_favtracks,
		))->display("Albums/View.tpl.php");
		return true;
	}
}
