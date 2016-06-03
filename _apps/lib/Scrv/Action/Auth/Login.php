<?php
/**
 * /lib/Scrv/Action/Auth/Login.php
 * @author mgng
 */

namespace lib\Scrv\Action\Auth;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Auth as DaoAuth;
use lib\Util\Server as Server;

/**
 * ログイン処理クラス
 * @author mgng
 */
class Login extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// ログインしていたらTOPリダイレクト
		$this->isLogined($this->_BasePath);

		// POSTパラメータ取得、mb_trim、改行コード統一、セッション保持
		$post_params = array(
			"token" => Server::post("token", ""),
			"username" => Server::post("username", ""),
			"password" => Server::post("password", ""),
			"autologin" => Server::post("autologin", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}
		$this->_Session->set(Scrv\SessionKeys::POST_PARAMS, $post_params);

		// CSRFチェック
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( $sess_token !== $post_params["token"] ) {
			Server::send404Header("system error.");
			return false;
		}

//		// スーパーユーザの場合はログイン済みとして処理、トップにリダイレクト
//		if ( $post_params["username"] === self::$_common_ini["root"]["username"]
//			&& $post_params["password"] === self::$_common_ini["root"]["password"]
//		) {
//			$this->_Session->init();
//			$this->_Session->regenerate();
//			$this->_Session->set(Scrv\SessionKeys::IS_LOGIN, true);
//			$this->_Session->set(Scrv\SessionKeys::LOGIN_USER_DATA, array(
//				"id" => 0,
//				"username" => self::$_common_ini["root"]["username"],
//				"role" => "admin",
//				"favalbum_count" => null,
//				"favtrack_count" => null,
//				"img_file" => null,
//				"created" => "2016-01-01 00:00:00",
//				"modifiled" => null,
//				"review_count" => 0,
//			));
//			Server::redirect($this->_BasePath);
//			return true;
//		}

		// post_params チェック
		$check_result = $this->_checkPostParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Auth");
			return false;
		}

		// ログイン処理
		$DaoAuth = new DaoAuth();
		$login_result = $DaoAuth->login($post_params["username"], $post_params["password"]);
		if ( ! $login_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $login_result["messages"]);
			Server::redirect($this->_BasePath . "Auth");
			return false;
		}

		// セッションにログイン情報を保持、ログイン前にみていたページにリダイレクト
		$after_url_logined = $this->_Session->get(Scrv\SessionKeys::URL_AFTER_LOGINED);
		$this->_Session->clear(Scrv\SessionKeys::URL_AFTER_LOGINED);
		if ( !isset($after_url_logined) ){
			$after_url_logined = "Users/View/id/" . $login_result["data"]["id"];
		}

		// 自動ログインの場合の処理
		$timeout = 30 * 60;
		if ( $post_params["autologin"] === "1" ) {
			// timeout を伸ばす
			$timeout = isset(self::$_common_ini["session"]["session.gc_maxlifetime"])
								? (int)self::$_common_ini["session"]["session.gc_maxlifetime"]
								: 30 * 60;
			session_set_cookie_params($timeout);
		}

		$this->_Session->init();
		$this->_Session->regenerate();
		$this->_Session->set(Scrv\SessionKeys::IS_LOGIN, true);
		$this->_Session->set(Scrv\SessionKeys::LOGIN_USER_DATA, $login_result["data"]);
		$this->_Session->set(Scrv\SessionKeys::LOGIN_TIMEOUT, $timeout);
		$this->_Session->set(Scrv\SessionKeys::LOGIN_EXPIRES, $timeout + self::$_nowTimestamp);
		Server::redirect($this->_BasePath . $after_url_logined);

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
		}

		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}

}
