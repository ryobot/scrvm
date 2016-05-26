<?php
/**
 * /lib/Scrv/Batch/SyncPointUpdate.php
 * @author mgng
 */

// ユーザ全体のポイント更新一括バッチ
// cliから実行

require_once __DIR__ . "/../../_apps/require.php";

$start = microtime(true);
$DaoSyncPoints = new \lib\Scrv\Dao\SyncPoints();
$result = $DaoSyncPoints->updateSyncPoints();
$end = microtime(true);

echo ($end - $start) . " sec\n";
print_r($result);
exit;
