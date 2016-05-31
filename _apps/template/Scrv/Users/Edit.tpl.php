<?php
/**
 * Users/Edit.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Users :: Edit</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Edit User</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="user_edit">
		<div class="displaytable w100per user_edit_menu">
			<div class="displaytablecell active"><a href="<?= h($base_path) ?>Users/Edit">プロフィール編集</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/EditPassword">パスワード変更</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク作成</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/ConnectTwitter">twitter 連携</a></div>
		</div>
		<div class="user_edit_area">
			<form action="<?= h($base_path) ?>Users/Save" enctype="multipart/form-data" method="POST">
				<input type="hidden" name="token" value="<?= h($token) ?>" />
				<p><h3><?= h($login_user_data["username"]) ?></h3></p>
				<p><img class="user_photo" src="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.svg" ?>" alt="" /></p>
				<p class="actions">
					<input type="file" name="file" id="id_file" accept="image/*" />
				</p>
				<p><textarea name="profile" id="id_profile" placeholder="your profile"><?= isset($login_user_data["profile"]) ? h($login_user_data["profile"]) : "" ?></textarea></p>
				<p class="actions"><input type="submit" value="save" ></p>
			</form>
		</div>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>