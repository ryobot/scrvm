<?php
/**
 * Users/FavReviews.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Users/FavReviews?id={$user_id}";
$most_prev_link = "{$_base_url}";
$prev_link = "{$_base_url}&" . hbq(array("page" => $pager["now_page"]-1,));
$next_link = "{$_base_url}&" . hbq(array("page" => $pager["now_page"]+1,));
$most_next_link = "{$_base_url}&" . hbq(array("page" => $pager["max_page"],));
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$_base_url}&" . hbq(array("page" => $nav["page"],)),
	);
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($user["username"]) ?> | <?= h($base_title) ?> :: Users :: FavReviews</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2><?= h($user["username"]) ?></h2>

	<table class="w100per every_other_row_odd">
		<tr>
			<td class="w80px tacenter vtalgmiddle">
				<img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" />
			</td>
			<td class="user_menu_list">
				<ul>
					<li class="reviews"><a href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a></li>
					<li class="fav_tracks"><a href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a></li>
					<li class="fav_albums"><a href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a></li>
					<li class="fav_reviews"><a href="<?= h("{$base_path}Users/FavReviews?id={$user_id}") ?>"><?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?></a></li>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
					<li class="syncs"><a href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a></li>
<?php endif;?>
				</ul>
			</td>
		</tr>
	</table>
<?php if(isset($user["profile"]) && $user["profile"] !== ""): ?>
	<p class="w100per user_profile"><?= nl2br(linkIt(h($user["profile"]))) ?></p>
<?php endif;?>

	<h3>Fav.Reviews (<?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?>)</h3>

<?php if(count($favreviews) > 0): ?>

	<div class="tacenter">
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

	<table class="w100per every_other_row_odd">
<?php foreach($favreviews as $favreview): ?>
		<tr>
			<td>
				<div class="floatleft mgr5px">
					<a href="<?= h($base_path) ?>Albums/View?id=<?= h($favreview["id"]) ?>"><img class="album_cover" src="<?= isset($favreview["img_file"])? "{$base_path}files/covers/{$favreview["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></a>
				</div>
				<div>
						<a href="<?= h($base_path) ?>Albums/Tag?tag=<?= urlencode($favreview["artist"]) ?>"><?= h($favreview["artist"]) ?></a>
						<p><a href="<?= h($base_path) ?>Albums/View?id=<?= h($favreview["album_id"]) ?>">
							<?= h($favreview["title"]) ?>
							(<?= isset($favreview["year"]) && $favreview["year"] !== "" ? h($favreview["year"]) : "unknown" ?>)
						</a></p>
					</div>
				<div class="review_comment clearboth">
					<?= $favreview["body"] === "" || $favreview["body"] === "listening log" ? "(no review)" : nl2br(linkIt(h($favreview["body"]))) ?>
				</div>
				<p>
					<a href="<?= h($base_path) ?>Users/View?id=<?= h($favreview["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($favreview["user_img_file"]) ? "files/attachment/photo/{$favreview["user_img_file"]}" : "img/user.png" ?>" alt="<?= h($favreview["username"]) ?>" /></a>
					<a href="<?= h($base_path) ?>Users/View?id=<?= h($favreview["user_id"]) ?>"><?= h($favreview["username"]) ?></a>
					-
					<span class="post_date"><a href="<?= h($base_path) ?>Reviews/View?id=<?= h($favreview["id"]) ?>"><?= h(timeAgoInWords($favreview["created"])) ?></a></span>
<?php if($favreview["listening_last"] === "today"): ?>
					<img class="vtalgmiddle" src="<?= h($base_path) ?>img/<?= h($favreview["listening_system"]) ?>_30.png" alt="<?= h($favreview["listening_system"]) ?>" title="<?= h($favreview["listening_system"]) ?>" />
<?php endif; ?>
			</td>
		</tr>
<?php endforeach; ?>
	</table>

	<div class="tacenter">
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

</body>
</html>