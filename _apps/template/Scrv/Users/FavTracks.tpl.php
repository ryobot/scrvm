<?php
/**
 * Users/FavTracks.tpl.php
 * @author mgng
 */

$prev_link = $base_path . "Users/FavTracks?id={$user_id}&" . http_build_query(array(
	"offset" => $pager["offset"]-$pager["limit"],
));
$next_link = $base_path . "Users/FavTracks?id={$user_id}&" . http_build_query(array(
	"offset" => $pager["offset"]+$pager["limit"],
));
if($pager["offset"]-$pager["limit"] < 0){
	$prev_link = "";
}
if($pager["offset"]+$pager["limit"] >= $pager["total_count"]){
	$next_link = "";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Users :: FavTracks</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2><?= h($user["username"]) ?></h2>

	<div class="lists">
		<table>
			<tr>
				<td><img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" /></td>
				<td>
					Reviews    : <a href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a><br />
					Fav.Tracks : <a href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a><br />
					Fav.Albums : <a href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a><br />
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
					Syncs      : <a href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a>
<?php endif;?>
				</td>
			</tr>
		</table>
	</div>

	<h3>Fav.Tracks (<?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?>)</h3>

<?php if(count($favtracks) > 0): ?>

	<p class="pager">
<?php if($prev_link !== ""): ?>
		<a href="<?= h($prev_link) ?>">≪prev</a>
<?php else:?>
		<span>≪prev</span>
<?php endif;?>
		<?= h($pager["now_page"]) ?> / <?= h($pager["max_page"]) ?>
<?php if($next_link !== ""): ?>
		<a href="<?= h($next_link) ?>">next≫</a>
<?php else:?>
		<span>next≫</span>
<?php endif;?>
	</p>

	<table class="w100per every_other_row_odd">
<?php foreach($favtracks as $favtrack): ?>
		<tr>
			<td><img class="album_search_cover_result" src="<?= isset($favtrack["img_file"])? "{$base_path}files/covers/{$favtrack["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></td>
			<td>
				<div><strong><?= h($favtrack["track_title"]) ?></strong></div>
				<div><a href="<?= h($base_path) ?>Albums/View?id=<?= h($favtrack["album_id"]) ?>"><?= h($favtrack["artist"]) ?> / <?= h($favtrack["title"]) ?></a> : tr.<?= h($favtrack["track_num"]) ?></div>
			</td>
		</tr>
<?php endforeach; ?>
	</table>

	<p class="pager">
<?php if($prev_link !== ""): ?>
		<a href="<?= h($prev_link) ?>">≪prev</a>
<?php else:?>
		<span>≪prev</span>
<?php endif;?>
		<?= h($pager["now_page"]) ?> / <?= h($pager["max_page"]) ?>
<?php if($next_link !== ""): ?>
		<a href="<?= h($next_link) ?>">next≫</a>
<?php else:?>
		<span>next≫</span>
<?php endif;?>
	</p>

<?php endif; ?>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>