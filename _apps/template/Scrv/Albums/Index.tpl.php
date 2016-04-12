<?php
/**
 * Albums/Index.tpl.php
 * @author mgng
 */

$prev_link = $base_path . "Albums?" . http_build_query(array(
	"artist" => $artist,
	"offset" => $pager["offset"]-$pager["limit"],
));
$next_link = $base_path . "Albums?" . http_build_query(array(
	"artist" => $artist,
	"offset" => $pager["offset"]+$pager["limit"],
));
if($pager["offset"]-$pager["limit"] < 0){
	$prev_link = "";
}
if($pager["offset"]+$pager["limit"] >= $pager["total_count"]){
	$next_link = "";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Albums</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Albums (<?= h($pager["total_count"]) ?>)</h2>

<?php if( $is_login ): ?>
	<p class="actions"><a href="<?= h($base_path) ?>Albums/Add">add Album</a></p>
<?php endif; ?>

	<form id="id_form_Albums_ArtistFilter" action="<?= h($base_path) ?>Albums" method="GET">
		artist Filter:
		<input type="text" name="artist" id="id_artist" value="<?= h($artist) ?>" />
		<input type="submit" value="filter" />
	</form>

	<p class="pager">
<?php if($prev_link !== ""): ?>
		<a href="<?= h($prev_link) ?>">≪prev</a>
<?php else:?>
		<span>≪prev</span>
<?php endif;?>
		<?= h($pager["now_page"]) ?> / <?= h($pager["max_page"]) ?>
<?php if($next_link !== ""): ?>
		<a href="<?= h($next_link) ?>">next≫</a>
<?php else:?>
		<span>next≫</span>
<?php endif;?>
	</p>

	<table>
		<tr>
			<th></th>
			<th>Artist / Title (Year)</th>
			<th></th>
		</tr>
<?php foreach($lists as $album): ?>
		<tr>
			<td><img class="album_search_cover_result" src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></td>
			<td><a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["id"]) ?>"><?= h( "{$album["artist"]} / {$album["title"]}") ?></a> (<?= isset($album["year"]) ? h($album["year"]) : "unknown" ?>)</td>
			<td></td>
		</tr>
<?php endforeach; ?>
	</table>

	<p class="pager">
<?php if($prev_link !== ""): ?>
		<a href="<?= h($prev_link) ?>">≪prev</a>
<?php else:?>
		<span>≪prev</span>
<?php endif;?>
		<?= h($pager["now_page"]) ?> / <?= h($pager["max_page"]) ?>
<?php if($next_link !== ""): ?>
		<a href="<?= h($next_link) ?>">next≫</a>
<?php else:?>
		<span>next≫</span>
<?php endif;?>
	</p>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>