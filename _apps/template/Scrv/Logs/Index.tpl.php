<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();
$title_head = "Listening Log";
$add_title = "Listening Log";
if ( isset($select_user) ) {
	$title_head .= ":: {$select_user["username"]}";
	$add_title .= "<br />{$select_user["username"]} <img class='width_50px w3-circle' src='{$base_path}files/attachment/photo/{$select_user["img_file"]}' />";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($title_head) ?> - <?= h($base_title) ?></title>
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
		<h2 class="w3-xlarge"><?= $add_title ?> (<?= h($pager["total_count"]) ?>)</h2>
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
		<div class="w3-padding-tile flex-item col_tile w3-white info"><h5><?php echo $date; ?></h5></div>
		<?php $lastdate = $date; $divcnt++; ?>
<?php endif;?>
		<div class="w3-padding-tile flex-item col_tile w3-white info" style="position:relative;">
			<div style="position:absolute; top:5px; right:5px; z-index:1">
				<table><tr>
<?php if ($review["body"]):?>
					<td width="20">
						<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><img
							src="<?= h($base_path) ?>img/reviews.svg"
							class="width_20px w3-circle w3-white w3-border w3-border-white"
							alt="reviews"
						/></a>
					</td>
<?php endif;?>
<?php if (!isset($select_user)):?>
					<td width="20">
						<a href="<?= h($base_path) ?>Logs/Index/user/<?= h($review["user_id"]) ?>"><img
							class="width_20px w3-circle w3-white w3-border w3-border-white"
							src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>"
							alt="<?= h($review["username"]) ?>"
						/></a>
					</td>
<?php endif;?>
					<!-- <td width="30"><a href="<?= h($base_path) ?>Reviews/Index/situation/<?= h($review["listening_system"]) ?>"><img class="width_12px" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" /></a></td> -->
				</tr></table>
			</div>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>">
				<img class="cover w3-card-4" style="z-index:0" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
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

});
</script>

</body>
</html>