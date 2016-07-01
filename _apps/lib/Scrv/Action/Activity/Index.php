<?php
/**
 * /lib/Scrv/Action/Activity/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Activity;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Activity as DaoActivity;
use lib\Util\Server as Server;

/**
 * Activity Index class
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
		// ログインしてなければログイン画面に
		$this->isNotLogined($this->_BasePath . "Auth");

		// activity 一覧を取得
		$DaoActivity = new DaoActivity();
		$activity_result = $DaoActivity->getLists(1);	// XXX 日付
		if ( ! $activity_result["status"]) {
			Server::send404Header("system error.");
			return false;
		}
		$lists = $activity_result["data"];

		// Etag用ハッシュ取得
		$etag = $this->_Template->getEtag(print_r($lists, 1));
		// キャッシュヘッダとETagヘッダ出力
		header("Cache-Control: max-age=60");
		header("ETag: {$etag}");
		// etagが同じなら304
		$client_etag = Server::env("HTTP_IF_NONE_MATCH");
		if ( $etag === $client_etag ) {
			header( 'HTTP', true, 304 );
			return true;
		}

		// created 降順に並べる
		$sort_keys_created = array();
		foreach ($lists as $key => $row) {
			$sort_keys_created[$key]  = $row['created'];
		}
		array_multisort($sort_keys_created, SORT_DESC, $lists);

		$this->_Template->assign(array(
			"lists" => $lists,
		))->display("Activity/Index.tpl.php");
		return true;
	}

}
