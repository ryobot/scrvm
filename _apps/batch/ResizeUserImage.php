<?php
/**
 * /lib/Scrv/Batch/ResizeUserImage.php
 * @author mgng
 */

// ユーザ画像を300x300にリサイズ
require_once __DIR__ . "/../../_apps/require.php";

$start = microtime(true);

// user一覧取得
$Images = new \lib\Util\Images();
$DaoUsers = new \lib\Scrv\Dao\Users();
$result = $DaoUsers->lists(0, 100);
foreach($result["data"]["lists"] as $row){

	if ( $row["img_file"] === null || $row["img_file"] === "" ) {
		continue;
	}

	// path 生成
	$img_path = __DIR__ . "/../../files/attachment/photo/" . $row["img_file"];
	if ( ! is_file($img_path) ) {
		continue;
	}

	// 大きい辺が300px以下であれば何もしない
	$imageinfo = getimagesize($img_path);
	$max_size = $imageinfo[0] < $imageinfo[1] ? $imageinfo[1] : $imageinfo[0];
	if ($max_size <= 300) {
		continue;
	}

	// リサイズ実行
	$resize_result = $Images->makeThumbnail($img_path, $img_path, 300, 300, true);
	var_dump($resize_result);
}

$end = microtime(true);

echo ($end - $start) . " sec\n";
exit;
