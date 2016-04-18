<?php
/**
 * /lib/Scrv/Action/Users/AddNew.php
 * @author mgng
 */

namespace lib\Scrv\Action\Users;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Util\Server as Server;
use lib\Scrv\Dao\Users as DaoUsers;

/**
 * Posts Add class
 * @author mgng
 */
class AddNew extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
//		// POSTパラメータ取得、mb_trim、改行コード統一、セッション保持
//		$post_params = array(
//			"token" => Server::post("token", ""),
//			"username" => Server::post("username", ""),
//			"password" => Server::post("password", ""),
//		);
//		foreach( $post_params as &$val ) {
//			$val = convertEOL(mb_trim($val), "\n");
//		}
//		$this->_Session->set(Scrv\SessionKeys::POST_PARAMS, $post_params);
//
//		// CSRFチェック
//		$sess_token = $this->_Session->get(Scrv\SessionKeys::CSRF_TOKEN);
//		if ( $sess_token !== $post_params["token"] ) {
//			Server::send404Header("system error.");
//			return false;
//		}
//
//		// post_params チェック
//		$check_result = $this->_checkPostParams($post_params);
//		if ( ! $check_result["status"] ) {
//			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $check_result["messages"]);
//			Server::redirect($this->_BasePath . "Users/Add");
//			return false;
//		}
//
//		// 登録処理
//		$DaoUsers = new DaoUsers();
//		$add_result = $DaoUsers->addNew(
//			$post_params["username"],
//			$post_params["password"],
//			"author"	// 管理者は admin, 通常は author
//		);
//		if ( ! $add_result["status"] ) {
//			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $add_result["messages"]);
//			Server::redirect($this->_BasePath . "Users/Add");
//			return false;
//		}
//
//		// セッションのpost_param をクリアしてリダイレクト
//		$this->_Session->clear(Scrv\SessionKeys::POST_PARAMS);
//		Server::redirect($this->_BasePath . "Users");
		Server::send404Header("404 not found.");
		return true;
	}

//	/**
//	 * post params check
//	 * @param array $post_params
//	 * @return resultSet
//	 */
//	private function _checkPostParams(array $post_params)
//	{
//		$check_result = getResultSet();
//
//		if ( $post_params["username"] === "" ) {
//			$check_result["messages"]["username"] = "username が未入力です。";
//		} else if ( mb_strlen($post_params["username"]) > 50 ){
//			$check_result["messages"]["username"] = "username は50文字以内で入力してください。";
//		}
//
//		if ( $post_params["password"] === "" ) {
//			$check_result["messages"]["password"] = "password が未入力です。";
//		} else if ( mb_strlen($post_params["password"]) > 100 ){
//			$check_result["messages"]["password"] = "password は100文字以内で入力してください";
//		}
//
//		$check_result["status"] = count($check_result["messages"]) === 0;
//		return $check_result;
//	}

}
