<?php
/**
 * Users/EditPassword.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Users::EditPassword - <?= h($base_title) ?></title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Edit Password</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="user_edit">
<?php require __DIR__ . '/_editmenu.tpl.php'; ?>
		<div class="user_edit_area">
			<p>パスワードを変更します。</p>
			<form action="<?= h($base_path) ?>Users/SavePassword" method="POST">
				<input type="hidden" name="token" value="<?= h($token) ?>" />
				<p><input type="password" name="current_password" id="id_current_password" value="" placeholder="current password" required="required" /></p>
				<p><input type="password" name="password" id="id_password" value="" placeholder="new password" required="required" /></p>
				<p><input type="password" name="password_re" id="id_password_re" value="" placeholder="retype new password" required="required" /></p>
				<p class="actions"><input type="submit" value=" 保存する " ></p>
			</form>
		</div>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>