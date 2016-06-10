<?php
/**
 * Users/FavAlbums.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Users/FavAlbums/id/{$user_id}";
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
<title><?= h($user["username"]) ?> | <?= h($base_title) ?> :: Users :: FavAlbums</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<?php require __DIR__ . "/_profile.tpl.php" ?>

	<h3>
		<img src="<?= h($base_path) ?>img/favalbums_on.svg" class="img16x16" alt="fav albums" title="fav albums" />
		Fav.Albums (<?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?>)
	</h3>

<?php if(count($favalbums) > 0): ?>

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

<?php foreach($favalbums as $favalbum): ?>
		<div class="displaytable w100per info">
			<div class="displaytablecell album_cover">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($favalbum["id"]) ?>"><img src="<?= isset($favalbum["img_file"])? "{$base_path}files/covers/{$favalbum["img_file"]}" : "{$base_path}img/user.svg" ?>" alt="" /></a>
			</div>
			<div class="displaytablecell vtalgmiddle">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($favalbum["id"]) ?>">
					<?= h($favalbum["artist"]) ?> / <?= h($favalbum["title"]) ?>
					(<?= isset($favalbum["year"]) && $favalbum["year"] !== "" ? h($favalbum["year"]) : "unknown" ?>)
				</a>
			</div>
		</div>
<?php endforeach; ?>

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

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>