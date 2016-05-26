<?php
/**
 * Users/ConnectTwitter.tpl.php
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
<div class="contents">

	<h2>Edit User</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="user_edit">
		<div class="displaytable w100per user_edit_menu">
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/Edit">編集</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/EditPassword">パスワード変更</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク作成</a></div>
			<div class="displaytablecell active"><a href="<?= h($base_path) ?>Users/ConnectTwitter">twitter 連携</a></div>
		</div>
		<div class="user_edit_area">
<?php if (isset($sess_twitter_access_token["oauth_token"])): ?>
			<p>現在 twitter 連携中です。連携を解除するには、以下手順を実行してください。</p>
			<ul style="list-style: disc;margin-left:1.5em;">
				<li>パソコンで twitter を開き、<a href="https://twitter.com/settings/applications">twitter / 設定 - アプリ連携</a> から scrv の許可を取り消す。</li>
				<li>syncreview をログアウト → 再ログイン。</li>
			</ul>
<?php else: ?>
			<p class="actions"><a href="javascript:;" id="id_users_twitter">twitter 連携</a></p>
			<form id="id_users_twitter_form" action="<?= h($base_path) ?>Users/Twitter" method="POST">
				<input type="hidden" name="authenticate" value="auth" />
			</form>
<?php endif; ?>
		</div>
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