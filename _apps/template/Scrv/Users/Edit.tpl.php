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
		<p><input type="text" name="username" id="id_username" value="<?= h($login_user_data["username"]) ?>" placeholder="username" /></p>
		<p><img class="user_photo" src="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></p>
		<p class="actions">
			<input type="file" name="file" id="id_file" accept=".jpg,.gif,.png,image/gif,image/jpeg,image/png" />
		</p>
		<p><input type="password" name="password" id="id_passowrd" value="" placeholder="current password" required="required" /></p>
		<p class="actions"><input type="submit" value="save" ></p>
	</form>

<?php if (!isset($login_user_data["twitter_user_id"])):?>
	<p><hr /></p>
	<p class="actions"><a href="javascript:;" id="id_users_twitter">twitter 連携</a></p>
	<form id="id_users_twitter_form" action="<?= h($base_path) ?>Users/Twitter" method="POST">
		<input type="hidden" name="authenticate" value="auth" />
	</form>
<?php endif; ?>

	<p><hr /></p>

	<p><a href="<?= h($base_path) ?>Users/Edit">Edit User</a></p>
	<p><a href="<?= h($base_path) ?>Users/EditPassword">Edit Password</a>	</p>
	<p><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク生成</a></p>


<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){
	$("#id_users_twitter").on("click.js", function(){
		$("#id_users_twitter_form").submit();
		return false;
	});
});
</script>

</body>
</html>