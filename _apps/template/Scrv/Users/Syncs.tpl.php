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
<title><?= h($user["username"]) ?> | <?= h($base_title) ?> :: Users :: Syncs</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<?php require __DIR__ . "/_profile.tpl.php" ?>

	<h3>Syncs : <?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?>pt</h3>

<!--	 sync menu
	<div class="displaytable w100per sync_menu tacenter">
		<div class="displaytablecell w33per"><a href="#id_title_sync_reviews">Reviews : <?= h($syncs_reviews_point_total) ?>pt</a></div>
		<div class="displaytablecell w33per"><a href="#id_title_sync_albums">Albums : <?= count($syncs["albums"])*5 ?>pt</a></div>
		<div class="displaytablecell w33per"><a href="#id_title_sync_tracks">Tracks : <?= count($syncs["tracks"])*2 ?>pt</a></div>
	</div>-->

	<!-- sync artists -->
	<h4 id="id_title_sync_artists">
		Artists (<?= count( $syncs["artists"] ) ?>)
		: ? pt
	</h4>
<?php if (count( $syncs["artists"] ) > 0):?>
	<div class="w100per info">
<?php foreach($syncs["artists"] as $artist): ?>
		<span class="sync_artists">
			<a href="<?= h($base_path) ?>Albums/Tag/tag/<?= urlencode($artist["artist"]) ?>"><?= h($artist["artist"]) ?></a>
		</span>
<?php endforeach; unset($artist); ?>
	</div>
<?php endif; ?>

	<!-- sync review -->
	<h4 id="id_title_sync_reviews">
		Reviews (<?= count( $syncs["reviews"] ) ?>)
		: <?= h($syncs_reviews_point_total) ?>pt
	</h4>
<?php if (count( $syncs["reviews"] ) > 0):?>
<?php foreach($syncs["reviews"] as $album_id => $reviews): ?>
	<div class="w100per info mgb10px">

		<div class="displaytable w100per">
			<div class="displaytablecell w80px">
				<img class="album_cover" src="<?= h($base_path) ?>files/covers/<?= h($reviews["data"][0]["img_file"]) ?>" alt="<?= h("{$reviews["data"][0]["artist"]} / {$reviews["data"][0]["title"]}") ?>" />
			</div>
			<div class="displaytablecell vtalgmiddle">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album_id) ?>"><?= h("{$reviews["data"][0]["artist"]} / {$reviews["data"][0]["title"]}") ?> (<?= isset($reviews["data"][0]["year"]) ? h($reviews["data"][0]["year"]) : "unknown" ?>)</a>
				<div>
					<span class="sync_point_days">
						between <?= h($reviews["diff"]+1) ?> days =
						<?= h($reviews["point"]) ?> pt
					</span>
				</div>
			</div>
		</div>

<?php foreach($reviews["data"] as $review): ?>
		<div class="w100per info">
			<div class="review_comment"><?= nl2br(linkIt(h($review["body"]))) ?></div>
			<div class="displaytable w100per">
				<div class="displaytablecell w50px">
					<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($review["username"]) ?>" />
				</div>
				<div class="displaytablecell vtalgmiddle">
					<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
					<span class="post_date"><?= h(timeAgoInWords($review["created"])) ?></span>
<?php if($review["listening_last"] === "today"): ?>
					<img class="vtalgmiddle img16x16" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>.svg" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
				</div>
			</div>
		</div>
<?php endforeach; unset($review); ?>

	</div>
<?php endforeach; unset($album_id, $reviews); ?>
<?php endif; ?>

	<!-- sync albums -->
	<h4 id="id_title_sync_albums">
		Albums (<?= count( $syncs["albums"]) ?>)
		: <?= count($syncs["albums"])*5 ?>pt
	</h4>
<?php if (count( $syncs["albums"] ) > 0):?>
	<div>
<?php foreach($syncs["albums"] as $album): ?>
		<div class="displaytable w100per album_info">
			<div class="displaytablecell w80px">
				<img class="album_search_cover_result" src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h("{$album["artist"]} / {$album["title"]}") ?>" />
			</div>
			<div class="displaytablecell vtalgmiddle">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["album_id"]) ?>">
					<?= h("{$album["artist"]} / {$album["title"]}") ?>
					(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
				</a>
			</div>
		</div>
<?php endforeach; unset($album); ?>
	</div>
<?php endif; ?>

	<!-- sync tracks -->
	<h4 id="id_title_sync_tracks">
		Tracks (<?= count( $syncs["tracks"] ) ?>)
		: <?= count($syncs["tracks"])*2 ?>pt
	</h4>
<?php if (count( $syncs["tracks"] ) > 0):?>
	<div>
<?php foreach($syncs["tracks"] as $track): ?>
		<div class="displaytable w100per track_info">
			<div class="displaytablecell w80px">
				<img class="album_search_cover_result" src="<?= h($base_path) ?>files/covers/<?= h($track["img_file"]) ?>" alt="<?= h("{$track["artist"]} / {$track["title"]}") ?>" />
			</div>
			<div class="displaytablecell vtalgmiddle">
				<div><strong><?= h($track["track_title"]) ?></strong></div>
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($track["album_id"]) ?>">
					<?= h("{$track["artist"]} / {$track["title"]}") ?>
					(<?= isset($track["year"]) && $track["year"] !== "" ? h($track["year"]) : "unknown" ?>)
				</a> : tr.<?= h($track["track_num"]) ?>
			</div>
		</div>
<?php endforeach; unset($track); ?>
	</div>
<?php endif; ?>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>