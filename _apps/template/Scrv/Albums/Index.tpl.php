<?php
/**
 * Albums/Index.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Albums";
$most_prev_link = "{$_base_url}?" . hbq(array(
	"artist" => $artist,
	"page" => "1",
	"sort"   => $sort,
	"order"  => $order,
));
$prev_link = "{$_base_url}?" . hbq(array(
	"artist" => $artist,
	"page" => $pager["now_page"]-1,
	"sort"   => $sort,
	"order"  => $order,
));
$next_link = "{$_base_url}?" . hbq(array(
	"artist" => $artist,
	"page" => $pager["now_page"]+1,
	"sort"   => $sort,
	"order"  => $order,
));
$most_next_link = "{$_base_url}?" . hbq(array(
	"artist" => $artist,
	"page" => $pager["max_page"],
	"sort"   => $sort,
	"order"  => $order,
));

$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$_base_url}?" . hbq(array(
			"artist" => $artist,
			"page" => $nav["page"],
			"sort"   => $sort,
			"order"  => $order,
		)),
	);
}

// ソート用リンク
$order_type = $order === "asc" ? "desc" : "asc";
$sort_links = array(
	"artist" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"sort"   => "artist",
			"artist" => $artist,
			"order"  => $order_type,
		)),
		"text" => $sort === "artist" ? "[Artist]" : "Artist",
	),
	"title" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"sort"   => "title",
			"artist" => $artist,
			"order"  => $order_type,
		)),
		"text" => $sort === "title" ? "[Title]" : "Title",
	),
	"year" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"sort"   => "year",
			"artist" => $artist,
			"order"  => $order_type,
		)),
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
	<p class="actions tacenter"><a href="<?= h($base_path) ?>Albums/Add">Add Album</a></p>
<?php endif; ?>

	<form id="id_form_Albums_ArtistFilter" action="<?= h($base_path) ?>Albums" method="GET">
		<dl class="search">
			<dt><input type="text" name="artist" id="id_artist" value="<?= h($artist) ?>" placeholder="Artist Filter" /></dt>
			<dd><input type="submit" value="filter" /></dd>
		</dl>
	</form>

<?php if ( count($lists) > 0 ):?>

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

	<table class="w100per every_other_row_even">
		<tr>
			<td class="w80px"></td>
			<td class="taleft">
				<a href="<?= h($sort_links["artist"]["link"]) ?>"><?= h($sort_links["artist"]["text"]) ?></a>
				/
				<a href="<?= h($sort_links["title"]["link"]) ?>"><?= h($sort_links["title"]["text"]) ?></a>
				( <a href="<?= h($sort_links["year"]["link"]) ?>"><?= h($sort_links["year"]["text"]) ?></a> )
			</td>
			<td></td>
		</tr>
<?php foreach($lists as $album): ?>
		<tr>
			<td>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["id"]) ?>"><img class="album_cover" src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>" /></a>
			</td>
			<td>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["id"]) ?>">
					<?= h( "{$album["artist"]} / {$album["title"]}") ?>
					(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
				</a>
<?php if ( $is_login && $album["create_user_id"] === $login_user_data["id"] ): ?>
				<p class="actions"><a href="<?= h($base_path) ?>Albums/Edit?id=<?= h($album["id"]) ?>">Edit Album</a></p>
<?php endif; ?>
			</td>
			<td>
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

<?php else:?>
	<p class="error_message tacenter">not found.</p>
<?php endif;?>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>