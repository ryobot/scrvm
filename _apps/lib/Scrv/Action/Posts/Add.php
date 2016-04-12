<?php
/**
 * /lib/Scrv/Action/Posts/Add.php
 * @author mgng
 */

namespace lib\Scrv\Action\Posts;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;
use lib\Scrv\Dao\Posts as DaoPosts;

/**
 * Posts Add class
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
		// ログインしてなければログイン画面に
		$this->isNotLogined($this->_BasePath . "Auth");

		// POSTパラメータ取得、mb_trim、改行コード統一、セッション保持
		$post_params = array(
			"token" => Server::post("token", ""),
			"title" => Server::post("title", ""),
			"body" => Server::post("body", ""),
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

		// post_params チェック
		$check_result = $this->_checkPostParams($post_params);
		if ( ! $check_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
			Server::redirect($this->_BasePath . "Posts");
			return false;
		}

		// 登録処理
		$DaoPosts = new DaoPosts();
		$add_result = $DaoPosts->add(
			$post_params["title"],
			$post_params["body"],
			isset( $this->_login_user_data["id"] ) ? $this->_login_user_data["id"] : 0,
			0
		);
		if ( ! $add_result["status"] ) {
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $add_result["messages"]);
			Server::redirect($this->_BasePath . "Posts");
			return false;
		}

		// セッションのpost_param をクリアしてリダイレクト
		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
		Server::redirect($this->_BasePath . "Posts");
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

		if ( $post_params["title"] === "" ) {
			$check_result["messages"]["title"] = "title が未入力です。";
		} else if ( mb_strlen($post_params["title"]) > 50 ){
			$check_result["messages"]["title"] = "title は50文字以内で入力してください";
		}

		if ( $post_params["body"] === "" ) {
			$check_result["messages"]["body"] = "body が未入力です。";
		} else if ( mb_strlen($post_params["body"]) > 1000 ){
			$check_result["messages"]["body"] = "body は1000文字以内で入力してください";
		}

		$check_result["status"] = count($check_result["messages"]) === 0;
		return $check_result;
	}

}
