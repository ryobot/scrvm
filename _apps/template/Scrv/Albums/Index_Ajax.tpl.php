<?php
/**
 * Albums/Index_Ajax.tpl.php
 * 部分書き換え用
 * @author mgng
 */

?>

<script>
$("#id_total_count").text(<?= h($pager["total_count"]) ?>);
</script>

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

		<!-- album order -->
		<div class="w100per tacenter">
			<a href="<?= h($sort_links["artist"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort artist" class="img16x16" /><?= h($sort_links["artist"]["text"]) ?></a>
			/
			<a href="<?= h($sort_links["title"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort title" class="img16x16" /><?= h($sort_links["title"]["text"]) ?></a>
			/
			<a href="<?= h($sort_links["year"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort year" class="img16x16" /><?= h($sort_links["year"]["text"]) ?></a>
			/
			<a href="<?= h($sort_links["reviews"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort reviews" class="img16x16" /><?= h($sort_links["reviews"]["text"]) ?></a>
		</div>

		<!-- album lists -->
		<div class="album_lists" id="id_album_lists">
<?php foreach($lists as $album): ?>
			<div class="album_info">
				<div class="cover">
					<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
						<img
							src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>"
							alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>"
						/>
					</a>
<?php if ($is_login && $album["create_user_id"] === $login_user_data["id"]): ?>
					<div class="w100per actions tacenter mgt10px">
						<a href="<?= h($base_path) ?>Albums/Edit/id/<?= h($album["id"]) ?>">Edit</a>
					</div>
<?php endif; ?>
				</div>
				<div class="detail">
					<div class="album_artist"><a href="<?= h($base_path) ?>Albums/Tag/tag/<?= urlencode($album["artist"]) ?>"><?= h($album["artist"]) ?></a></div>
					<div class="album_title"><a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>"><?= h($album["title"]) ?></a></div>
					<div class="album_year"><a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)</a></div>
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
		<div class="info">
			<p class="error_message tacenter strong">見つかりませんでした。</p>
<?php		if ($is_login): ?>
			<p class="tacenter strong">
				<a href="<?= h($base_path) ?>Albums/Add" class="add_album"><img src="<?= h($base_path) ?>img/add_album.svg" alt="add album" title="add album" class="img24x24" /></a>
				からアルバムを追加できます。
			</p>
<?php		endif; ?>
		</div>
<?php endif;?>

