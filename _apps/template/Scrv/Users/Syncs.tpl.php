<?php
/**
 * Users/Syncs.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($user["username"]) ?> - Users::Syncs - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

<?php require __DIR__ . "/_profile.tpl.php" ?>

	<div class="w3-center">
		<h2 class="w3-xlarge">Syncs : <?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?>pt</h2>
	</div>

	<!-- sync artists -->
	<h4 class="w3-center" id="id_title_sync_artists">
		Artists (<?= count( $syncs["artists"] ) ?>)
		: <?= count($syncs["artists"])*10 ?>pt
	</h4>
	<div class="w3-padding w3-center info">
<?php if (count( $syncs["artists"] ) > 0):?>
		<p>
<?php foreach($syncs["artists"] as $artist): ?>
			<span class="sync_artists">
				<a href="<?= h($base_path) ?>Albums/Tag/tag/<?= urlencode($artist["artist"]) ?>"><?= h($artist["artist"]) ?></a>
			</span>,
<?php endforeach; unset($artist); ?>
		</p>
<?php endif; ?>
	</div>

	<!-- sync review -->
	<h4 class="w3-center" id="id_title_sync_reviews">
		Reviews (<?= count( $syncs["reviews"] ) ?>)
		: <?= h($syncs_reviews_point_total) ?>pt
	</h4>
	<div class="flex-container w3-row-padding w3-center">
<?php foreach($syncs["reviews"] as $album_id => $reviews): ?>
		<div class="w3-padding flex-item info col">
			<div class="w3-padding">
				<p><img class="cover w3-card-4" src="<?= h($base_path) ?>files/covers/<?= h($reviews["data"][0]["img_file"]) ?>" /></p>
				<h5>
					<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album_id) ?>">
						<?= h($reviews["data"][0]["artist"] . " / " . $reviews["data"][0]["title"]) ?>
						(<?= isset($reviews["data"][0]["year"]) && $reviews["data"][0]["year"] !== "" ? h($reviews["data"][0]["year"]) : " unknown " ?>)
					</a>
				</h5>
				<img
					src="<?= h($base_path) ?>img/toggle.svg"
					class="sync_review_detail_button width_20px"
					title="detail"
					data-album_id="<?= h($album_id) ?>"
					data-toggle_off="sync_review_detail_button width_20px"
					data-toggle_on="sync_review_detail_button width_20px rotate180"
				/>
			</div>
			<div class="displaynone" id="id_sync_review_detail_<?= h($album_id) ?>">
				<p>
					<span class="sync_point_days">
						between <?= h($reviews["diff"]+1) ?> days = <?= h($reviews["point"]) ?> pt
					</span>
				</p>
	<?php foreach($reviews["data"] as $review): ?>
				<div class="w3-padding info">
					<p class="w3-left-align">
						<?= $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path) ?>
					</p>
					<p>
						<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="width_25px" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" /></a>
						<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
						-
						<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><?= h(timeAgoInWords($review["created"])) ?></a>
					</p>
				</div>
	<?php endforeach; ?>
			</div>
		</div>
<?php endforeach; unset($album_id, $reviews, $review); ?>
	</div>


	<!-- sync albums -->
	<h4 class="w3-center" id="id_title_sync_albums">
		Albums (<?= count( $syncs["albums"]) ?>)
		: <?= count($syncs["albums"])*5 ?>pt
	</h4>
	<div class="flex-container w3-row-padding w3-center">
<?php foreach($syncs["albums"] as $album): ?>
		<div class="w3-padding flex-item info col">
			<p><img class="cover w3-card-4" src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h("{$album["artist"]} / {$album["title"]}") ?>" /></p>
			<h5>
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["album_id"]) ?>">
					<?= h($album["artist"] . " / " . $album["title"]) ?>
					(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : " unknown " ?>)
				</a>
			</h5>
		</div>
<?php endforeach; unset($album); ?>
	</div>


	<!-- sync tracks -->
	<h4 class="w3-center" id="id_title_sync_tracks">
		Tracks (<?= count( $syncs["tracks"] ) ?>)
		: <?= count($syncs["tracks"])*2 ?>pt
	</h4>
	<div class="flex-container w3-row-padding w3-center">
<?php foreach($syncs["tracks"] as $track): ?>
		<div class="w3-padding flex-item info col">
			<p><img class="cover w3-card-4" src="<?= h($base_path) ?>files/covers/<?= h($track["img_file"]) ?>" alt="<?= h("{$track["artist"]} / {$track["title"]}") ?>" /></p>
			<h5>Tr.<?= h($track["track_num"]) ?> : <?= h($track["track_title"]) ?></h5>
			<h5>
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($track["album_id"]) ?>">
					<?= h($track["artist"] . " / " . $track["title"]) ?>
					(<?= isset($track["year"]) && $track["year"] !== "" ? h($track["year"]) : " unknown " ?>)
				</a>
			</h5>
		</div>
<?php endforeach; unset($track); ?>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


<script>
;$(function(){
	// sync_review_detail_button
	$(".sync_review_detail_button").each(function(){
		var $this = $(this);
		$(this).on("click.js",function(){
			var album_id = $this.attr("data-album_id");
			$("#id_sync_review_detail_" + album_id).slideToggle("fast", function(){
				var current_class = $this.attr("class");
				var toggle_on = $this.attr("data-toggle_on");
				var toggle_off = $this.attr("data-toggle_off");
				$this.removeClass().addClass( current_class === toggle_on ? toggle_off : toggle_on );
			});
		});
	});
});
</script>

</body>
</html>