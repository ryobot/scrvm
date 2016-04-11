<?php
/**
 * Users/Add.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: New User</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>New User</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<form action="<?= h($base_path) ?>Users/AddNew" method="POST">
		<input type="hidden" name="token" value="<?= h($token) ?>" />
		<p><input type="text" name="username" id="id_username" value="<?= isset($post_params["username"]) ? h($post_params["username"]) : "" ?>" placeholder="username" /></p>
		<p><input type="password" name="password" id="id_password" value="" placeholder="password" /></p>
		<p><input type="submit" value="add user" ></p>
	</form>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>