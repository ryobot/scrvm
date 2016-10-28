<?php
/**
 * /lib/Scrv/Action/Users/SaveUsername.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;

/**
 * Save Username class
 * @author mgng
 */
class SaveUsername extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインの場合はログイン画面にリダイレクト
		$this->isNotLogined($this->_BasePath . "Auth");

		// POSTパラメータ取得、mb_trim、改行コード統一
		$post_params = array(
			"token" => Server::post("token", ""),
			"username" => Server::post("username", ""),
			"password" => Server::post("password", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}

		// CSRFチェック
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( $sess_token !== $post_params["token"] ) {
			Server::send404Header("system error..");
			return false;
		}

		// post_params チェック
		$check_result = $this->_checkPostParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Users/Edit");
			return false;
		}

		// 登録処理
		$DaoUsers = new DaoUsers();
		$save_result = $DaoUsers->saveUsername(
			$this->_login_user_data["id"],
			$this->_login_user_data["username"],
			$post_params["username"],
			$post_params["password"],
			$this->_is_only_twitter_login
		);
		if ( ! $save_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $save_result["messages"]);
			Server::redirect($this->_BasePath . "Users/Edit");
			return false;
		}

		// 登録情報を取得してセッション情報を書き換える
		$user_result = $DaoUsers->view($this->_login_user_data["id"]);
		if ( !$user_result["status"] ) {
			Server::send404Header("system error...");
			return false;
		}
		$this->_Session->set(Scrv\SessionKeys::LOGIN_USER_DATA, $user_result["data"]);

		// セッションのpost_param をクリアしてリダイレクト
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
		$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, array("ユーザ名を変更しました。"));
		Server::redirect($this->_BasePath . "Users/Edit");
		return true;
	}

	/**
	 * post params check
	 * @param array $post_params
	 * @return resultSet
	 */
	private function _checkPostParams(array $post_params)
	{
		$check_result = getResultSet();
		if ( $post_params["username"] === "" ) {
			$check_result["messages"]["username"] = "username が未入力です。";
		} else if ( mb_strlen($post_params["username"]) > 50 ){
			$check_result["messages"]["username"] = "username は50文字以内で入力してください。";
		}
		// twitter ログインのみの場合はパスワードチェックしない
		if ( ! $this->_is_only_twitter_login ) {
			if ( $post_params["password"] === "" ) {
				$check_result["messages"]["password"] = "password が未入力です。";
			}
		}
		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}
}
