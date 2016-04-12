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
				<th></th>
				<th class="taleft">username</th>
				<th class="taleft"><?php if($is_login): ?>syncs<?php endif;?></th>
				<th class="taleft">reviews</th>
				<th></th>
			</tr>
<?php foreach($lists as $list): ?>
			<tr>
				<td><img class="user_photo" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.png" ?>" alt="<?= h($list["username"]) ?>" /></td>
				<td><a href="<?= h($base_path) ?>Users/View?id=<?= h($list["id"]) ?>"><?= h($list["username"]) ?></a></td>
				<td></td>
				<td><?= h($list["review_count"]) ?></td>
				<td class="actions">
<?php if( $is_login):?>
<?php		if ($login_user_data["role"] === "admin" || $login_user_data["id"] === $list["id"]):?>
					<a href="<?= h($base_path) ?>Users/Edit?id=<?= h($list["id"]) ?>">Edit</a>
<?php		endif;?>
<?php endif;?>
				</td>
			</tr>
<?php endforeach; unset($list) ?>
		</table>
	</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>