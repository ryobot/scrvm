<?php
/**
 * Albums/Tag.tpl.php
 * @author mgng
 */

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Albums::Tag::<?= h($tag) ?> - <?= h($base_title) ?></title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">
	<h2>Albums Tag &quot;<?= h($tag) ?>&quot; (<?= h($pager["total_count"]) ?>)</h2>
</div>

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

<?php if( $is_login ): ?>
<div class="info tacenter actions">
	<a href="<?= h($base_path) ?>Albums/Add/type/artist/q/<?= urlencode($tag) ?>" class="add_album">
		<img src="<?= h($base_path) ?>img/add_album.svg" alt="add album" title="add album" class="img16x16" />
		<?= h($tag) ?> の他の Album を追加する
	</a>
</div>
<?php endif; ?>

<!-- lists -->
<div class="review_list">
<?php foreach($lists as $album): ?>
	<div class="album_info">
		<div class="cover">
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>"><img src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>" /></a>
		</div>
		<div class="detail">
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
				<?= h( "{$album["artist"]}") ?><br />
				<?= h( "{$album["title"]}") ?><br />
				(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
			</a>
		</div>
		<div class="reviews">
<?php if($album["reviews"] > 0): ?>
			<a
<?php if ($is_login): ?>
				href="<?= h($base_path) ?>Reviews/Add/id/<?= h($album["id"]) ?>"
<?php else: ?>
				href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>"
<?php endif; ?>
			>
				<span><img src="<?= h($base_path) ?>img/reviews.svg" alt="reviews" class="img16x16" /></span>
				<span class="vtalgmiddlea"><?= h($album["reviews"]) ?></span>
			</a>
<?php endif;?>
		</div>
	</div>
<?php endforeach; ?>
</div>

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

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>