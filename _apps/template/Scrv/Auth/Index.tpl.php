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

	<div class="twitter_login mgt10px">
		<p class="actions tacenter"><a href="javascript:;" id="id_users_twitter">twitter でログイン</a></p>

		<h4 class="tacenter">【既存の syncreview アカウントをお持ちの方へ】</h4>
		<p>既存の syncreview アカウントと Twitter ログインのアカウントを統合する場合は、必ず下記の順番で手続きをしてください。</p>
		<ol class="strong">
			<li>syncreview の username と password で通常通りログインする</li>
			<li>画面右上のユーザメニューから「edit」を選択する</li>
			<li>edit ページの「Twitter 連携」メニューから Twitter 連携の処理をする</li>
			<li>一旦ログアウトして、再度「Twitterでログイン」からログインする</li>
		</ol>
		<p>※先に Twitter 連携をせずに「Twitterでログイン」をすると、syncreview のアカウントと Twitter ログインのアカウントが別々に作成されてしまいます。ご注意ください。</p>

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

</div>
</body>
</html>