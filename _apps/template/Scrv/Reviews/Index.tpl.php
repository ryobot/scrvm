<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();
$add_title = "";
if ( isset($hash) ) {
	$add_title = "::#{$hash}";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Reviews<?= h($add_title) ?> - <?= h($base_title) ?></title>
<!-- ogp -->
<meta property="og:sitename" content="<?= h($base_title) ?>" />
<meta property="og:title" content="<?= h($base_title) ?>" />
<meta property="og:locale" content="ja_JP" />
<meta property="og:description" content="<?= h($_description) ?>" />
<meta property="og:url" content="<?= h(\lib\Util\Server::getFullHostUrl() . $base_path) ?>" />
<meta property="og:image" content="<?= h(\lib\Util\Server::getFullHostUrl() . "{$base_path}img/headphone_icon_S.png") ?>" />
<!-- twitter cards -->
<meta name="twitter:card" value="summary" />
<meta name="twitter:site" value="@ryobotnotabot" />
<meta name="twitter:title" value="<?= h($base_title) ?>" />
<meta name="twitter:description" content="<?= h($_description) ?>" />
<meta name="twitter:url" content="<?= h(\lib\Util\Server::getFullHostUrl() . $base_path) ?>" />
<meta name="twitter:image:src" content="<?= h(\lib\Util\Server::getFullHostUrl() . "{$base_path}img/headphone_icon_S.png") ?>" />
</head>
<body><div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<h2>
	Reviews<?= h($add_title) ?>
	(<?= h($pager["total_count"]) ?>)
</h2>

<?php if(count($reviews) > 0):?>

<!-- pager -->
<div class="pager">
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

<!-- review list -->
<div class="review_list">
<?php foreach($reviews as $review): ?>
	<div class="album_info">
		<div class="info">
			<div class="cover">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
					<img src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
				</a>
			</div>
			<div class="detail">
				<p><a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
					<?= h($review["artist"]) ?><br />
					<?= h($review["title"]) ?><br />
					(<?= isset($review["year"]) && $review["year"] !== "" ? h($review["year"]) : "unknown" ?>)
				</a></p>
			</div>
		</div>
		<div class="review_comment"><?=
			$ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path)
		?></div>
		<div>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($review["username"]) ?>" /></a>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
			-
			<span class="post_date"><a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><?= h(timeAgoInWords($review["created"])) ?></a></span>
<?php if($review["listening_last"] === "today"): ?>
			<img class="situation" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
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
			<div>
				<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><img src="<?= h($base_path)?>img/link.svg" class="img16x16" alt="perma link" /></a>
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
	</div>
<?php endforeach; ?>
</div>

<!-- pager -->
<div class="pager">
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
			$.ajax( BASE_PATH + "Reviews/Fav", {
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