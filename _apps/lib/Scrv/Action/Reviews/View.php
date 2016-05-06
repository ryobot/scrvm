<?php
/**
 * /lib/Scrv/Action/Reviews/View.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Util\Server as Server;

/**
 * Reviews.View class
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
		$id = Server::get("id");
		if ( !isset($id) || !ctype_digit($id) ) {
			Server::send404Header("404 not found.");
			return false;
		}

		// レビュー取得
		$DaoReviews = new DaoReviews();
		$lists_result = $DaoReviews->viewById($id);
		if ( !$lists_result["status"] ) {
			Server::send404Header("404 not found..");
			return false;
		}

		$this->_Template->assign(array(
			"review" => $lists_result["data"],
		))->display("Reviews/View.tpl.php");
		return true;
	}
}
