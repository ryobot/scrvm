<?php
/**
 * Users/Index.tpl.php
 * @author mgng
 */

$_base_url = $base_path . "Users";

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Users</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Users (<?= h($lists_count) ?>)</h2>

<?php if(count($lists) > 0): ?>

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

	<!-- user lists -->
	<div class="lists">
		<!-- sort -->
		<div class="w100per tacenter">
			<a href="<?= h($sort_links["username"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort username" class="img16x16" /><?= $sort_links["username"]["text"] ?></a>
			/
			<a href="<?= h($sort_links["review_count"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort reviews" class="img16x16" /><?= $sort_links["review_count"]["text"] ?></a>
<?php if($is_login):?>
			/
			<a href="<?= h($sort_links["sync_point"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort sync points" class="img16x16" /><?= $sort_links["sync_point"]["text"] ?></a>
<?php endif; ?>
		</div>

		<!-- lists -->
		<div class="user_list">
<?php foreach($lists as $list): ?>
			<div class="user_info">
				<h3><?= h($list["username"]) ?></h3>
				<div class="cover">
					<a href="<?= h($_base_url) ?>/View/id/<?= h($list["id"]) ?>"><img src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["username"]) ?>" /></a>
				</div>
				<div class="detail">
<?php if($list["review_count"] > 0): ?>
					<div class="displaytablecell">
						<a class="reviews" href="<?= h($_base_url) ?>/View/id/<?= h($list["id"]) ?>">
							<img src="<?= h($base_path) ?>img/reviews.svg" class="img16x16" alt="reviews" />
							<?= h($list["review_count"]) ?> reviews
						</a>
						&nbsp;&nbsp;
					</div>
<?php endif; ?>
<?php if($is_login && isset($list["sync_point"]) && $list["sync_point"] !== 0):?>
					<div class="displaytablecella">
						<a class="syncs" href="<?= h($_base_url) ?>/Syncs/id/<?= h($list["id"]) ?>">
							<img src="<?= h($base_path) ?>img/sync.svg" class="img16x16" alt="syncs" />
							<?= h($list["sync_point"]) ?> pt
						</a>
					</div>
<?php endif; ?>
<?php if(isset($list["has_invited_user_id"])): ?>
					<div>
						&laquo; invited from <a href="<?= h($_base_url) ?>/View/id/<?= h($list["has_invited_user_id"]) ?>"><?= h($list["has_invited_username"]) ?></a>
						<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($list["has_invited_img_file"]) ? "files/attachment/photo/{$list["has_invited_img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["has_invited_username"]) ?>" />
					</div>
<?php endif; ?>
				</div>
			</div>
<?php endforeach; unset($list) ?>
		</div>


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

<?php endif; ?>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>