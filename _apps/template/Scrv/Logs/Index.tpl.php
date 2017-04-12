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
if ( isset($select_user) ) {
                  $add_title_head = " :: {$select_user["id"]}";
                  $add_title = "<br>:: {$select_user["username"]} <img class='width_50px' src='{$base_path}files/attachment/photo/{$select_user["img_file"]}' />";
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
		<h2 class="w3-xlarge">Listening Log<?= $add_title ?> (<?= h($pager["total_count"]) ?>)</h2>
	</div>

<?php if(count($reviews) > 0):?>

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

	<div class="flex-container w3-row-padding w3-padding-16 w3-center"  style="justify-content:flex-start;">
<?php $lastdate = ""; $divcnt = 0; ?>
<?php foreach($reviews as $idx => $review): ?>
        <?php $date = dateInWeek($review["created"]); ?>
        <?php if ($date !== $lastdate):?>
            <div class="w3-padding-tile flex-item info col_tile"><h5><?php echo $date; ?></h5></div>
            <?php $lastdate = $date; $divcnt++; ?>
        <?php endif;?>
		<div class="w3-padding-tile flex-item info col_tile" style="position:relative;">
			<div  style="position:absolute; top:5px; right:5px;">
                                                       <table><tr>
				<td width="20">
                                                                        <?php if ($review["body"]):?>
                                                                                                    <a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><img src="<?= h($base_path) ?>img/reviews.svg" class="width_12px" alt="reviews" /></a>
                                                                        <?php else:?>
                                                                                                    &nbsp;
                                                                        <?php endif;?>
                                                                        </td>
                                                                        <?php if (!isset($select_user)):?>
                                                                                        <td width="20"><a href="<?= h($base_path) ?>Logs/Index/user/<?= h($review["user_id"]) ?>"><img class="width_20px" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" /></a></td>
                                                                        <?php endif;?>
				<!-- <td width="30"><a href="<?= h($base_path) ?>Reviews/Index/situation/<?= h($review["listening_system"]) ?>"><img class="width_12px" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" /></a></td> -->
                                                       </tr></table>
			</div>
                                                       <a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
			<img class="cover w3-card-4" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
                                                       </a>
                                    </div>
            <?php $divcnt++; ?>
<?php endforeach; ?>
	</div>



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

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

<script>
;$(function(){

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