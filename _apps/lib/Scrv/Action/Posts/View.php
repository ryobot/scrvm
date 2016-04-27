<?php
/**
 * /lib/Scrv/Action/Posts/View.php
 * @author mgng
 */

namespace lib\Scrv\Action\Posts;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Posts as DaoPosts;
use lib\Util\Server as Server;

/**
 * Posts View class
 * @author mgng
 */
class View extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		$type = Server::get("type", "");
		$id = Server::get("id", "");
		if ( ! ctype_digit($id) ) {
			Server::send404Header("404 not found");
			return false;
		}

		$DaoPosts = new DaoPosts();
		$view_result = $DaoPosts->view((int)$id);
		if ( ! $view_result["status"] ) {
			Server::send404Header("404 not found");
			return false;
		}

		if ( $type === "json" ) {
			foreach($view_result["data"] as $key=>&$val) {
				if ($key === "created") {
					$val = timeAgoInWords($val);
				}
				$val = nl2br(h($val));
			}
			header("Content-Type:application/json; charset=utf-8");
			echo json_encode($view_result["data"]);
			return true;
		}

		$this->_Template->assign(array(
			"id" => $id,
			"post" => $view_result["data"],
		))->display("Posts/View.tpl.php");
		return true;
	}

}
