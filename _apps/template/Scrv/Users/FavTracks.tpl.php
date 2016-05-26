<?php
/**
 * Users/FavTracks.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Users/FavTracks?id={$user_id}";
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
<title><?= h($user["username"]) ?> | <?= h($base_title) ?> :: Users :: FavTracks</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<?php require __DIR__ . "/_profile.tpl.php" ?>

	<h3>Fav.Tracks (<?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?>)</h3>

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

<?php foreach($favtracks as $favtrack): ?>
		<div class="displaytable w100per track_info">
			<div class="displaytablecell w80px">
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($favtrack["album_id"]) ?>"><img class="album_cover" src="<?= isset($favtrack["img_file"])? "{$base_path}files/covers/{$favtrack["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></a>
			</div>
			<div class="displaytablecell vtalgmiddle">
				<div><strong><?= h($favtrack["track_title"]) ?></strong></div>
				<div>
					<a href="<?= h($base_path) ?>Albums/View?id=<?= h($favtrack["album_id"]) ?>">
						<?= h($favtrack["artist"]) ?> / <?= h($favtrack["title"]) ?>
						(<?= isset($favtrack["year"]) && $favtrack["year"] !== "" ? h($favtrack["year"]) : "unknown" ?>)
					</a> : tr.<?= h($favtrack["track_num"]) ?>
				</div>
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