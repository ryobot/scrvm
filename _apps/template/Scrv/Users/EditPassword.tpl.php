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

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Edit Password</h2>
	</div>

<?php require __DIR__ . '/_editmenu.tpl.php'; ?>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="w3-panel w3-display-container w3-center w3-red">
		<span onclick="this.parentElement.style.display='none'" class="w3-display-topright w3-btn w3-red">X</span>
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="w3-padding w3-center w3-margin-bottom w3-card-2 w3-white">
		<form action="<?= h($base_path) ?>Users/SavePassword" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<p><input class="w3-input w3-border" type="password" name="current_password" id="id_current_password" value="" placeholder="現在のパスワード" required="required" /></p>
			<p><input class="w3-input w3-border" type="password" name="password" id="id_password" value="" placeholder="新しいパスワード" required="required" /></p>
			<p><input class="w3-input w3-border" type="password" name="password_re" id="id_password_re" value="" placeholder="新しいパスワード（再入力）" required="required" /></p>
			<p><input type="submit" class="w3-btn" value=" 変更する " ></p>
		</form>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</body>
</html>