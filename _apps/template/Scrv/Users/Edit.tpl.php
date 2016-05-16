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

	<div class="search_tab">
		<p>
			<ul class="tab">
				<li class="active"><a href="<?= h($base_path) ?>Users/Edit">編集</a></li>
				<li><a href="<?= h($base_path) ?>Users/EditPassword">パスワード変更</a></li>
				<li><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク作成</a></li>
			</ul>
		</p>
		<div class="search_type"></div>
		<div class="tabContent active">
			<form action="<?= h($base_path) ?>Users/Save" enctype="multipart/form-data" method="POST">
				<input type="hidden" name="token" value="<?= h($token) ?>" />
				<p><h3><?= h($login_user_data["username"]) ?></h3></p>
				<p><img class="user_photo" src="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></p>
				<p class="actions">
					<input type="file" name="file" id="id_file" accept="image/*" />
				</p>
				<p><textarea name="profile" id="id_profile" placeholder="your profile"><?= isset($login_user_data["profile"]) ? h($login_user_data["profile"]) : "" ?></textarea></p>
				<p class="actions"><input type="submit" value="save" ></p>
			</form>
<?php if (!isset($login_user_data["twitter_user_id"])):?>
			<p class="actions"><a href="javascript:;" id="id_users_twitter">twitter 連携</a></p>
			<form id="id_users_twitter_form" action="<?= h($base_path) ?>Users/Twitter" method="POST">
				<input type="hidden" name="authenticate" value="auth" />
			</form>
<?php endif; ?>
		</div>
	</div>

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