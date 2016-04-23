<?php
/**
 * Users/FavTracks.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Users/FavTracks?id={$user_id}";
$most_prev_link = "{$_base_url}";
$prev_link = "{$_base_url}&" . hbq(array("page" => $pager["now_page"]-1,));
$next_link = "{$_base_url}&" . hbq(array("page" => $pager["now_page"]+1,));
$most_next_link = "{$_base_url}&" . hbq(array("page" => $pager["max_page"],));
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$_base_url}&" . hbq(array("page" => $nav["page"],)),
	);
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

	<table class="w100per every_other_row_odd">
		<tr>
			<td class="w80px tacenter vtalgmiddle">
				<img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" />
			</td>
			<td class="user_menu_list">
				<ul>
					<li class="reviews"><a href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a></li>
					<li class="fav_tracks"><a href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a></li>
					<li class="fav_albums"><a href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a></li>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
					<li class="syncs"><a href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a></li>
<?php endif;?>
				</ul>
			</td>
		</tr>
	</table>

	<h3>Fav.Tracks (<?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?>)</h3>

<?php if(count($favtracks) > 0): ?>

	<div class="tacenter">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

	<table class="w100per every_other_row_odd">
<?php foreach($favtracks as $favtrack): ?>
		<tr>
			<td class="w80px">
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($favtrack["album_id"]) ?>"><img class="album_cover" src="<?= isset($favtrack["img_file"])? "{$base_path}files/covers/{$favtrack["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></a>
			</td>
			<td>
				<div><strong><?= h($favtrack["track_title"]) ?></strong></div>
				<div><a href="<?= h($base_path) ?>Albums/View?id=<?= h($favtrack["album_id"]) ?>"><?= h($favtrack["artist"]) ?> / <?= h($favtrack["title"]) ?></a> : tr.<?= h($favtrack["track_num"]) ?></div>
			</td>
		</tr>
<?php endforeach; ?>
	</table>

	<div class="tacenter">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>


<?php endif; ?>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>