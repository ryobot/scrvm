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
		// キャッシュ設定
		$cache_setting = array(
			"created" => self::$_nowTimestamp,
			"expire" => 3, // 3秒キャッシュ
			"request_uri" => Server::env("REQUEST_URI"),
		);
		$cache_contents = $this->_Template->getCache( $cache_setting["request_uri"]);
		if ( $cache_contents ) {
			header("X-Cached-Contents: " . $cache_setting["expire"]);
			echo $cache_contents;
			return true;
		}

		// hashtag取得
		$hash = Server::get("hash", "");
		$hash = mb_trim($hash);
		if ( $hash === "" ) {
			$hash = null;
		}

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
		$lists_result = $DaoReviews->lists($offset, $limit, $my_user_id, $hash);
		if ( !$lists_result["status"] ) {
			Server::send404Header("not found");
			return false;
		}

		// pager
		$params = array();
		if ( $hash !== "" ) {
			$params["hash"] = $hash;
		}
		$Pager = new Pager();
		$pager = $Pager->getPager((int)$page, $lists_result["data"]["reviews_count"], $limit, 5);
		$_base_path = $this->_BasePath . "Reviews/Index/";
		$most_prev_link = $_base_path;

		$prev_link = $_base_path . hbq2( array_merge(array("page" => $pager["now_page"]-1), $params) );
		$next_link = $_base_path . hbq2( array_merge(array("page" => $pager["now_page"]+1), $params) );
		$most_next_link = $_base_path . hbq2( array_merge(array("page" => $pager["max_page"]), $params) );
		$nav_list = array();
		foreach($pager["nav_list"] as $nav) {
			$nav_list[] = array(
				"active" => $nav["active"],
				"page" => $nav["page"],
				"link" => $_base_path . hbq2( array_merge(array("page" => $nav["page"]), $params) ),
			);
		}

		$this->_Template->setCache($cache_setting)->assign(array(
			"reviews" => $lists_result["data"]["reviews"],
			"reviews_count" => $lists_result["data"]["reviews_count"],
			"pager" => $pager,
			"most_prev_link" => $most_prev_link,
			"prev_link" => $prev_link,
			"next_link" => $next_link,
			"most_next_link" => $most_next_link,
			"nav_list" => $nav_list,
			"hash" => $hash,
			"_description" => "生活に音楽が欠かせない全ての人へ。聴いて、記録して、誰かとSyncする。音楽の新しい楽しみ方がここにあります。",
		))->display("Reviews/Index.tpl.php");
		return true;
	}
}
