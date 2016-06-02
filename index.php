<?php
/**
 * index.php
 * @author mgng
 */

/**
 * http://hostname/scrv/アクション名/実行クラス名
 *	- アクション名が未定義 : common.ini[routing][default_action] を設定
 *	- 実行クラス名が未定義 : common.ini[routing][default_run] を設定
 */

require_once __DIR__ . "/_apps/require.php";
use lib\Util\Server as Server;

// 実行するインスタンスを設定して実行
$Router = new \lib\Scrv\Router();
$instance = $Router->getInstance();
if ( $instance === null ) {
	Server::send404Header("404 not found.");
	exit;
}
$instance->run();
exit;
