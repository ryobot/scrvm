<?php
/**
 * /lib/Scrv/Action/Reviews/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Reviews;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Util\Server as Server;
use lib\Util\Pager as Pager;

/**
 * トップ画面表示処理クラス
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
		// offset設定
		$page = Server::get("page", "1");
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		$limit = (int)self::$_common_ini["search"]["limit"];
		$offset = ((int)$page-1) * $limit;

		// レビュー一覧取得
		$my_user_id = isset($this->_login_user_data) ? $this->_login_user_data["id"] : null;
		$DaoReviews = new DaoReviews();
		$lists_result = $DaoReviews->lists($offset, $limit, $my_user_id);
		if ( !$lists_result["status"] ) {
			Server::send404Header("not found");
			return false;
		}

		// pager
		$Pager = new Pager();

		$this->_Template->assign(array(
			"reviews" => $lists_result["data"]["reviews"],
			"reviews_count" => $lists_result["data"]["reviews_count"],
			"pager" => $Pager->getPager((int)$page, $lists_result["data"]["reviews_count"], $limit, 5),
		))->display("Reviews/Index.tpl.php");
		return true;
	}
}
