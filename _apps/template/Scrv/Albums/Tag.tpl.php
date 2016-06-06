<?php
/**
 * Albums/Tag.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Albums/Tag";
$most_prev_link = "{$_base_url}?" . hbq(array(
	"tag" => $tag,
	"artist" => $artist,
	"page" => "1",
	"sort"   => $sort,
	"order"  => $order,
));
$prev_link = "{$_base_url}?" . hbq(array(
	"tag" => $tag,
	"artist" => $artist,
	"page" => $pager["now_page"]-1,
	"sort"   => $sort,
	"order"  => $order,
));
$next_link = "{$_base_url}?" . hbq(array(
	"tag" => $tag,
	"artist" => $artist,
	"page" => $pager["now_page"]+1,
	"sort"   => $sort,
	"order"  => $order,
));
$most_next_link = "{$_base_url}?" . hbq(array(
	"tag" => $tag,
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
			"tag" => $tag,
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
			"tag" => $tag,
			"sort"   => "artist",
			"artist" => $artist,
			"order"  => $order_type,
		)),
		"text" => $sort === "artist" ? "[Artist]" : "Artist",
	),
	"title" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"tag" => $tag,
			"sort"   => "title",
			"artist" => $artist,
			"order"  => $order_type,
		)),
		"text" => $sort === "title" ? "[Title]" : "Title",
	),
	"year" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"tag" => $tag,
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
<title><?= h($base_title) ?> :: Albums :: Tag :: <?= h($tag) ?></title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Albums Tag &quot;<?= h($tag) ?>&quot; (<?= h($pager["total_count"]) ?>)</h2>

<?php if ( count($lists) > 0 ):?>

	<!-- pager -->
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

	<!-- sort -->
	<div class="w100per tacenter">
		<a href="<?= h($sort_links["artist"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort artist" class="img16x16" /><?= h($sort_links["artist"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["title"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort title" class="img16x16" /><?= h($sort_links["title"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["year"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort year" class="img16x16" /><?= h($sort_links["year"]["text"]) ?></a>
	</div>

	<!-- lists -->

<?php if( $is_login ): ?>
	<div class="info">
		<div>
			<a href="<?= h($base_path) ?>Albums/Add/type/artist/q/<?= urlencode($tag) ?>" class="add_album">
				<img src="<?= h($base_path) ?>img/add_album.svg" alt="add album" title="add album" class="img24x24" />
				<?= h($tag) ?> の他の Album を追加する
			</a>
		</div>
	</div>
<?php endif; ?>

<?php foreach($lists as $album): ?>
	<div class="displaytable w100per info">
		<div class="displaytablecell album_cover">
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>"><img src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>" /></a>
		</div>
		<div class="displaytablecell vtalgmiddle">
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
				<?= h( "{$album["artist"]} / {$album["title"]}") ?>
				(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
			</a>
		</div>
	</div>
<?php endforeach; ?>

	<!-- pager -->
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

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>