<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();

$year = isset($review["year"]) && $review["year"] !== "" ? $review["year"] : "unknown";
$review_title = "{$review["artist"]} / {$review["title"]}";
$album_image_path = isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png";

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<link rel="canonical" href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>" />
<title>
	<?= h($review_title) ?>
	(by <?= h($review["username"]) ?>)
	- Reviews
	- <?= h($base_title) ?>
</title>
<?php require __DIR__ . '/_ogp.tpl.php'; ?>
<?php require __DIR__ . '/_twitter_cards.tpl.php'; ?>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<div class="album_info">
		<h3><?= h($review_title) ?> (<?= h($year) ?>)</h3>
		<div class="info">
			<div class="cover">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
					<img class="album_view_cover" src="<?= h($album_image_path) ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
				</a>
			</div>
			<div class="detail">
			</div>
		</div>
		<div class="review_comment">
			<p><?= $review["body"] === "" || $review["body"] === "listening log"
					? "(no review)"
					: $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path)
			?></p>
		</div>

		<div>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($review["username"]) ?>" /></a>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
			<div class="post_date">
				<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><?= date('Y年n月j日 H時i分',strtotime($review["created"])) ?></a>
<?php if($review["listening_last"] === "today"): ?>
				<img class="vtalgmiddle img16x16" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>.svg" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
			</div>
		</div>

		<div class="reaction_area">
			<div class="fav_reviews_wrapper">
				<img
					class="fav_review vtalgmiddle img16x16"
					src="<?= h($base_path) ?>img/fav_off.svg"
					data-img_on="<?= h($base_path) ?>img/fav_on.svg"
					data-img_off="<?= h($base_path) ?>img/fav_off.svg"
					data-review_id="<?= h($review["id"]) ?>"
					data-my_fav="<?= isset($review["my_fav_id"]) ? 1 : 0 ?>"
					data-fav_reviews_count="<?= h($review["fav_reviews_count"]) ?>"
					alt="fav review"
					title="fav review"
				/>
				<span class="fav_reviews_count"></span>
			</div>
			<div>
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
					<span class="vtalgmiddle">
						<img src="<?= h($base_path) ?>img/reviews.svg" class="img16x16" alt="reviews" />
						<?= $review["reviews_count"] ?>
					</span>
				</a>
			</div>
<?php if( $is_login && $review["user_id"] === $login_user_data["id"] ):?>
			<div>
				<a href="<?= h($base_path) ?>Reviews/Edit/id/<?= h($review["id"]) ?>"><img src="<?= h($base_path) ?>img/edit.svg" class="img16x16" alt="edit review" title="edit review" /></a>
			</div>
			<div>
				<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete"><img src="<?= h($base_path) ?>img/dustbox.svg" class="img16x16" alt="delete review" title="delete review" /></a>
			</div>
<?php endif;?>
		</div>

<?php if(count($favreviews_user_lists) > 0): ?>
		<div class="fav_review_user_lists w100per">
			<p>faved by</p>
<?php		foreach($favreviews_user_lists as $user): ?>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($user["user_id"]) ?>">
				<img
					class="user_photo_min vtalgmiddle"
					src="<?= h($base_path) ?><?= isset($user["user_img_file"]) ? "files/attachment/photo/{$user["user_img_file"]}" : "img/user.svg" ?>"
					alt="<?= h($user["username"]) ?>"
					title="<?= h($user["username"]) ?>"
				/>
			</a>
<?php		endforeach;unset($user) ?>
		</div>
<?php endif; ?>

	</div>

</div>

<!-- music search 用 -->
<div id="id_itunes_search_results"></div>
<div id="id_gpm_search_results"></div>
<input
	type="hidden"
	name="term"
	id="id_term"
	value="<?= h("{$review["artist"]} {$review["title"]}") ?>"
	data-artist="<?= h($review["artist"]) ?>"
	data-title="<?= h($review["title"]) ?>"
/>
<script src="<?= h($base_path) ?>js/MusicSearch.js"></script>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){

	$(".review_delete").each(function(){
		var $del = $(this);
		var delete_id = $del.attr("data-delete_id");
		$del.on("click.js", function(){
			if(confirm("are you sure ?")){
				$.ajax( BASE_PATH + "Reviews/Del", {
					method : 'POST',
					dataType : 'json',
					data : { id : delete_id }
				})
				.done(function(json){
					location.href = BASE_PATH + "Reviews";
				})
				.fail(function(e){
					alert("system error.");
				})
				.always(function(){
				});
			}
			return false;
		});
	});

	$(".fav_review").each(function(){
		var $this = $(this);
		var fav_reviews_count = parseInt($this.attr("data-fav_reviews_count"), 10);
		var my_fav = parseInt($this.attr("data-my_fav"), 10);
		if ( fav_reviews_count > 0 ) {
			$this.next().text(fav_reviews_count);
		}
		if (my_fav === 1) {
			$this.attr("src",$this.attr("data-img_on"));
		}

<?php if($is_login): ?>
		$this.on("click.js", function(){
			var review_id = $(this).attr("data-review_id");
			$.ajax( "<?= h($base_path) ?>Reviews/Fav", {
				method : 'POST',
				dataType : 'json',
				data : { review_id : review_id }
			})
			.done(function(json){
				if (!json.status) {
					alert("system error.");
				} else {
					$this.attr("src",$this.attr("data-img_" + json.data.operation));
					$this.next().text(json.data.fav_count > 0 ? json.data.fav_count : "");
				}
			})
			.fail(function(e){
				alert("system error.");
			})
			.always(function(){
			});
		});
<?php endif; ?>

	});
});
</script>

</body>
</html>