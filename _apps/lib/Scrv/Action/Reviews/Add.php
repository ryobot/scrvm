<?php
/**
 * /lib/Scrv/Action/Reviews/Add.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Albums as DaoAlbums;
use lib\Util\Server as Server;
use lib\Util\Password as Password;

/**
 * Reviews add class
 * @author mgng
 */
class Add extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはログイン画面へ飛ばす
		$this->isNotLogined($this->_BasePath . "Auth");

		$album_id = Server::get("id");
		if ( !isset($album_id) || !ctype_digit($album_id) ) {
			Server::send404Header("not found.");
			return false;
		}

		// アルバム検索
		$DaoAlbums = new DaoAlbums();
		$album_result = $DaoAlbums->view($album_id);
		if (!$album_result["status"]){
			Server::send404Header("not found.");
			return false;
		}

		// セッション値取得
		$post_params = $this->_Session->get(Scrv\SessionKeys::POST_PARAMS);
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		$this->_Template->assign(array(
			"token" => $token,
			"album_id" => $album_id,
			"album" => $album_result["data"]["album"],
			"tracks" => $album_result["data"]["tracks"],
			"reviews" => $album_result["data"]["reviews"],
			"post_params" => $post_params,
			"error_messages" => $error_messages,
		))->display("Reviews/Add.tpl.php");
		return true;
	}
}
