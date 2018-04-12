<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();

$year = isset($review["year"]) && $review["year"] !== "" ? $review["year"] : "?";
$review_title = "{$review["title"]} / {$review["artist"]} ($year)";
$album_image_path = isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png";

$is_unpublished = $review["published"] === 0 && (!$is_login || ($is_login && $review["user_id"] !== $login_user_data["id"]));
if($is_unpublished){
	$_description = "";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<link rel="canonical" href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>" />
<?php if($is_unpublished): ?>
<title>この投稿は非表示にされています。</title>
<?php else: ?>
<title>
	<?= h($review_title) ?>
	by <?= h($review["username"]) ?>
	- Reviews
	- <?= h($base_title) ?>
</title>
<?php require __DIR__ . '/_ogp.tpl.php'; ?>
<?php require __DIR__ . '/_twitter_cards.tpl.php'; ?>
<?php endif; ?>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="main w3-content">

	<!-- album info -->
	<div class="w3-white w3-center w3-card w3-margin-bottom">

<?php if($is_unpublished): ?>
		<div class="w3-padding notice">この投稿は非表示にされています。</div>
<?php else: ?>

		<div class="w3-padding">
			<img class="cover w3-card" src="<?= h($album_image_path) ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
		</div>

		<div class="w3-large w3-padding">
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
				<span><?= h($review["title"]) ?></span>
				<br />
				<span class="w3-small"><?= h($review["artist"]) ?> (<?= h($year) ?>)</span>
			</a>
		</div>

		<div class="w3-padding w3-left-align">
			<?= $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path) ?>
		</div>

		<div class="w3-padding">
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="w3-round width_25px" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" /></a>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
			-
			<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><?= h(timeAgoInWords($review["created"])) ?></a>
		</div>

		<div class="w3-padding reaction_area">
<?php if($review["published"] === 0): ?>
			<span><img src="<?= h($base_path) ?>img/locked.svg" title="非公開" alt="非公開" /></span>
<?php endif; ?>
<?php if($review["listening_last"] === "today"): ?>
			<a href="<?= h($base_path) ?>Reviews/Index/situation/<?= h($review["listening_system"]) ?>"><img class="width_25px" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" /></a>
<?php endif; ?>
			<span class="fav_reviews_wrapper">
				<a href="javascript:void(0)">
					<img
						class="fav_review"
						src="<?= h($base_path) ?>img/fav_off.svg"
						data-img_on="<?= h($base_path) ?>img/fav_on.svg"
						data-img_off="<?= h($base_path) ?>img/fav_off.svg"
						data-review_id="<?= h($review["id"]) ?>"
						data-my_fav="<?= isset($review["my_fav_id"]) ? 1 : 0 ?>"
						data-fav_reviews_count="<?= h($review["fav_reviews_count"]) ?>"
					/>
					<span class="fav_reviews_count"></span>
				</a>
			</span>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
				<span class="vtalgmiddle">
					<img src="<?= h($base_path) ?>img/reviews.svg" class="img16x16" alt="reviews" />
					<?= $review["reviews_count"] ?>
				</span>
			</a>
<?php if( $is_login && $review["user_id"] === $login_user_data["id"] ):?>
			<a href="javascript:;" class="reaction_more" data-review_id="<?= h($review["id"]) ?>"><img src="<?= h($base_path) ?>img/more.svg" class="img16x16" alt="more" /></a>
<?php endif;?>
		</div>
<?php if( $is_login && $review["user_id"] === $login_user_data["id"] ):?>
		<div class="displaynone w3-container w3-padding w3-light-gray" id="id_reaction_more_<?= h($review["id"]) ?>">
			<p><a href="<?= h($base_path) ?>Reviews/Edit/id/<?= h($review["id"]) ?>" class="w3-btn w3-teal w3-round"><i class="fas fa-edit"></i> レビューを編集する</a></p>
			<p><a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete w3-btn w3-round"><i class="fas fa-trash-alt"></i> レビューを削除する</a></p>
		</div>
<?php endif;?>
	</div>

<?php if(count($favreviews_user_lists) > 0): ?>
	<div class="w3-padding w3-center w3-card w3-white w3-margin-bottom">
		<p>faved by</p>
		<p>
<?php		foreach($favreviews_user_lists as $user): ?>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($user["user_id"]) ?>">
				<img
					class="w3-round width_25px"
					src="<?= h($base_path) ?><?= isset($user["user_img_file"]) ? "files/attachment/photo/{$user["user_img_file"]}" : "img/user.svg" ?>"
					alt="<?= h($user["username"]) ?>"
				/>
			</a>
<?php		endforeach;unset($user) ?>
		</p>
	</div>
<?php endif; ?>
	<input
		type="hidden"
		name="term"
		id="id_term"
		value="<?= h("{$review["artist"]} {$review["title"]}") ?>"
		data-artist="<?= h($review["artist"]) ?>"
		data-title="<?= h($review["title"]) ?>"
	/>
	<script src="<?= h($base_path) ?>js/MusicSearch.js"></script>
<?php endif; ?>

	<!-- music search 用 -->
	<div class="w3-card w3-margin-bottom w3-white">
		<div class="w3-padding" id="id_itunes_search_results"></div>
		<div class="w3-padding" id="id_gpm_search_results"></div>
	</div>

</div>


<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

<script>
;$(function(){

	$(".reaction_more").on("click.js", function(){
		var review_id = $(this).attr("data-review_id");
		$("#id_reaction_more_" + review_id).slideToggle("fast");
		return false;
	});

	$(".review_delete").each(function(){
		var $del = $(this);
		var delete_id = $del.attr("data-delete_id");
		$del.on("click.js", function(){
			if(confirm("このレビューを削除しますか ?")){
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