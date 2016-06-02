<?php
/**
 * /lib/Scrv/Action/Rss/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Rss;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Reviews as DaoReviews;
use lib\Util\Server as Server;

/**
 * Rss Index class
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
		$page = "1";
		//$limit = (int)self::$_common_ini["search"]["limit"];
		$limit = 20;
		$offset = ((int)$page-1) * $limit;

		// レビュー一覧取得
		$DaoReviews = new DaoReviews();
		$lists_result = $DaoReviews->lists($offset, $limit, null);
		if ( !$lists_result["status"] ) {
			Server::send404Header("not found");
			return false;
		}

		// output
		header('Content-Type: application/xml; charset=utf-8');
		$this->_Template->assign(array(
			"reviews" => $lists_result["data"]["reviews"],
			"http_host" => Server::getFullHostUrl(),
		))->display("Rss/Index.tpl.php");
		return true;
	}
}
