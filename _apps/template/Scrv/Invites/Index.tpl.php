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
<title>Sign In - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Sign In</h2>
	</div>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="w3-padding w3-center w3-red">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="w3-padding w3-center w3-white">
		<form action="<?= h($base_path) ?>Invites/Add" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<p><input class="w3-input w3-border" type="text" name="username" id="id_username" value="<?= isset($post_params["username"]) ? h($post_params["username"]) : "" ?>" placeholder="your username" /></p>
			<p><input class="w3-input w3-border" type="password" name="password" id="id_password" value="" placeholder="login password" /></p>
			<p><input class="w3-input w3-border" type="password" name="password_re" id="id_password_re" value="" placeholder="retype login password" /></p>
			<p><input type="submit" value="SIGN IN" class="w3-btn w3-orange w3-round"></p>
		</form>

		<p>― または ―</p>

		<p class="w3-center"><button class="w3-btn w3-blue w3-round" href="javascript:;" id="id_users_twitter">Twitter でログイン</button></p>
		<form id="id_users_twitter_form" action="<?= h($base_path) ?>Auth/LoginTwitter" method="POST">
			<input type="hidden" name="authenticate" value="auth" />
		</form>
		<script>
			$("#id_users_twitter").on("click.js", function(){
				$("#id_users_twitter_form").submit();
				return false;
			});
		</script>

	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


</body>
</html>