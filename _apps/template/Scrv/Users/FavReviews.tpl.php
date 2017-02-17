<?php
/**
 * Users/FavReviews.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();

$_base_url = "{$base_path}Users/FavReviews/id/{$user_id}";
$most_prev_link = "{$_base_url}";
$prev_link = "{$_base_url}/page/".($pager["now_page"]-1);
$next_link = "{$_base_url}/page/".($pager["now_page"]+1);
$most_next_link = "{$_base_url}/page/".$pager["max_page"];
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$_base_url}/page/".$nav["page"],
	);
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($user["username"]) ?> - Users::FavReviews - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

<?php require __DIR__ . "/_profile.tpl.php" ?>

	<div class="w3-center">
		<h2 class="w3-xlarge">Fav.Reviews (<?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?>)</h2>
	</div>

<?php if(count($favreviews) > 0): ?>

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

<!-- reviews -->
<div class="flex-container w3-row-padding w3-padding-16 w3-center">
<?php foreach($favreviews as $favreview): ?>
	<div class="w3-padding flex-item info col">
<?php if(
	($favreview["published"] === 0 && !$is_login)
	||
	($favreview["published"] === 0 && $is_login && $favreview["user_id"] !== $login_user_data["id"])
): ?>
		<div class="notice">
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($favreview["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($favreview["user_img_file"]) ? "files/attachment/photo/{$favreview["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($favreview["username"]) ?>" /></a>
			この投稿は非表示にされています。
		</div>
<?php else: ?>
		<p><img class="cover w3-card-4" src="<?= isset($favreview["img_file"])? "{$base_path}files/covers/{$favreview["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="" /></p>
		<h5>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($favreview["album_id"]) ?>">
				<?= h($favreview["artist"] . " / " . $favreview["title"]) ?>
				(<?= isset($favreview["year"]) && $favreview["year"] !== "" ? h($favreview["year"]) : " unknown " ?>)
			</a>
		</h5>

		<p class="w3-left-align">
			<?= $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($favreview["body"]))), $base_path) ?>
		</p>
		<p>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($favreview["user_id"]) ?>"><img class="width_25px" src="<?= h($base_path) ?><?= isset($favreview["user_img_file"]) ? "files/attachment/photo/{$favreview["user_img_file"]}" : "img/user.svg" ?>" /></a>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($favreview["user_id"]) ?>"><?= h($favreview["username"]) ?></a>
			-
			<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($favreview["id"]) ?>"><?= h(timeAgoInWords($favreview["created"])) ?></a>
		</p>
<?php endif; ?>
	</div>
<?php endforeach; ?>
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

</body>
</html>