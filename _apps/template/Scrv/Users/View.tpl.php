<?php
/**
 * Users/View.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();

$most_prev_link = "{$base_path}Users/View/id/{$user_id}";
$prev_link = "{$base_path}Users/View/id/{$user_id}/page/".($pager["now_page"]-1);
$next_link = "{$base_path}Users/View/id/{$user_id}/page/".($pager["now_page"]+1);
$most_next_link = "{$base_path}Users/View/id/{$user_id}/page/".$pager["max_page"];
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$base_path}Users/View/id/{$user_id}/page/".$nav["page"],
	);
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($user["username"]) ?> - Users::View - <?= h($base_title) ?></title>
</head>
<body><div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">
	<?php require __DIR__ . "/_profile.tpl.php" ?>
</div>

<h3>
	<img src="<?= h($base_path) ?>img/reviews.svg" class="img16x16" alt="reviews" title="reviews" />
	Reviews (<?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?>)
</h3>

<?php if (count($reviews) > 0): ?>

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

<div class="review_list">
<?php foreach($reviews as $review): ?>
	<div class="album_info">
<?php if(
	($review["published"] === 0 && !$is_login)
	||
	($review["published"] === 0 && $is_login && $review["user_id"] !== $login_user_data["id"])
): ?>
		<div class="notice">
			この投稿は非表示にされています。</p>
		</div>
<?php else: ?>
		<div class="cover">
			<img src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
		</div>
		<div class="detail">
			<p><a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
				<?= h($review["artist"]) ?><br />
				<?= h($review["title"]) ?>
				(<?= isset($review["year"]) && $review["year"] !== "" ? h($review["year"]) : "unknown" ?>)
			</a></p>
		</div>
		<div class="review_comment"><?=
			$ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path)
		?></div>
		<div>
			<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>">
				<span class="post_date"><?= h( timeAgoInWords($review["created"])) ?></span>
			</a>
<?php if($review["listening_last"] === "today"): ?>
			<a href="<?= h($base_path) ?>Reviews/Index/situation/<?= h($review["listening_system"]) ?>">
				<img class="situation" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
			</a>
<?php endif;?>
		</div>
		<div class="reaction_area">
<?php if($review["published"] === 0): ?>
			<div><img src="<?= h($base_path) ?>img/locked.svg" title="非公開" alt="非公開" /></div>
<?php endif; ?>
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
				<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><img src="<?= h($base_path) ?>img/link.svg" class="img16x16" alt="perma link" /></a>
			</div>
<?php if($is_login && $user_id === $login_user_data["id"]): ?>
			<div>
				<a href="<?= h($base_path) ?>Reviews/Edit/id/<?= h($review["id"]) ?>"><img src="<?= h($base_path) ?>img/edit.svg" class="img16x16" alt="edit review" title="edit review" /></a>
			</div>
			<div>
				<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete"><img src="<?= h($base_path) ?>img/dustbox.svg" class="img16x16" alt="delete review" title="delete review" /></a>
			</div>
<?php endif;?>
		</div>
<?php endif; ?>
	</div>
<?php		endforeach; ?>
</div>


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
					location.href = BASE_PATH + "Users/View/id/<?= $login_user_data["id"] ?>";
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