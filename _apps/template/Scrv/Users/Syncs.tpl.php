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
</div>

<h3>
	<img src="<?= h($base_path) ?>img/sync.svg" class="img16x16" alt="sync point" title="sync point" />
	Syncs : <?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?>pt
</h3>


<!-- sync artists -->
<!--<div class="contents">
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
</div>-->


<!-- sync review -->
<div class="contents">
	<h4 id="id_title_sync_reviews">
		Reviews (<?= count( $syncs["reviews"] ) ?>)
		: <?= h($syncs_reviews_point_total) ?>pt
	</h4>
<?php foreach($syncs["reviews"] as $album_id => $reviews): ?>
	<div>
		<div class="album_info">
			<div class="cover">
				<img src="<?= h($base_path) ?>files/covers/<?= h($reviews["data"][0]["img_file"]) ?>" alt="<?= h("{$reviews["data"][0]["artist"]} / {$reviews["data"][0]["title"]}") ?>" />
			</div>
			<div class="detail">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album_id) ?>">
					<?= h("{$reviews["data"][0]["artist"]}") ?><br />
					<?= h("{$reviews["data"][0]["title"]}") ?>
					(<?= isset($reviews["data"][0]["year"]) ? h($reviews["data"][0]["year"]) : "unknown" ?>)
				</a>
			</div>
			<div class="reviews">
				<img
					src="<?= h($base_path) ?>img/toggle.svg"
					class="sync_review_detail_button"
					alt="detail"
					title="detail"
					data-album_id="<?= h($album_id) ?>"
					data-toggle_off="sync_review_detail_button"
					data-toggle_on="sync_review_detail_button rotate180"
				/>
			</div>
		</div>
		<div class="info displaynone" id="id_sync_review_detail_<?= h($album_id) ?>">
			<div class="tacenter">
				<span class="sync_point_days">
					between <?= h($reviews["diff"]+1) ?> days =
					<?= h($reviews["point"]) ?> pt
				</span>
			</div>
<?php foreach($reviews["data"] as $review): ?>
			<div class="sync_review">
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
<?php endforeach; ?>
		</div>
	</div>
<?php endforeach; unset($album_id, $reviews, $review); ?>
</div>


<!-- sync albums -->
<div class="contents">
	<h4 id="id_title_sync_albums">
		Albums (<?= count( $syncs["albums"]) ?>)
		: <?= count($syncs["albums"])*5 ?>pt
	</h4>
<?php foreach($syncs["albums"] as $album): ?>
	<div class="displaytable w100per info">
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


<!-- sync tracks -->
<div class="contents">
	<h4 id="id_title_sync_tracks">
		Tracks (<?= count( $syncs["tracks"] ) ?>)
		: <?= count($syncs["tracks"])*2 ?>pt
	</h4>
<?php foreach($syncs["tracks"] as $track): ?>
	<div class="album_info">
		<div class="cover">
			<img class="album_search_cover_result" src="<?= h($base_path) ?>files/covers/<?= h($track["img_file"]) ?>" alt="<?= h("{$track["artist"]} / {$track["title"]}") ?>" />
		</div>
		<div class="detail">
			<div><strong><?= h($track["track_num"]) ?>. <?= h($track["track_title"]) ?></strong></div>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($track["album_id"]) ?>">
				<?= h("{$track["artist"]}") ?><br />
				<?= h("{$track["title"]}") ?>
				(<?= isset($track["year"]) && $track["year"] !== "" ? h($track["year"]) : "unknown" ?>)
			</a>
		</div>
	</div>
<?php endforeach; unset($track); ?>
</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

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