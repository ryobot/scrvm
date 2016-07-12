<?php
/**
 * /lib/Scrv/Action/Albums/Edit.php
 * @author mgng
 */

namespace lib\Scrv\Action\Albums;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Server as Server;
use lib\Util\Password as Password;

/**
 * albums Edit class
 * @author mgng
 */
class Edit extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはログイン画面へ
		$this->isNotLogined($this->_BasePath . "Auth");

		// album_id
		$id = Server::get("id", "");
		if ( !ctype_digit($id) ) {
			Server::send404Header("404 not found");
			return false;
		}

		// アルバム情報取得
		$DaoAlbums = new DaoAlbums();
		$album_result = $DaoAlbums->view($id);
		if (!$album_result["status"]) {
			Server::send404Header("server error.");
			return false;
		}
		if ( $this->_login_user_data["role"] !== "admin"
			&& $album_result["data"]["album"]["create_user_id"] !== $this->_login_user_data["id"]
		) {
			Server::send404Header("server error..");
			return false;
		}

		// セッション値取得
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// token生成、セッションに保持.ただしsess_tokenは hash:album_idの形式 -> token,idを遷移先でチェック
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, "{$token}:{$id}");

		$this->_Template->assign(array(
			"error_messages" => $error_messages,
			"id" => $id,
			"token" => $token,
			"album" => $album_result["data"]["album"],
			"tags" => $album_result["data"]["tags"],
			"tracks" => $album_result["data"]["tracks"],
			"reviews" => $album_result["data"]["reviews"],
		))->display("Albums/Edit.tpl.php");
		return true;
	}
}
