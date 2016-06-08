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

		<!-- album lists -->
		<div class="w100per tacenter">
			<a href="<?= h($sort_links["artist"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort artist" class="img16x16" /><?= h($sort_links["artist"]["text"]) ?></a>
			/
			<a href="<?= h($sort_links["title"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort title" class="img16x16" /><?= h($sort_links["title"]["text"]) ?></a>
			/
			<a href="<?= h($sort_links["year"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort year" class="img16x16" /><?= h($sort_links["year"]["text"]) ?></a>
			/
			<a href="<?= h($sort_links["reviews"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort reviews" class="img16x16" /><?= h($sort_links["reviews"]["text"]) ?></a>
		</div>

<?php foreach($lists as $album): ?>
		<div class="info">
			<div class="displaytable w100per">
				<div class="displaytablecell tacenter album_cover">
					<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
						<img
							src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>"
							alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>"
						/>
					</a>
<?php if ( $is_login && $album["create_user_id"] === $login_user_data["id"] ): ?>
					<div class="actions mgt10px">
						<a href="<?= h($base_path) ?>Albums/Edit/id/<?= h($album["id"]) ?>">Edit</a>
					</div>
<?php endif; ?>
				</div>
				<div class="displaytablecell vtalgmiddle">
					<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
						<?= h($album["artist"]) ?> /
						<?= h($album["title"]) ?>
						(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
					</a>
<?php if($album["reviews"] > 0): ?>
					<div>
						<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
							<img src="<?= h($base_path) ?>img/reviews.svg" alt="reviews" class="img16x16" />
							<span class="vtalgmiddlea"><?= h($album["reviews"]) ?></span>
						</a>
					</div>
<?php endif;?>
				</div>
			</div>
<?php if($is_login): ?>
			<div class="w100per actions tacenter">
				<a href="<?= h($base_path) ?>Reviews/Add/id/<?= h($album["id"]) ?>">Write a Review</a>
			</div>
<?php endif; ?>
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

