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

	<h2>Edit User</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<form action="<?= h($base_path) ?>Users/Save" enctype="multipart/form-data" method="POST">
		<input type="hidden" name="token" value="<?= h($token) ?>" />
		<input type="hidden" name="user_id" value="<?= h($user_id) ?>" />
		<p><input type="text" name="username" id="id_username" value="<?= h($post_params["username"]) ?>" placeholder="username" /></p>
		<p><input type="password" name="password" id="id_password" value="" placeholder="password" /></p>
		<p><input type="file" name="file" id="id_file" /></p>
		<p><img src="<?= isset($post_params["img_file"]) ? "{$base_path}files/attachment/photo/{$post_params["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></p>
		<p><input type="submit" value="save" ></p>
	</form>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>