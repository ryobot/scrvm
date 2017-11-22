<?php
/**
 * /lib/Scrv/Action/Auth/LoginTwitter.php
 * @author mgng
 */

namespace lib\Scrv\Action\Auth;
use lib\Scrv as Scrv;
use lib\Scrv\SessionKeys as SessionKeys;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Scrv\Dao\Auth as DaoAuth;
use lib\Util\Server as Server;

require_once __DIR__ . "/../../../Vender/tmhOAuth/tmhOAuth.php";

/**
 * ログイン処理クラス
 * @author mgng
 */
class LoginTwitter extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// ログインしていたらTOPリダイレクト
		$this->isLogined($this->_BasePath);

		$authenticate = Server::post("authenticate");
		$is_ok_url = Server::getFullHostUrl() . "{$this->_BasePath}";
		$sess_twitter_access_token = $this->_Session->get(Scrv\SessionKeys::TWITTER_ACCESS_TOKEN);
		if ( isset($sess_twitter_access_token) ) {
			header("Location: {$is_ok_url}");
			return true;
		}
		$this->_auth($authenticate, $is_ok_url);
		return true;
	}

	/**
	 * auth
	 * @param string $authenticate
	 * @param string $is_ok_url
	 * @return string
	 */
	private function _auth($authenticate, $is_ok_url)
	{
		$tmhOAuth = new \tmhOAuth( array(
			'consumer_key' => self::$_common_ini["twitter"]['consumer_key'],
			'consumer_secret' => self::$_common_ini["twitter"]['consumer_secret'],
		) );

		$api_url = "https://api.twitter.com/oauth/";
		$callback = Server::selfUrl();
		$error = "";

		// session.access_token data
		$access_token = $this->_Session->get(Scrv\SessionKeys::TWITTER_ACCESS_TOKEN);
		if ($access_token === null) {
			$access_token = array();
		}
		$access_token["update_timestamp"] = self::$_nowTimestamp;

		// oauth_verifier param
		$oauth_verifier = Server::get("oauth_verifier");

		$DaoUsers = new DaoUsers();

		// session.access_token.user_idがあればtwitter側ですでにデータ取得済み
		if ( isset( $access_token["user_id"] ) ) {
			// リダイレクト
			Server::redirect($this->_BasePath);
		}
		// twitter側認証を終えたあとのブロック
		elseif ( $oauth_verifier !== null ) {
			$this->_Session->regenerate();
			$oauth = $this->_Session->get(Scrv\SessionKeys::TWITTER_OAUTH);
			$tmhOAuth->config['user_token'] = $oauth['oauth_token'];
			$tmhOAuth->config['user_secret'] = $oauth['oauth_token_secret'];
			$code = $tmhOAuth->request('POST', "{$api_url}access_token", array(
				'oauth_verifier' => $oauth_verifier,
			));
			if ($code === 200) {
				$twitter_access_token = $tmhOAuth->extract_params($tmhOAuth->response['response']);

				// user_id で Users テーブルを検索
				$users_search_result = $DaoUsers->viewByTwitterUserId($twitter_access_token["user_id"]);
				if ( !$users_search_result["status"] ) {
					$this->_Session->set(SessionKeys::ERROR_MESSAGES, array("DBエラーが発生しました。"));
					Server::redirect($this->_BasePath."Auth");
					exit;
				}
				// - 2件以上の場合はどれか一つに絞るようエラーを出力
				$user_data = $users_search_result["data"];
				if ( count($user_data) > 1 ) {
					$this->_Session->set(SessionKeys::ERROR_MESSAGES, array("複数アカウントでtwitter連携されています。どれか1つにアカウントを絞ってください…。"));
					Server::redirect($this->_BasePath."Auth");
					exit;
				}

				// 招待リンク経由の場合、セッション値に招待者のIDがあるのでそれを取得
				$sess_inbitations_data = $this->_Session->get(SessionKeys::INVITATIONS_DATA, array());
				$invited_user_id = isset($sess_inbitations_data["user_id"]) ? $sess_inbitations_data["user_id"] : null;

				// - 1件の場合は is_twitter_login フラグを1に→ログイン処理
				// - 0件の場合は新規ユーザ作成→ログイン処理
				$DaoAuth = new DaoAuth();
				$is_existing_user = count($user_data) === 1;
				$auth_result = $is_existing_user ?
					$DaoAuth->loginByTwitter($twitter_access_token) :
					$DaoAuth->loginByTwitterNew($twitter_access_token, $invited_user_id)
				;
				if ( !$auth_result["status"] ) {
					$this->_Session->set(SessionKeys::ERROR_MESSAGES, $auth_result["messages"]);
					Server::redirect($this->_BasePath."Auth");
					exit;
				}
				// セッションにログイン情報を保持、ログイン前にみていたページにリダイレクト
				$after_url_logined = $this->_Session->get(Scrv\SessionKeys::URL_AFTER_LOGINED);
				$this->_Session->clear(Scrv\SessionKeys::URL_AFTER_LOGINED);
				if ( !isset($after_url_logined) ){
					$after_url_logined = "Users/View/id/" . $auth_result["data"]["id"];
				}
				// ログインタイムアウト設定
				$timeout = isset(self::$_common_ini["session"]["session.gc_maxlifetime"])
									? (int)self::$_common_ini["session"]["session.gc_maxlifetime"]
									: 30 * 60;
				session_set_cookie_params($timeout);
				// セッションにログイン情報追加
				$this->_Session->init();
				$this->_Session->regenerate();
				$this->_Session->set(Scrv\SessionKeys::IS_LOGIN, true);
				$this->_Session->set(Scrv\SessionKeys::LOGIN_USER_DATA, $auth_result["data"]);
				$this->_Session->set(Scrv\SessionKeys::LOGIN_TIMEOUT, $timeout);
				$this->_Session->set(Scrv\SessionKeys::LOGIN_EXPIRES, $timeout + self::$_nowTimestamp);
				// リダイレクト
				Server::redirect($this->_BasePath . $after_url_logined);
			} else {
				$error = $code . ':' . $tmhOAuth->response['response'];
			}
		}
		// 初回アクセス時
		elseif ( $authenticate !== null ) {
			$code = $tmhOAuth->request('POST', "{$api_url}request_token", array(
				'oauth_callback' => $callback,
			));
			if ($code === 200) {
				$this->_Session->set(Scrv\SessionKeys::TWITTER_OAUTH, $tmhOAuth->extract_params($tmhOAuth->response['response']));
				$oauth = $this->_Session->get(Scrv\SessionKeys::TWITTER_OAUTH);
				$oauth_token = $oauth['oauth_token'];
				header("Location: {$api_url}authenticate?oauth_token={$oauth_token}");
			} else {
				$error = $code . ':' . $tmhOAuth->response['response'];
			}
		}
		return $error;
	}

}
