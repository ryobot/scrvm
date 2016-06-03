<?php
/**
 * /lib/Scrv/Action/Tags/Add.php
 * @author mgng
 */

namespace lib\Scrv\Action\Tags;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Tags as DaoTags;
use lib\Util\Server as Server;

/**
 * Tags Add class
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
		// 未ログインは404
		if(!$this->_is_login) {
			Server::send404Header("404 not found");
			return false;
		}

		// post params 取得
		$tag = mb_trim(Server::post("tag", ""));
		$album_id = mb_trim(Server::post("album_id", ""));
		$token = mb_trim(Server::post("token", ""));

		// CSRFチェック
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( $sess_token !== $token ) {
			Server::send404Header("system error.");
			return false;
		}

		// album_id check
		if ( !ctype_digit($album_id) ){
			Server::send404Header("system error.");
			return false;
		}

		// tagが空の場合はなにもせずに戻す
		if ( $tag === "" ) {
			Server::redirect($this->_BasePath . "Albums/View/id/{$album_id}");
			return false;
		}

		// tag登録
		$DaoTags = new DaoTags();
		$tags_result = $DaoTags->add($album_id, $this->_login_user_data["id"], $tag);
		if ( !$tags_result["status"] ){
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $tags_result["messages"]);
		}

		Server::redirect($this->_BasePath . "Albums/View/id/{$album_id}");
		return true;
	}
}
