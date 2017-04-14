<?php
/**
 * Albums/Index_Ajax.tpl.php
 * 部分書き換え用
 * @author mgng
 */
?>

	<script>
	;$("#id_total_count").text(<?= h($pager["total_count"]) ?>);
	</script>

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


	<!-- album order -->
	<div class="w3-center w3-padding">
		<a href="<?= h($sort_links["artist"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" title="sort artist" class="width_16px" /><?= h($sort_links["artist"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["title"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" title="sort title" class="width_16px" /><?= h($sort_links["title"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["year"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" title="sort year" class="width_16px" /><?= h($sort_links["year"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["reviews"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" title="sort reviews" class="width_16px" /><?= h($sort_links["reviews"]["text"]) ?></a>
	</div>

	<!-- album lists -->
	<div class="flex-container w3-row-padding w3-padding-16 w3-center">
<?php foreach($lists as $album): ?>
		<div class="w3-padding w3-margin-bottom flex-item w3-white w3-card-2 col">
			<div><img
				class="cover w3-card-4"
				src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>"
				alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>"
			/></div>
			<h5>
				<a href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>">
					<span><?= h($album["title"]) ?></span>
					<br />
					<span class="w3-small"><?= h($album["artist"]) ?> (<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "?" ?>)</span>
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
	<div class="w3-center w3-padding info">
		<p class="error_message">見つかりませんでした。</p>
	</div>
<?php endif;?>

	<script>
	;$(function(){
	});
	</script>

