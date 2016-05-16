<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

$review_title = "{$review["artist"]} / {$review["title"]} (".
		(isset($review["year"]) && $review["year"] !== "" ? $review["year"] : "unknown") . ") - by {$review["username"]}";

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($review_title) ?> | <?= h($base_title) ?> :: Reviews</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2><?= h($review_title) ?></h2>

		<p>
			<a href="<?= h($base_path) ?>Albums/View?id=<?= h($review["album_id"]) ?>">
				<img class="album_cover" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
			</a>
		</p>
		<div class="review_comment">
			<p><?= $review["body"] === "" || $review["body"] === "listening log" ? "(no review)" : nl2br(linkIt(h($review["body"]))) ?></p>
		</div>
		<p>
			<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.png" ?>" alt="<?= h($review["username"]) ?>" /></a>
			<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
			-
			<span class="post_date"><a href="<?= h($base_path) ?>Reviews/View?id=<?= h($review["id"]) ?>"><?= date('Y年n月j日 H時i分',strtotime($review["created"])) ?></a></span>
<?php if($review["listening_last"] === "today"): ?>
			<img class="vtalgmiddle" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>_30.png" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
			<span class="fav_reviews_wrapper">
				<img
					class="fav_review vtalgmiddle "
					src="<?= h($base_path) ?>img/fav_off.png"
					data-img_on="<?= h($base_path) ?>img/fav_on.png"
					data-img_off="<?= h($base_path) ?>img/fav_off.png"
					data-review_id="<?= h($review["id"]) ?>"
					data-my_fav="<?= isset($review["my_fav_id"]) ? 1 : 0 ?>"
					data-fav_reviews_count="<?= h($review["fav_reviews_count"]) ?>"
					alt="fav review"
					title="fav review"
				/>
				<span class="fav_reviews_count"></span>
			</span>
		</p>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>
</div>

<script>
;$(function(){
	$(".review_delete").each(function(){
		var $del = $(this);
		var delete_id = $del.attr("data-delete_id");
		$del.on("click.js", function(){
			if(confirm("are you sure ?")){
				$.ajax( "<?= h($base_path) ?>Reviews/Del", {
					method : 'POST',
					dataType : 'json',
					data : { id : delete_id }
				})
				.done(function(json){
					location.href="<?= h($base_path) ?>Reviews";
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