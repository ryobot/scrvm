<?php
/**
 * /lib/Scrv/Action/Auth/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Auth;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Util\Password as Password;
use lib\Util\Server as Server;

/**
 * ログイン画面表示処理クラス
 * @author mgng
 */
class Index extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// ログインしていたらTOPリダイレクト
		$this->isLogined($this->_BasePath);

		// ログイン後の戻り先をリファラから設定
		$ref= Server::env("HTTP_REFERER");
		$base_full_url = Server::getFullHostUrl(). $this->_BasePath;
		if ( isset($ref)
			&& strpos($ref, $base_full_url) === 0
			&& strpos($ref, "{$base_full_url}Auth") !== 0
		) {
			$path = str_replace($base_full_url, "", $ref);
			$this->_Session->set(Scrv\SessionKeys::URL_AFTER_LOGINED, $path);
		} else {
			$this->_Session->clear(Scrv\SessionKeys::URL_AFTER_LOGINED);
		}

		// セッション値取得
		$post_params = $this->_Session->get(Scrv\SessionKeys::POST_PARAMS);
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		$this->_Template->assign(array(
			"post_params" => $post_params,
			"error_messages" => $error_messages,
			"token" => $token,
		))->display("Auth/Index.tpl.php");
		return true;
	}

}
