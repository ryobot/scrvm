<?php
/**
 * Posts/Index.tpl.php
 * @author mgng
 */
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

	<h2>Users</h2>

	<div class="lists">
		<table class="w100per every_other_row_even">
			<tr>
				<th class="taleft w20per">user</th>
<?php if($is_login): ?>
				<th class="taleft w20per">syncs</th>
<?php endif;?>
				<th class="taleft">reviews</th>
			</tr>
<?php foreach($lists as $list): ?>
			<tr>
				<td>
					<p><a href="<?= h($base_path) ?>Users/View?id=<?= h($list["id"]) ?>"><?= h($list["username"]) ?></a></p>
					<p><a href="<?= h($base_path) ?>Users/View?id=<?= h($list["id"]) ?>"><img class="user_photo" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.png" ?>" alt="<?= h($list["username"]) ?>" /></a></p>
<?php if( $is_login):?>
<?php		if ($login_user_data["id"] === $list["id"]):?>
					<p class="actions"><a href="<?= h($base_path) ?>Users/Edit">Edit</a></p>
<?php		endif;?>
<?php endif;?>
				</td>
<?php if($is_login):?>
				<td><?= isset($list["sync_point"]) ? h($list["sync_point"]) : "-" ?></td>
<?php endif; ?>
				<td><?= h($list["review_count"]) ?></td>
			</tr>
<?php endforeach; unset($list) ?>
		</table>
	</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>