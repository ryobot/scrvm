<?php
/**
 * Users/Syncs.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Users :: Syncs</title>
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
					Reviews    : <a href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a><br />
					Fav.Tracks : <a href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a><br />
					Fav.Albums : <a href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a><br />
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
					Syncs      : <a href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a>
<?php endif;?>
			</tr>
		</tbody></table>
	</div>

	<h3>Syncs : <?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?>pt</h3>
	<h4>Sync Reviews</h4>
	<h4>Sync Albums</h4>
	<h4>Sync Tracks</h4>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>