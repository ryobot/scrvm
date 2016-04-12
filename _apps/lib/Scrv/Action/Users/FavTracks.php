<?php
/**
 * /lib/Scrv/Action/Users/FavTracks.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Scrv\Dao\Tracks as DaoTracks;
use lib\Util\Server as Server;
use lib\Util\Pager as Pager;

/**
 * Users FavTracks class
 * @author mgng
 */
class FavTracks extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// id取得
		$user_id = Server::get("id");
		if ( ! isset($user_id) || ! ctype_digit($user_id)) {
			Server::send404Header("404 not found.");
			return false;
		}

		// ユーザ情報取得
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view((int)$user_id);
		if ( ! $user_result["status"] || count($user_result["data"]) === 0){
			Server::send404Header("404 not found..");
			return false;
		}

		// offset設定
		$offset = Server::get("offset", "0");
		if ( ! ctype_digit($offset) ) {
			$offset = "0";
		}
		$limit = $this->_common_ini["search"]["limit"];

		// track情報取得
		$DaoTracks = new DaoTracks();
		$favtracks_result = $DaoTracks->favtracksByUserId((int)$user_id, (int)$offset, (int)$limit);
		if ( !$favtracks_result["status"] ) {
			Server::send404Header("404 not found....");
			return false;
		}

		// pager
		$Pager = new Pager();

		$this->_Template->assign(array(
			"user_id" => $user_id,
			"user" => $user_result["data"],
			"favtracks" => $favtracks_result["data"],
			"pager" => $Pager->getPager($offset, $limit, $user_result["data"]["favtracks_count"]),
		))->display("Users/FavTracks.tpl.php");
		return true;
	}

}
