<?php
/**
 * lib/Scrv/Router.php
 * @author mgng
 */

namespace lib\Scrv;
use lib\Scrv\Base as Base;
use lib\Util\Server as Server;

/**
 * router class
 * @author mgng
 * @package lib\Scrv
 */
class Router extends Base
{
	/**
	 * $action と $run に応じたクラスインスタンスを返す。
	 * クラスインスタンスが存在しなければ null を返す。
	 * @param string $action
	 * @param string $run
	 * @return class instatce | null
	 */
	public function getInstance()
	{
		$instance = null;
		$parsed = $this->parseRequestUri();
		if ( $parsed === null ) {
			return $instance;
		}
		self::$_actionName = $parsed["action"];
		self::$_runClassName = $parsed["run"];
		$run_class_name = "\\lib\\Scrv\\Action\\{$parsed["action"]}\\{$parsed["run"]}";
		if (class_exists($run_class_name) ) {
			$instance = new $run_class_name;
		}
		return $instance;
	}

	public function parseRequestUri()
	{
		// ディフォルトルーティング
		$routing = array(
			"action" => self::$_common_ini["routing"]["default_action"],
			"run" => self::$_common_ini["routing"]["default_run"],
		);
		// セパレータ
		$sep = "/";
		// request_uri 取得
		$reqest_uri = Server::env("REQUEST_URI");
		// decode
		$reqest_uri = urldecode($reqest_uri);
		// base_path を除外
		$reqest_uri = preg_replace("/\A".preg_quote(self::$_common_ini["common"]["base_path"], $sep)."/u", "", $reqest_uri);
		// クエリ文字列を除外
		$reqest_uri = preg_replace("/\?.*/u", "", $reqest_uri);
		// 複数 / は1つに
		$reqest_uri = preg_replace("/(".preg_quote($sep, $sep)."+)/u", $sep, $reqest_uri);
		// 末尾に / があったら削除
		$reqest_uri = preg_replace("/".preg_quote($sep, $sep)."\z/u", "", $reqest_uri);
		// 空文字であればdefaultを返す
		if ( $reqest_uri === "" ) {
			return $routing;
		}

		// short url 対応
		$this->_runShortUrl($reqest_uri);

		// split
		$parsed = preg_split("/".preg_quote($sep, $sep)."/u", $reqest_uri);
		$action = $parsed[0];
		$run = isset($parsed[1]) ? $parsed[1] : $routing["run"];
		// pattern チェック
		$pattern = "/\A[a-zA-Z0-9_]+\z/";
		if ( preg_match($pattern, $action) !== 1 || preg_match($pattern, $run) !== 1 ) {
			return null;
		}
		// 設定
		$routing["action"] = $action;
		$routing["run"] = $run;
		// 3個め以降があってkeyがpatternに一致していればGETに上書き...
		if ( count($parsed) > 2 ) {
			for($i=2,$len=count($parsed); $i<$len; $i=$i+2) {
				$key = $parsed[$i];
				$val = isset($parsed[$i+1]) ? $parsed[$i+1] : null;
				if (preg_match($pattern, $key) === 1 && $val !== null ) {
					$_GET[$key] = $val;
				}
			}
		}
		return $routing;
	}

	/**
	 * run short url
	 * @param string $request_uri
	 * @return null
	 */
	private function _runShortUrl($request_uri)
	{
		$base = Server::getFullHostUrl() . self::$_common_ini["common"]["base_path"];
		// r/[digit] → Reviews/View/[digit]
		if ( preg_match("/\Ar\/([0-9]+)\z/", $request_uri, $match) === 1 ) {
			$url = "{$base}Reviews/View/id/". (int)$match[1];
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: {$url}");
			exit;
		}
		// u/[digit] → Users/View/[digit]
		if ( preg_match("/\Au\/([0-9]+)\z/", $request_uri, $match) === 1 ) {
			$url = "{$base}Users/View/id/". (int)$match[1];
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: {$url}");
			exit;
		}
		// a/[digit] → Albums/View/[digit]
		if ( preg_match("/\Aa\/([0-9]+)\z/", $request_uri, $match) === 1 ) {
			$url = "{$base}Albums/View/id/". (int)$match[1];
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: {$url}");
			exit;
		}
		return null;
	}

}
