<?php
/**
 * /lib/Scrv/Action/Reviews/Fav.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Util\Server as Server;

/**
 * Reviews fav class
 * @author mgng
 */
class Fav extends Base
{
	/**
	 * 実行クラス
	 * @return boolean
	 */
	public function run()
	{
		// 未ログインはエラー
		if ( ! $this->_is_login ) {
			Server::send404Header();
			return false;
		}

		$review_id = Server::post("review_id", "");
		if (!ctype_digit($review_id)) {
			Server::send404Header();
			return false;
		}

		// json header
		header("Content-Type:application/json; charset=utf-8");

		// favreviews更新
		$DaoReviews = new DaoReviews();
		$fav_result = $DaoReviews->fav((int)$review_id, $this->_login_user_data["id"]);
		if ( ! $fav_result["status"] ) {
			echo json_encode($fav_result);
			return false;
		}

		echo json_encode(array(
			"status" => true,
			"data" => array(
				"operation" => $fav_result["data"]["operation"],
				"fav_count" => $fav_result["data"]["fav_count"],
			),
		));

		return true;
	}
}
