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
<title>Users::ConnectTwitter - <?= h($base_title) ?></title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Connect Twitter</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="user_edit">
<?php require __DIR__ . '/_editmenu.tpl.php'; ?>
		<div class="user_edit_area">
<?php if (isset($login_user_data["twitter_user_id"])): ?>
			<p>twitter連携を完全に解除するには、以下ボタンでtwitterの連携を解除したのち、パソコンで twitter を開き、<a href="https://twitter.com/settings/applications">twitter / 設定 - アプリ連携</a> から scrv の許可を取り消す作業が必要です。</p>
			<p class="actions"><a href="javascript:;" id="id_users_twitter_disconnect">twitter 連携を解除する</a></p>
			<form id="id_users_twitter_disconnect_form" action="<?= h($base_path) ?>Users/DisconnectTwitter" method="POST"></form>
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

	$("#id_users_twitter_disconnect").on("click.js", function(){
		if (confirm("twitter 連携を解除しますか？")) {
			$("#id_users_twitter_disconnect_form").submit();
		}
		return false;
	});

	$("#id_users_twitter").on("click.js", function(){
		$("#id_users_twitter_form").submit();
		return false;
	});

});
</script>

</body>
</html>