<?php
/**
 * /lib/Scrv/Action/Albums/View.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;
use lib\Util\Password as Password;
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

		// セッション値取得
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		// アルバム情報取得
		$own_user_id = isset($this->_login_user_data["id"]) ? $this->_login_user_data["id"] : null;
		$DaoAlbums = new DaoAlbums();
		$view_result = $DaoAlbums->view((int)$id, $own_user_id);
		if (!$view_result["status"]) {
			Server::send404Header("not found..");
			return false;
		}

		// XXX user_id ごとにまとめておく
		$reviews_thread_by_user_id = array();
		foreach($view_result["data"]["reviews"] as $row){
			$user_id = $row["user_id"];
			if ( !isset($reviews_thread_by_user_id[$user_id]) ){
				$reviews_thread_by_user_id[$user_id] = array();
			}
			$reviews_thread_by_user_id[$user_id][] = $row;
		}

		// ログインしている場合は自身がfavしたid一覧を取得
		$own_favtracks = array();
		$own_favalbums = array();
		if ( $this->_is_login ) {
			$DaoTracks = new DaoTracks();
			$DaoAlbums = new DaoAlbums();
			$favtracks_result = $DaoTracks->favtracks((int)$id, $this->_login_user_data["id"]);
			$favalbums_result = $DaoAlbums->favalbums((int)$id, $this->_login_user_data["id"]);
			if (!$favtracks_result["status"] || !$favalbums_result["status"]) {
				Server::send404Header("not found...");
				return false;
			}
			foreach($favtracks_result["data"] as $data) {
				$own_favtracks[] = $data["id"];
			}
			foreach($favalbums_result["data"] as $data) {
				$own_favalbums[] = $data["id"];
			}
		}

		$album = $view_result["data"]["album"];
		$view_year = isset($album["year"]) && $album["year"] !== "" ? $album["year"] : "unknown";

		$this->_Template->assign(array(
			"album_id" => $id,
			"album" => $album,
			"tags" => $view_result["data"]["tags"],
			"token" => $token,
			"tracks" => $view_result["data"]["tracks"],
			"reviews" => $view_result["data"]["reviews"],
			"reviews_thread_by_user_id" => $reviews_thread_by_user_id,
			"own_favtracks" => $own_favtracks,
			"own_favalbums" => $own_favalbums,
			"error_messages" => $error_messages,
			"_description" => "{$album["artist"]}/{$album["title"]} ({$view_year})",
		))->display("Albums/View.tpl.php");
		return true;
	}
}
