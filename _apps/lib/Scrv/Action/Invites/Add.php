<?php
/**
 * /lib/Scrv/Action/Users/AddNew.php
 * @author mgng
 */

namespace lib\Scrv\Action\Invites;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Scrv\Dao\Invites as DaoInvites;

/**
 * Invittes User Add class
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
		// POSTパラメータ取得、mb_trim、改行コード統一、セッション保持
		$post_params = array(
			"token" => Server::post("token", ""),
			"username" => Server::post("username", ""),
			"password" => Server::post("password", ""),
			"password_re" => Server::post("password_re", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}
		$this->_Session->set(Scrv\SessionKeys::POST_PARAMS, $post_params);

		// CSRFチェック
		$sess_invitation_data = $this->_Session->get(Scrv\SessionKeys::INVITATIONS_DATA);
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( !isset($sess_invitation_data) || $sess_token !== $post_params["token"] ) {
			Server::send404Header("system error.");
			return false;
		}

		// post_params チェック
		$check_result = $this->_checkPostParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Invites?hash={$sess_invitation_data["hash"]}");
			return false;
		}

		// 登録処理
		$DaoUsers = new DaoUsers();
		$add_result = $DaoUsers->addNew(
			$post_params["username"],
			$post_params["password"],
			"author",
			$sess_invitation_data["user_id"]
		);
		if ( ! $add_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $add_result["messages"]);
			Server::redirect($this->_BasePath . "Invites?hash={$sess_invitation_data["hash"]}");
			return false;
		}

		// hash削除
		$DaoInvites = new DaoInvites();
		$DaoInvites->deleteHash($sess_invitation_data["hash"]);

		// セッションのpost_param をクリア
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
		$this->_Session->clear(Scrv\SessionKeys::INVITATIONS_DATA);

		// ログイン済みとして処理
		$timeout = 30 * 60;
		$this->_Session->init();
		$this->_Session->regenerate();
		$this->_Session->set(Scrv\SessionKeys::IS_LOGIN, true);
		$this->_Session->set(Scrv\SessionKeys::LOGIN_USER_DATA, $add_result["data"]);
		$this->_Session->set(Scrv\SessionKeys::LOGIN_TIMEOUT, $timeout);
		$this->_Session->set(Scrv\SessionKeys::LOGIN_EXPIRES, $timeout + self::$_nowTimestamp);

		//Server::redirect($this->_BasePath . "Users/View/id/" . $add_result["data"]["id"]);
		Server::redirect($this->_BasePath . "About");
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

		if ( $post_params["password"] === "" ) {
			$check_result["messages"]["password"] = "password が未入力です。";
		} else if ( mb_strlen($post_params["password"]) > 100 ){
			$check_result["messages"]["password"] = "password は100文字以内で入力してください";
		} else if ( $post_params["password"] !== $post_params["password_re"] ) {
			$check_result["messages"]["password"] = "password が一致しません。";
		}

		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}

}
