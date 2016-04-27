<?php
/**
 * /lib/Scrv/Action/Tags/Del.php
 * @author mgng
 */

namespace lib\Scrv\Action\Tags;
use lib\Scrv as Scrv;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Tags as DaoTags;
use lib\Util\Server as Server;

/**
 * Tags Del class
 * @author mgng
 */
class Del extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインは404
		if(!$this->_is_login) {
			Server::send404Header("404 not found");
			return false;
		}

		// post params 取得, check
		$id = mb_trim(Server::post("id", ""));
		if ( !ctype_digit($id) ){
			Server::send404Header("system error!");
			return false;
		}

		// delete tag
		$DaoTags = new DaoTags();
		$tags_result = $DaoTags->del((int)$id, $this->_login_user_data["id"]);
		if ( !$tags_result["status"] ){
			$this->_Session->set(Scrv\SessionKeys::ERROR_MESSAGES, $tags_result["messages"]);
		}

		header("Content-Type:application:json; charset=utf-8");
		echo json_encode($tags_result);

		return true;
	}
}
