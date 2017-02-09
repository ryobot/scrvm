<?php
/**
 * Invites/Index.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Invites - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">User Add</h2>
	</div>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="w3-padding w3-center w3-red">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="w3-padding w3-center info">
		<form action="<?= h($base_path) ?>Invites/Add" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<p><input class="w3-input" type="text" name="username" id="id_username" value="<?= isset($post_params["username"]) ? h($post_params["username"]) : "" ?>" placeholder="username" /></p>
			<p><input class="w3-input" type="password" name="password" id="id_password" value="" placeholder="password" /></p>
			<p><input class="w3-input" type="password" name="password_re" id="id_password_re" value="" placeholder="retype password" /></p>
			<p><input type="submit" value="作成" ></p>
		</form>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


</body>
</html>