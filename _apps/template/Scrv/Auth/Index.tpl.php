<?php
/**
 * Auth/Index.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Login - <?= h($base_title) ?></title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Login</h2>

	<div class="form_info">
<?php if(isset($error_messages) && count($error_messages) > 0): ?>
		<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
			<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
		</div>
<?php endif;?>
		<form action="<?= h($base_path) ?>Auth/Login" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<p><input type="text" name="username" id="id_user_id" value="<?= isset($post_params["username"]) ? h($post_params["username"]) : "" ?>" placeholder="username" /></p>
			<p><input type="password" name="password" id="id_password" placeholder="password" /></p>
			<p><label><input type="checkbox" name="autologin" id="id_autologin" value="1" /> ログイン状態を保持する</label></p>
			<p class="actions tacenter"><input type="submit" value=" login " /></p>
		</form>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>