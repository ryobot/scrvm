<?php
/**
 * /lib/Scrv/Action/Reviews/AddRun.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Scrv\Helper\Reviews\Check as ReviewsCheck;
use lib\Scrv\Helper\Reviews\PostTwitter as PostTwitter;
use lib\Util\Server as Server;

/**
 * Reviews AddRun class
 * @author mgng
 */
class AddRun extends Base
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
			"album_id" => Server::post("album_id", ""),
			"listening_last" => Server::post("listening_last", ""),
			"listening_system" => Server::post("listening_system", ""),
			"send_twitter" => Server::post("send_twitter"),
			"published" => Server::post("published", "0"),
			"body" => Server::post("body", ""),
		);
		foreach( $post_params as &$val ) {
			$val = convertEOL(mb_trim($val), "\n");
		}
		$this->_Session->set(Scrv\SessionKeys::POST_PARAMS, $post_params);

		// CSRFチェック, $album_id チェック
		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
		if ( $sess_token !== $post_params["token"]
			|| !ctype_digit($post_params["album_id"])
		) {
			Server::send404Header("system error.");
			return false;
		}

		// POST params チェック
		$ReviewsCheck = new ReviewsCheck();
		$check_result = $ReviewsCheck->postParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Reviews/Add/id/" . urlencode($post_params["album_id"]));
			return false;
		}

		// 登録処理
		$DaoReviews = new DaoReviews();
		$add_result = $DaoReviews->add(
			$this->_login_user_data["id"],
			$post_params["album_id"],
			$post_params["listening_last"],
			$post_params["listening_system"],
			(int)$post_params["published"],
			$post_params["body"]
		);
		if ( !$add_result["status"] ){
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $add_result["messages"]);
			Server::redirect($this->_BasePath . "Reviews/Add/id/" . urlencode($post_params["album_id"]));
			return false;
		}

		// post twitter
		if ( isset($post_params["send_twitter"]) && $post_params["send_twitter"] === "1" ) {
			$PostTwitter = new PostTwitter();
			$PostTwitter->run(
				$post_params["album_id"],
				$add_result["data"]["album_data"]["artist"],
				$add_result["data"]["album_data"]["title"],
				$add_result["data"]["posted_review_id"],	// 登録したreview ID
				$post_params["body"]
			);
		}

		// alubums.viewにリダイレクト
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
		Server::redirect($this->_BasePath . "Albums/View/id/{$post_params["album_id"]}");

		return true;
	}

}
