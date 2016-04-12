<?php
/**
 * Users/FavAlbums.tpl.php
 * @author mgng
 */

$prev_link = $base_path . "Users/FavAlbums?id={$user_id}&" . http_build_query(array(
	"offset" => $pager["offset"]-$pager["limit"],
));
$next_link = $base_path . "Users/FavAlbums?id={$user_id}&" . http_build_query(array(
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
<title><?= h($base_title) ?> :: Users :: FavAlbums</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2><?= h($user["username"]) ?></h2>

	<div class="lists">
		<table><tbody>
			<tr>
				<td><img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" /></td>
				<td>
					Reviews : <a href="<?= h($base_path) ?>Users/View?id=<?= h($user_id) ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a><br />
					Fav.Tracks : <a href="<?= h($base_path) ?>Users/FavTracks?id=<?= h($user_id) ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a><br />
					Fav.Albums : <a href="<?= h($base_path) ?>Users/FavAlbums?id=<?= h($user_id) ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a><br />
			</tr>
		</tbody></table>
	</div>

	<h3>Fav.Albums (<?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?>)</h3>

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

	<table>
<?php foreach($favalbums as $favalbum): ?>
		<tr>
			<td><img class="album_search_cover_result" src="<?= isset($favalbum["img_file"])? "{$base_path}files/covers/{$favalbum["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></td>
			<td>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($favalbum["album_id"]) ?>"><?= h($favalbum["artist"]) ?> / <?= h($favalbum["title"]) ?></a>
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

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>