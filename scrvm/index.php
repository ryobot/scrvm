<?php
/**
 * index.php
 * @author mgng
 */

/**
 * http://hostname/scrv/アクション名/実行クラス名
 * http://hostname/scrv/?_act=アクション名&_run=実行クラス
 *	- ac1が未定義 : Index を設定
 *	- ac2が未定義 : Index を設定
 */

require_once __DIR__ . "/_apps/require.php";
use lib\Util\Server as Server;

// GETパラメータの _act, _runを取得、ディフォルトは Reviews, Index
$act = Server::get( "_act", "Reviews" );
$run = Server::get( "_run", "Index" );

// 実行するインスタンスを設定して実行
$Router = new \lib\Scrv\Router();
$instance = $Router->getInstance($act, $run);
if ( $instance === null ) {
	Server::send404Header("404 not found");
	exit;
}
$instance->run();

exit;
