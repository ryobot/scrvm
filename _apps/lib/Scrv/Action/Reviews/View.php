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
		$own_user_id = isset($this->_login_user_data["id"]) ? $this->_login_user_data["id"] : null;
		$DaoReviews = new DaoReviews();
		$lists_result = $DaoReviews->viewById($id,$own_user_id);
		if ( !$lists_result["status"] ) {
			Server::send404Header("404 not found..");
			return false;
		}

		// fav review user lists
		$favreviews_user_lists_result = $DaoReviews->favReviewsUserLists($id);
		if ( !$favreviews_user_lists_result["status"] ) {
			Server::send404Header("404 not found...");
			return false;
		}

		$this->_Template->assign(array(
			"review" => $lists_result["data"],
			"favreviews_user_lists" => $favreviews_user_lists_result["data"],
			"_description" => "{$lists_result["data"]["artist"]}/{$lists_result["data"]["title"]} | {$lists_result["data"]["body"]} (by {$lists_result["data"]["username"]})",
		))->display("Reviews/View.tpl.php");
		return true;
	}
}
