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
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

<?php require __DIR__ . "/_profile.tpl.php" ?>

	<div class="w3-center">
		<h2 class="w3-xlarge">Reviews (<?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?>)</h2>
	</div>

<?php if (count($reviews) > 0): ?>

<?php if(count($pager["nav_list"])>0): ?>
	<!-- pager -->
	<div class="w3-center w3-padding-8">
		<ul class="w3-pagination">
<?php if($pager["prev"]): ?>
			<li><a class="w3-hover-black" href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a class="<?= $nav["active"] ? "w3-black" : "w3-hover-black" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a class="w3-hover-black" href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>
<?php endif; ?>

<div class="flex-container w3-row-padding w3-padding-16 w3-center">
<?php foreach($reviews as $review): ?>
	<div class="w3-padding flex-item info col">
<?php if(
	($review["published"] === 0 && !$is_login)
	||
	($review["published"] === 0 && $is_login && $review["user_id"] !== $login_user_data["id"])
): ?>
		<div class="notice">
			この投稿は非表示にされています。
		</div>
<?php else: ?>
		<p><img class="cover w3-card-4" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" /></p>
		<h5>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
				<?= h($review["artist"] . " / " . $review["title"]) ?>
				(<?= isset($review["year"]) && $review["year"] !== "" ? h($review["year"]) : " unknown " ?>)
			</a>
		</h5>
		<p class="w3-left-align">
			<?= $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path) ?>
		</p>
		<p>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="width_25px" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" /></a>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
			-
			<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><?= h(timeAgoInWords($review["created"])) ?></a>
		</p>
		<div class="w3-center reaction_area">
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
			<a href="<?= h($base_path) ?>Reviews/Edit/id/<?= h($review["id"]) ?>"><img src="<?= h($base_path) ?>img/edit.svg" class="img16x16" alt="edit review" title="edit review" /></a>
			<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete"><img src="<?= h($base_path) ?>img/dustbox.svg" /></a>
<?php endif;?>
		</div>
<?php endif; ?>
	</div>
<?php		endforeach; ?>
</div>

<?php if(count($pager["nav_list"])>0): ?>
	<!-- pager -->
	<div class="w3-center w3-padding-8">
		<ul class="w3-pagination">
<?php if($pager["prev"]): ?>
			<li><a class="w3-hover-black" href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a class="<?= $nav["active"] ? "w3-black" : "w3-hover-black" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a class="w3-hover-black" href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>
<?php endif; ?>

<?php endif; ?>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


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