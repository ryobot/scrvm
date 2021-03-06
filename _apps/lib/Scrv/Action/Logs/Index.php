<?php
/**
 * /lib/Scrv/Action/Logs/Index.php
 * @author mgng
 */

namespace lib\Scrv\Action\Logs;
use lib\Scrv\Action\Base as Base;
use lib\Scrv\Dao\Logs as DaoReviews;
use lib\Scrv\Dao\Users as DaoUsers;
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
		// hashtag取得
		$hash = Server::get("hash", "");
		$hash = mb_trim($hash);
		if ( $hash === "" ) {
			$hash = null;
		}

		// situation
		$situation = mb_trim(Server::get("situation", ""));
		if ( $situation === "" ) {
			$situation = null;
		}

		// select user
		$select_user = mb_trim(Server::get("user", ""));
		if ( $select_user === "" ) {
			$select_user = null;
			$user_result = null;
		} else {
                                            $DaoUsers = new DaoUsers();
                                            $user_result = $DaoUsers->view((int)$select_user);
                                    }

		// offset設定
		$page = Server::get("page", "1");
		if ( ! ctype_digit($page) ) {
			$page = "1";
		}
		//$limit = (int)self::$_common_ini["search"]["limit"];
		$limit = 100;
		$offset = ((int)$page-1) * $limit;

		// レビュー一覧取得
		$my_user_id = isset($this->_login_user_data) ? $this->_login_user_data["id"] : null;
		$DaoReviews = new DaoReviews();
		//$lists_result = $DaoReviews->lists($offset, $limit, $my_user_id, $hash, $situation);
		$lists_result = $DaoReviews->lists($offset, $limit, $my_user_id, $hash, $situation, $select_user);
		if ( !$lists_result["status"] ) {
			Server::send404Header("not found");
			return false;
		}

		// Etag用ハッシュ取得
		$etag = $this->_Template->getEtag(print_r($lists_result, 1));
		// キャッシュヘッダとETagヘッダ出力
		header("Cache-Control: max-age=60");
		header("ETag: {$etag}");
		// etagが同じなら304
		$client_etag = Server::env("HTTP_IF_NONE_MATCH");
		if ( $etag ===  $client_etag) {
			header( 'HTTP', true, 304 );
			return true;
		}

		// pager
		$params = array();
		if ( $hash !== "" ) {
			$params["hash"] = $hash;
		}
		if ( $situation !== "" ) {
			$params["situation"] = $situation;
		}
		if ( $select_user !== "" ) {
			$params["user"] = $select_user;
		}
		$Pager = new Pager();
		$pager = $Pager->getPager((int)$page, $lists_result["data"]["reviews_count"], $limit, 5);
		$_base_path = $this->_BasePath . "Logs/Index/";
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
		if ( $user_result ) {
                    $user_result_data = $user_result["data"];
                } else {
                    $user_result_data = null;
                }
		$this->_Template->assign(array(
			"reviews" => $lists_result["data"]["reviews"],
			"reviews_count" => $lists_result["data"]["reviews_count"],
			"pager" => $pager,
			"most_prev_link" => $most_prev_link,
			"prev_link" => $prev_link,
			"next_link" => $next_link,
			"most_next_link" => $most_next_link,
			"nav_list" => $nav_list,
			"hash" => $hash,
			"situation" => $situation,
			"select_user" => $user_result_data,
			"_description" => "生活に音楽が欠かせない全ての人へ。聴いて、記録して、誰かとSyncする。音楽の新しい楽しみ方がここにあります。",
		))->display("Logs/Index.tpl.php");
		return true;
	}
}
