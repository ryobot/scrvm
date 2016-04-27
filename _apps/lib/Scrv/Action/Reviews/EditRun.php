<?php
/**
 * /lib/Scrv/Action/Reviews/EditRun.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Util\Server as Server;

// XXX...
require_once __DIR__ . "/../../../Vender/tmhOAuth/tmhOAuth.php";

/**
 * Reviews EditRun class
 * @author mgng
 */
class EditRun extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// ログインしてなければログイン画面に
		$this->isNotLogined($this->_BasePath . "Auth");

		// POSTパラメータ取得、mb_trim、改行コード統一、セッション保持
		$post_params = array(
			"token" => Server::post("token", ""),
			"review_id" => Server::post("review_id", ""),
			"listening_last" => Server::post("listening_last", ""),
			"listening_system" => Server::post("listening_system", ""),
			"send_twitter" => Server::post("send_twitter"),
			"body" => Server::post("body", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}
		$this->_Session->set(Scrv\SessionKeys::POST_PARAMS, $post_params);

		// CSRFチェック, review_id チェック
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( $sess_token !== $post_params["token"]
			|| !ctype_digit($post_params["review_id"])
		) {
			Server::send404Header("system error.");
			return false;
		}

		// POST params チェック
		$check_result = $this->_checkPostParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Reviews/Add?id=" . urlencode($post_params["album_id"]));
			return false;
		}

		// 登録処理
		$DaoReviews = new DaoReviews();
		$add_result = $DaoReviews->edit(
			$this->_login_user_data["id"],
			$post_params["review_id"],
			$post_params["listening_last"],
			$post_params["listening_system"],
			$post_params["body"]
		);
		if ( !$add_result["status"] ){
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $add_result["messages"]);
			Server::redirect($this->_BasePath . "Reviews/Edit?id=" . urlencode($post_params["review_id"]));
			return false;
		}
		$album_data = $add_result["data"]["album_data"];

		// sendtwitter
		if ( isset($post_params["send_twitter"]) && $post_params["send_twitter"] === "1" ) {
			$this->_sendTwtter(
				$album_data["id"],
				$album_data["artist"],
				$album_data["title"],
				$post_params["body"]
			);
		}

		// Albums.View にリダイレクト
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
		Server::redirect($this->_BasePath . "Albums/View?id={$album_data["id"]}");

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

		if ( preg_match( "/\A(today|recently)\z/", $post_params["listening_last"] ) !== 1 ) {
			$check_result["messages"]["listening_last"] = "listening date が未入力です。";
		}
		if ( preg_match( "/\A(home|headphones|car|other)\z/", $post_params["listening_system"] ) !== 1 ) {
			$check_result["messages"]["listening_system"] = "listening system が未入力です。";
		}
		if ( mb_strlen($post_params["body"]) > 1000 ) {
			$check_result["messages"]["body"] = "review は1000文字以内で入力してください。";
		}

		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}

	/**
	 * sendTwitter
	 * @param string $album_id
	 * @param string $artist
	 * @param string $title
	 * @param string $body
	 * @return resuktSet
	 */
	private function _sendTwtter($album_id, $artist, $title, $body)
	{
		$result = getResultSet();
		$tmhOAuth = new \tmhOAuth( array(
			'consumer_key'    => $this->_common_ini["twitter"]['consumer_key'],
			'consumer_secret' => $this->_common_ini["twitter"]['consumer_secret'],
			'user_token'      => $this->_login_user_data["twitter_user_token"],
			'user_secret'     => $this->_login_user_data["twitter_user_secret"],
		) );

		$max_length = 140;
		$content = "{$artist}/{$title}\n{$body}";
		$hashtag = "#scrv";
		$perma_link = "";
//		$perma_link = Server::getFullHostUrl() . $this->_BasePath . "Albums/View?id={$album_id}";

		$status = "{$content}\n{$hashtag}\n{$perma_link}";
		$status_length = mb_strlen($status);
		if ( $status_length > $max_length ) {
			$sub_length = $max_length - $status_length;
			$content = mb_substr($content, 0, $sub_length - 3 ); // ちょっと余裕を持たせて
			$status = "{$content}…\n\n{$perma_link} {$hashtag}";
		}

		$code = $tmhOAuth->request('POST',"https://api.twitter.com/1.1/statuses/update.json",array(
			"include_entities" => "true",
			"status" => $status,
		));
		$res = $tmhOAuth->response['response'];
		$result["status"] = $code === 200;
		$result["data"] = array(
			"code" => $code,
			"response" => $res,
		);
		return $result;
	}
}
