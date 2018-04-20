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
<title>Users - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Users (<?= h($lists_count) ?>)</h2>
	</div>

<?php if(count($lists) > 0): ?>

<?php if(count($pager["nav_list"])>0): ?>
	<!-- pager -->
	<div class="w3-padding-top w3-padding-bottom w3-center">
		<div class="w3-bar">
<?php if($pager["prev"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($prev_link) ?>">&laquo;</a>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<a class="w3-button w3-circle <?= $nav["active"] ? "w3-indigo" : "w3-hover-indigo" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($next_link) ?>">&raquo;</a>
<?php endif;?>
		</div>
	</div>
<?php endif; ?>

<!-- sort -->
<div class="w3-center w3-padding">
	<a href="<?= h($sort_links["username"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort username" class="width_16px" /><?= $sort_links["username"]["text"] ?></a>
	/
	<a href="<?= h($sort_links["review_count"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort reviews" class="width_16px" /><?= $sort_links["review_count"]["text"] ?></a>
<?php if($is_login):?>
	/
	<a href="<?= h($sort_links["sync_point"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort sync points" class="width_16px" /><?= $sort_links["sync_point"]["text"] ?></a>
<?php endif; ?>
</div>

<!-- lists -->
<div class="flex-container w3-center">
<?php foreach($lists as $list): ?>
	<div class="w3-padding flex-item col w3-margin-bottom w3-card-2 w3-white">
		<p><a href="<?= h($_base_url) ?>/View/id/<?= h($list["id"]) ?>"><img class="cover_user" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["username"]) ?>" /></a></p>
		<h5><a href="<?= h($_base_url) ?>/View/id/<?= h($list["id"]) ?>"><?= h($list["username"]) ?></a></h5>
		<p>
<?php if($list["review_count"] > 0): ?>
			<span>
				<a class="reviews" href="<?= h($_base_url) ?>/View/id/<?= h($list["id"]) ?>">
					<img src="<?= h($base_path) ?>img/reviews.svg" class="width_16px" alt="reviews" />
					<?= h($list["review_count"]) ?>
				</a>
			</span>
<?php endif; ?>
<?php if($is_login && isset($list["sync_point"]) && $list["sync_point"] !== 0):?>
			&nbsp;<span>
				<a class="syncs" href="<?= h($_base_url) ?>/Syncs/id/<?= h($list["id"]) ?>">
					<img src="<?= h($base_path) ?>img/sync.svg" class="width_16px" alt="syncs" />
					<?= h($list["sync_point"]) ?> pt
				</a>
			</span>
<?php endif; ?>
		</p>
		<p>
<?php if(isset($list["profile"]) && $list["profile"] !== ""): ?>
			<?= linkIt(h($list["profile"])) ?>
<?php endif; ?>
<?php if(isset($list["has_invited_user_id"])): ?>
			<p class="invitedfrom">
				(invited from
				<a href="<?= h($_base_url) ?>/View/id/<?= h($list["has_invited_user_id"]) ?>">
					<?= h($list["has_invited_username"]) ?>
				</a>)
			</p>
<?php endif; ?>
		</p>
	</div>
<?php endforeach; unset($list) ?>
</div>

<?php if(count($pager["nav_list"])>0): ?>
	<!-- pager -->
	<div class="w3-padding-top w3-padding-bottom w3-center">
		<div class="w3-bar">
<?php if($pager["prev"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($prev_link) ?>">&laquo;</a>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<a class="w3-button w3-circle <?= $nav["active"] ? "w3-indigo" : "w3-hover-indigo" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<a class="w3-button w3-circle w3-hover-indigo" href="<?= h($next_link) ?>">&raquo;</a>
<?php endif;?>
		</div>
	</div>
<?php endif; ?>

<?php endif; ?>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</body>
</html>