<?php
/**
 * /lib/Scrv/Action/Reviews/Del.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Util\Server as Server;

/**
 * Reviews Delete class
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
		// ログインしてなければ404
		if ( ! $this->_is_login ) {
			Server::send404Header("not found.");
			return false;
		}

		// 削除対象ID
		$review_id = Server::post("id", "");
		if (!ctype_digit($review_id)){
			Server::send404Header("not found..");
			return false;
		}

		// 削除処理
		$DaoReviews = new DaoReviews();
		$add_result = $DaoReviews->del($this->_login_user_data["id"], $review_id);
		if ( !$add_result["status"] ){
			Server::send404Header("system error.");
			return false;
		}

		header("Content-Type:application/json; charset=utf-8");
		echo json_encode($add_result);

		return true;
	}
}
