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

	<table class="w100per every_other_row_odd">
		<tr>
			<td class="w50px">
				<img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" />
			</td>
			<td>
				Reviews    : <a href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a><br />
				Fav.Tracks : <a href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a><br />
				Fav.Albums : <a href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a><br />
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
				Syncs      : <a href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a>
<?php endif;?>
		</tr>
	</table>

	<h3>Syncs : <?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?>pt</h3>

	<h4>Sync Reviews (<?= count( $syncs["reviews"] ) ?>) : <?= h($syncs_reviews_point_total) ?> pt</h4>
<?php if (count( $syncs["reviews"] ) > 0):?>
<?php foreach($syncs["reviews"] as $album_id => $reviews): ?>
	<div class="group">
		<table class="w100per">
			<tr>
				<td class="w80px">
					<img class="album_cover" src="<?= h($base_path) ?>files/covers/<?= h($reviews["data"][0]["img_file"]) ?>" alt="<?= h("{$reviews["data"][0]["artist"]} / {$reviews["data"][0]["title"]}") ?>" />
				</td>
				<td>
					<p>
						<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album_id) ?>"><?= h("{$reviews["data"][0]["artist"]} / {$reviews["data"][0]["title"]}") ?> (<?= isset($reviews["data"][0]["year"]) ? h($reviews["data"][0]["year"]) : "unknown" ?>)</a>
						: <span class="sync_point_days"><?= h($reviews["point"]) ?> pt</span>
					</p>
				</td>
			</tr>
		</table>
		<table class="w100per every_other_row_odd">
<?php   foreach($reviews["data"] as $review): ?>
			<tr>
				<td class="w50px">
					<img class="user_photo_min" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.png" ?>" alt="<?= h($review["username"]) ?>" />
				</td>
				<td>
					<p><?= h($review["body"]) ?></p>
<?php if(isset($review["sync_point"]) ): ?>
					<span class="sync_point_days">between <?= h($review["sync_point"]["diff"]+1) ?> days = <?= h($review["sync_point"]["point"]) ?> pt</span>
<?php endif;?>
					<p>
						<a href="<?= h($base_path) ?>Users?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
						<?= h(timeAgoInWords($review["created"])) ?>
					</p>
				</td>
			</tr>
<?php   endforeach; unset($review); ?>
		</table>
	</div>
<?php endforeach; unset($album_id, $reviews); ?>
<?php endif; ?>

	<h4>
		Sync Albums (<?= count( $syncs["albums"]) ?>)
		: <?= count($syncs["albums"])*5 ?>pt
	</h4>
<?php if (count( $syncs["albums"] ) > 0):?>
	<table class="w100per every_other_row_odd">
<?php foreach($syncs["albums"] as $album): ?>
		<tr>
			<td class="w80px">
				<img class="album_search_cover_result" src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h("{$album["artist"]} / {$album["title"]}") ?>" />
			</td>
			<td>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["album_id"]) ?>"><?= h("{$album["artist"]} / {$album["title"]}") ?></a>
				(<?= isset($album["year"]) ? h($album["year"]) : "unknown" ?>)
			</td>
		</tr>
<?php endforeach; unset($album); ?>
	</table>
<?php endif; ?>


	<h4>
		Sync Tracks (<?= count( $syncs["tracks"] ) ?>)
		: <?= count($syncs["tracks"])*2 ?>pt
	</h4>
<?php if (count( $syncs["tracks"] ) > 0):?>
	<table class="w100per every_other_row_odd">
<?php foreach($syncs["tracks"] as $track): ?>
		<tr>
			<td class="w80px">
				<img class="album_search_cover_result" src="<?= h($base_path) ?>files/covers/<?= h($track["img_file"]) ?>" alt="<?= h("{$track["artist"]} / {$track["title"]}") ?>" />
			</td>
			<td>
				<div><strong><?= h($track["track_title"]) ?></strong></div>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($track["album_id"]) ?>"><?= h("{$track["artist"]} / {$track["title"]}") ?></a>
				(<?= isset($track["year"]) ? h($track["year"]) : "unknown" ?>)
				: tr. <?= h($track["track_num"]) ?>
			</td>
		</tr>
<?php endforeach; unset($track); ?>
	</table>
<?php endif; ?>


<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>