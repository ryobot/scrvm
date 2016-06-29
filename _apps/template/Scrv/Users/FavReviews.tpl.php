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
<body><div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">
	<?php require __DIR__ . "/_profile.tpl.php" ?>
</div>

<h3>
	<img src="<?= h($base_path) ?>img/fav_on.svg" class="img16x16" alt="fav reviews" title="fav reviews" />
	Fav.Reviews (<?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?>)
</h3>

<?php if(count($favreviews) > 0): ?>

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


<!-- reviews -->
<div class="review_list">
<?php foreach($favreviews as $favreview): ?>
	<div class="album_info">
		<div class="info">
			<div class="cover">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($favreview["album_id"]) ?>"><img src="<?= isset($favreview["img_file"])? "{$base_path}files/covers/{$favreview["img_file"]}" : "{$base_path}img/user.svg" ?>" alt="" /></a>
			</div>
			<div class="detail">
				<p><a href="<?= h($base_path) ?>Albums/View/id/<?= h($favreview["album_id"]) ?>">
					<?= h($favreview["artist"]) ?><br />
					<?= h($favreview["title"]) ?>
					(<?= isset($favreview["year"]) && $favreview["year"] !== "" ? h($favreview["year"]) : "unknown" ?>)
				</a></p>
			</div>
		</div>
		<div class="review_comment"><?=
			$ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($favreview["body"]))), $base_path)
		?></div>
		<p>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($favreview["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($favreview["user_img_file"]) ? "files/attachment/photo/{$favreview["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($favreview["username"]) ?>" /></a>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($favreview["user_id"]) ?>"><?= h($favreview["username"]) ?></a>
			-
			<span class="post_date"><a href="<?= h($base_path) ?>Reviews/View/id/<?= h($favreview["id"]) ?>"><?= h(timeAgoInWords($favreview["created"])) ?></a></span>
<?php if($favreview["listening_last"] === "today"): ?>
			<img class="vtalgmiddle img16x16" src="<?= h($base_path) ?>img/<?= h($favreview["listening_system"]) ?>.svg" alt="<?= h($favreview["listening_system"]) ?>" title="<?= h($favreview["listening_system"]) ?>" />
<?php endif; ?>
	</div>
<?php endforeach; ?>
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

</div></body>
</html>