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
		$offset = Server::get("offset", "0");
		if ( ! ctype_digit($offset) ) {
			$offset = "0";
		}
		$limit = $this->_common_ini["search"]["limit"];

		// レビュー一覧取得
		$DaoReviews = new DaoReviews();
		$lists_result = $DaoReviews->lists($offset, $limit);
		if ( !$lists_result["status"] ) {
			Server::send404Header("not found");
			return false;
		}

		// pager
		$Pager = new Pager();

		$this->_Template->assign(array(
			"reviews" => $lists_result["data"]["reviews"],
			"reviews_count" => $lists_result["data"]["reviews_count"],
			"pager" => $Pager->getPager($offset, $limit, $lists_result["data"]["reviews_count"]),
		))->display("Reviews/Index.tpl.php");
		return true;
	}
}
