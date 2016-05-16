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
<title><?= h($base_title) ?> :: Users :: EditPassword</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Edit Password</h2>

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
				<li><a href="<?= h($base_path) ?>Users/Edit">編集</a></li>
				<li class="active"><a href="<?= h($base_path) ?>Users/EditPassword">パスワード変更</a></li>
				<li><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク作成</a></li>
			</ul>
		</p>
		<div class="search_type"></div>
		<div class="tabContent active">
			<p><?= h($login_user_data["username"]) ?> のパスワードを変更します。</p>
			<p><img class="user_photo" src="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></p>
			<form action="<?= h($base_path) ?>Users/SavePassword" method="POST">
				<input type="hidden" name="token" value="<?= h($token) ?>" />
				<table class="w100per">
					<tr>
						<td>
							<p><input type="password" name="current_password" id="id_current_password" value="" placeholder="current password" required="required" /></p>
						</td>
					</tr>
					<tr>
						<td>
							<p><input type="password" name="password" id="id_password" value="" placeholder="new password" required="required" /></p>
						</td>
					</tr>
					<tr>
						<td>
							<p><input type="password" name="password_re" id="id_password_re" value="" placeholder="retype new password" required="required" /></p>
						</td>
					</tr>
					<tr>
						<td>
							<p class="actions"><input type="submit" value="save" ></p>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

</body>
</html>