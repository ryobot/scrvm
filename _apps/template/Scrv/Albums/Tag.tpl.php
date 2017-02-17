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

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Albums Tag &quot;<?= h($tag) ?>&quot; (<?= h($pager["total_count"]) ?>)</h2>
	</div>

<?php if ( count($lists) > 0 ):?>


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


<!-- sort -->
<div class="w3-center w3-padding">
	<a href="<?= h($sort_links["artist"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort artist" class="width_16px" /><?= h($sort_links["artist"]["text"]) ?></a>
	/
	<a href="<?= h($sort_links["title"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort title" class="width_16px" /><?= h($sort_links["title"]["text"]) ?></a>
	/
	<a href="<?= h($sort_links["year"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort year" class="width_16px" /><?= h($sort_links["year"]["text"]) ?></a>
</div>

<!-- lists -->
<div class="flex-container w3-row-padding w3-padding-16 w3-center">
<?php foreach($lists as $album): ?>
	<div class="w3-padding flex-item info col">
		<img class="cover w3-card-4" src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>" />

		<h5>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
				<span
					class="artist_title"
					data-artist="<?= h($album["artist"]) ?>"
					data-title="<?= h($album["title"]) ?>"
				><?= h("{$album["artist"]} / {$album["title"]}") ?></span>
				(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
			</a>
		</h5>

		<div class="reviews">
<?php if($album["reviews"] > 0): ?>
			<a
<?php if ($is_login): ?>
				href="<?= h($base_path) ?>Reviews/Add/id/<?= h($album["id"]) ?>"
<?php else: ?>
				href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>"
<?php endif; ?>
			>
				<span><img src="<?= h($base_path) ?>img/reviews.svg" alt="reviews" class="width_16px" /></span>
				<span class="vtalgmiddlea"><?= h($album["reviews"]) ?></span>
			</a>
<?php endif;?>
<?php if ($is_login && $album["create_user_id"] === $login_user_data["id"]): ?>
			<p><a href="<?= h($base_path) ?>Albums/Edit/id/<?= h($album["id"]) ?>">アルバム情報を編集する</a></p>
<?php endif; ?>
		</div>
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


<?php else:?>
	<p class="error_message w3-center info">not found.</p>
<?php endif;?>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</body>
</html>