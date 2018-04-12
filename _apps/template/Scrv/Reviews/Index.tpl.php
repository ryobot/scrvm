<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();
$add_title_head = "";
$add_title = "";
if ( isset($hash) ) {
	$add_title_head = "::#{$hash}";
	$add_title = h("::#{$hash}");
}
if ( isset($situation) ) {
	$add_title_head = "::{$situation}";
	$add_title = " :: <img class='width_25px' src='{$base_path}img/situation/".h($situation).".svg' class='img24x24' alt='".h($situation)."' title='".h($situation)."' />";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Reviews<?= h($add_title_head) ?> - <?= h($base_title) ?></title>
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
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Reviews<?= $add_title ?> (<?= h($pager["total_count"]) ?>)</h2>
	</div>

<?php if(count($reviews) > 0):?>

	<!-- pager -->
	<div class="w3-padding-top w3-padding-bottom w3-center">
		<div class="w3-bar">
<?php if($pager["prev"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($prev_link) ?>">&laquo;</a>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<a class="w3-button w3-circle <?= $nav["active"] ? "w3-indigo" : "w3-hover-indigo" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($next_link) ?>">&raquo;</a>
<?php endif;?>
		</div>
	</div>

	<div class="flex-container w3-center">
<?php foreach($reviews as $idx => $review): ?>
		<div class="flex-item w3-card w3-white w3-margin-bottom col">
			<div class="w3-padding">
				<img class="cover w3-card" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
			</div>
			<div class="w3-large w3-padding">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
					<span><?= h($review["title"]) ?></span>
					<br />
					<span class="w3-small">
						<?= h($review["artist"]) ?>
						(<?= isset($review["year"]) && $review["year"] !== "" ? h($review["year"]) : " ? " ?>)
					</span>
				</a>
			</div>
			<div class="w3-padding w3-left-align">
				<?= $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path) ?>
			</div>
			<div class="w3-padding">
				<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="w3-image w3-round width_25px" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" /></a>
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
<?php endforeach; ?>
	</div>

	<!-- pager -->
	<div class="w3-padding w3-center">
		<div class="w3-bar">
<?php if($pager["prev"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($prev_link) ?>">&laquo;</a>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<a class="w3-button w3-circle <?= $nav["active"] ? "w3-indigo" : "w3-hover-indigo" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($next_link) ?>">&raquo;</a>
<?php endif;?>
		</div>
	</div>

<?php endif; ?>

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
			if(confirm("このレビューを削除しますか？")){
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