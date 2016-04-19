<?php
/**
 * /lib/Scrv/Action/Posts/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Posts;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Posts as DaoPosts;
use lib\Util\Password as Password;
use lib\Util\Server as Server;
use lib\Util\Pager as Pager;

/**
 * Posts 画面表示クラス
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
		// セッション値取得
		$post_params = $this->_Session->get(Scrv\SessionKeys::POST_PARAMS);
		$error_messages = $this->_Session->get(Scrv\SessionKeys::ERROR_MESSAGES);
		$this->_Session->clear(Scrv\SessionKeys::ERROR_MESSAGES);

		// token生成、セッションに保持
		$Password = new Password();
		$token = $Password->makeRandomHash($this->_Session->id());
		$this->_Session->set(Scrv\SessionKeys::CSRF_TOKEN, $token);

		$page = Server::get("page", "1");
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		$limit = (int)$this->_common_ini["search"]["limit"];
		$offset = ((int)$page-1) * $limit;

		// 一覧取得
		$DaoPosts = new DaoPosts();
		$lists_result = $DaoPosts->lists($offset, $limit);
		if ( ! $lists_result["status"] ) {
			Server::send404Header("db error.");
			print_r($lists_result);
			return false;
		}

		// pager
		$Pager = new Pager();

		$this->_Template->assign(array(
			"post_params" => $post_params,
			"error_messages" => $error_messages,
			"token" => $token,
			"lists" => $lists_result["data"]["lists"],
			"lists_count" => $lists_result["data"]["lists_count"],
			"pager" => $Pager->getPager((int)$page, $lists_result["data"]["lists_count"], $limit, 5),
		))->display("Posts/Index.tpl.php");

		return true;
	}

}
