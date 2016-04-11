<?php
/**
 * /lib/Scrv/Action/Reviews/AddRun.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
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
		$check_result = $this->_checkPostParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Reviews/Add?id=" . urlencode($post_params["album_id"]));
			return false;
		}

		// 登録処理
		$DaoReviews = new DaoReviews();
		$add_result = $DaoReviews->add(
			$this->_login_user_data["id"],
			$post_params["album_id"],
			$post_params["listening_last"],
			$post_params["listening_system"],
			$post_params["body"]
		);
		if ( !$add_result["status"] ){
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $add_result["messages"]);
			Server::redirect($this->_BasePath . "Reviews/Add?id=" . urlencode($post_params["album_id"]));
			return false;
		}

		// Reviewsにリダイレクト
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
		Server::redirect($this->_BasePath);

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
}
