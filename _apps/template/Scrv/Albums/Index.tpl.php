<?php
/**
 * Albums/Index.tpl.php
 * @author mgng
 */

$prev_link = "{$base_path}Albums?" . http_build_query(array(
	"artist" => $artist,
	"offset" => $pager["offset"]-$pager["limit"],
	"sort"   => $sort,
	"order"  => $order,
));
$next_link = "{$base_path}Albums?" . http_build_query(array(
	"artist" => $artist,
	"offset" => $pager["offset"]+$pager["limit"],
	"sort"   => $sort,
	"order"  => $order,
));
if($pager["offset"]-$pager["limit"] < 0){
	$prev_link = "";
}
if($pager["offset"]+$pager["limit"] >= $pager["total_count"]){
	$next_link = "";
}

// ソート用リンク
$order_type = $order === "asc" ? "desc" : "asc";
$sort_links = array(
	"artist" => array(
		"link" => "{$base_path}Albums?sort=artist&order={$order_type}",
		"text" => $sort === "artist" ? "[Artist]" : "Artist",
	),
	"title" => array(
		"link" => "{$base_path}Albums?sort=title&order={$order_type}",
		"text" => $sort === "title" ? "[Title]" : "Title",
	),
	"year" => array(
		"link" => "{$base_path}Albums?sort=year&order={$order_type}",
		"text" => $sort === "year" ? "[Year]" : "Year",
	),
);

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
	<p class="actions"><a href="<?= h($base_path) ?>Albums/Add">Add Album</a></p>
<?php endif; ?>

	<form id="id_form_Albums_ArtistFilter" action="<?= h($base_path) ?>Albums" method="GET">
		<p><input type="text" name="artist" id="id_artist" value="<?= h($artist) ?>" placeholder="Artist Filter" /></p>
		<p class="actions"><input type="submit" value="filter" /></p>
	</form>

<?php if ( count($lists) > 0 ):?>

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

	<table class="w100per every_other_row_even">
		<tr>
			<th class="w20per"></th>
			<th class="taleft">
				<a href="<?= h($sort_links["artist"]["link"]) ?>"><?= h($sort_links["artist"]["text"]) ?></a>
				/
				<a href="<?= h($sort_links["title"]["link"]) ?>"><?= h($sort_links["title"]["text"]) ?></a>
				( <a href="<?= h($sort_links["year"]["link"]) ?>"><?= h($sort_links["year"]["text"]) ?></a> )
			</th>
			<th></th>
		</tr>
<?php foreach($lists as $album): ?>
		<tr>
			<td>
				<img class="album_cover" src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/user.png" ?>" alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>" />
			</td>
			<td>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["id"]) ?>"><?= h( "{$album["artist"]} / {$album["title"]}") ?></a> (<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
			</td>
			<td>
			</td>
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

<?php else:?>
	<p class="error_message tacenter">not found.</p>
<?php endif;?>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>