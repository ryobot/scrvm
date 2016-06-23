<?php
/**
 * Users/FavTracks.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Users/FavTracks/id/{$user_id}";
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
<title><?= h($user["username"]) ?> | <?= h($base_title) ?> :: Users :: FavTracks</title>
</head>
<body><div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">
	<?php require __DIR__ . "/_profile.tpl.php" ?>
</div>

<h3>
	<img src="<?= h($base_path) ?>img/favtracks_on.svg" class="img16x16" alt="fav tracks" title="fav tracks" />
	Fav.Tracks (<?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?>)
</h3>

<?php if(count($favtracks) > 0): ?>
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

<div class="contents">
<?php foreach($favtracks as $favtrack): ?>
		<div class="album_info">
			<div class="cover">
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($favtrack["album_id"]) ?>"><img src="<?= isset($favtrack["img_file"])? "{$base_path}files/covers/{$favtrack["img_file"]}" : "{$base_path}img/user.svg" ?>" alt="" /></a>
			</div>
			<div class="detail">
				<div>
					<strong><?= h($favtrack["track_num"]) ?>. <?= h($favtrack["track_title"]) ?></strong>
				</div>
				<div>
					<a href="<?= h($base_path) ?>Albums/View/id/<?= h($favtrack["album_id"]) ?>">
						<?= h($favtrack["artist"]) ?><br />
						<?= h($favtrack["title"]) ?>
						(<?= isset($favtrack["year"]) && $favtrack["year"] !== "" ? h($favtrack["year"]) : "unknown" ?>)
					</a>
				</div>
			</div>
		</div>
<?php endforeach; ?>
</div>

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

</div></body>
</html>