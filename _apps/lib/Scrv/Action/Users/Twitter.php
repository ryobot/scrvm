<?php
/**
 * /lib/Scrv/Action/Users/Twitter.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Users as DaoUsers;
use lib\Util\Server as Server;

// XXX...
require_once __DIR__ . "/../../../Vender/tmhOAuth/tmhOAuth.php";

/**
 * Users Twitter class
 * @author mgng
 */
class Twitter extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはエラー
		if (!$this->_is_login) {
			Server::send404Header("404 not found.");
			return false;
		}

		$authenticate = Server::post("authenticate");
		$is_ok_url = Server::getFullHostUrl() . "{$this->_BasePath}Users/Edit?id={$this->_login_user_data["id"]}";

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
			'consumer_key' => $this->_common_ini["twitter"]['consumer_key'],
			'consumer_secret' => $this->_common_ini["twitter"]['consumer_secret'],
		) );

		$api_url = "https://api.twitter.com/oauth/";
		$callback = Server::selfUrl();
		$error = "";

		// session.access_token data
		$access_token = $this->_Session->get(Scrv\SessionKeys::TWITTER_ACCESS_TOKEN);
		if ($access_token === null) {
			$access_token = array();
		}
		$access_token["update_timestamp"] = $this->_nowTimestamp;

		// oauth_verifier param
		$oauth_verifier = Server::get("oauth_verifier");

		$DaoUsers = new DaoUsers();

		// session.access_token.user_idがあれば
		if ( isset( $access_token["user_id"] ) ) {
			$DaoUsers->saveTwitter(
				$this->_login_user_data["id"],
				$access_token["user_id"],
				$access_token["oauth_token"],
				$access_token["oauth_token_secret"]
			);
		}
		// 未ログイン時
		elseif ( $oauth_verifier !== null ) {
			$this->_Session->regenerate();
			$oauth = $this->_Session->get(Scrv\SessionKeys::TWITTER_OAUTH);
			$tmhOAuth->config['user_token'] = $oauth['oauth_token'];
			$tmhOAuth->config['user_secret'] = $oauth['oauth_token_secret'];
			$code = $tmhOAuth->request('POST', "{$api_url}access_token", array(
				'oauth_verifier' => $oauth_verifier,
			));
			if ($code === 200) {
				$this->_Session->set(Scrv\SessionKeys::TWITTER_ACCESS_TOKEN, $tmhOAuth->extract_params($tmhOAuth->response['response']));
				$this->_Session->clear(Scrv\SessionKeys::TWITTER_OAUTH);

				$access_token = $this->_Session->get(Scrv\SessionKeys::TWITTER_ACCESS_TOKEN);
				$DaoUsers->saveTwitter(
					$this->_login_user_data["id"],
					$access_token["user_id"],
					$access_token["oauth_token"],
					$access_token["oauth_token_secret"]
				);

				header("Location: {$is_ok_url}");
			} else {
				$error = $code . ':' . $tmhOAuth->response['response'];
			}
		} elseif ( $authenticate !== null ) {
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
