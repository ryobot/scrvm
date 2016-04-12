<?php
/**
 * /lib/Scrv/Action/Users/Edit.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;
use lib\Util\Password as Password;

/**
 * Users Edit class
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
		// 未ログインの場合はログイン画面にリダイレクト
		$this->isNotLogined($this->_BasePath . "Auth");

		// ユーザidチェック
		$user_id = Server::get("id", "");
		if ( ! ctype_digit($user_id) ) {
			Server::send404Header("not found.");
			return false;
		}

		// admin権限の場合はOK、通常権限の場合はログインユーザのidと一致していないとダメ
		if ( $this->_login_user_data["role"] !== "admin" && $this->_login_user_data["id"] !== (int)$user_id ) {
			Server::send404Header("not found..");
			return false;
		}

		// ユーザ情報取得
		$DaoUsers = new DaoUsers();
		$user_result = $DaoUsers->view((int)$user_id);
		if ( ! $user_result["status"] ) {
			Server::send404Header("db error.");
			return false;
		}

		// セッション値取得
		$post_params = $this->_Session->get(Scrv\SessionKeys::POST_PARAMS);
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// POSTパラメータがnullまたはエラーがある場合はDB情報をセット
		if ( ! isset($post_params) || (isset($error_messages) && count($error_messages) > 0) ) {
			$post_params = $user_result["data"];
		}

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		$this->_Template->assign(array(
			"post_params" => $post_params,
			"error_messages" => $error_messages,
			"token" => $token,
			"user_id" => $user_id,
		))->display("Users/Edit.tpl.php");

		return true;
	}

}
