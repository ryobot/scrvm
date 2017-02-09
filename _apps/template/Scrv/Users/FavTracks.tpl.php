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
<title><?= h($user["username"]) ?> - Users::FavTracks - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

<?php require __DIR__ . "/_profile.tpl.php" ?>

	<div class="w3-center">
		<h2 class="w3-xlarge">Fav.Tracks (<?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?>)</h2>
	</div>

<?php if(count($favtracks) > 0): ?>

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
<?php foreach($favtracks as $favtrack): ?>
	<div class="w3-padding flex-item info col">
		<p><img class="cover" src="<?= isset($favtrack["img_file"])? "{$base_path}files/covers/{$favtrack["img_file"]}" : "{$base_path}img/user.svg" ?>" alt="" /></p>
		<h5>Tr.<?= h($favtrack["track_num"]) ?> : <?= h($favtrack["track_title"]) ?></h5>
		<h5>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($favtrack["album_id"]) ?>">
				<?= h($favtrack["artist"] . " / " . $favtrack["title"]) ?>
				(<?= isset($favtrack["year"]) && $favtrack["year"] !== "" ? h($favtrack["year"]) : " unknown " ?>)
			</a>
		</h5>
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