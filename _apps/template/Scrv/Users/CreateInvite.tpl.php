<?php
/**
 * Users/Add.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Users::Create Invite - <?= h($base_title) ?></title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Create Invite</h2>

	<div class="user_edit">
<?php require __DIR__ . '/_editmenu.tpl.php'; ?>
		<div class="user_edit_area">
<?php if($login_user_data["role"] !== "admin"): ?>
			<p>リンク生成回数：残り <span id="id_can_be_invited_count"><?= h($can_be_invited_count) ?></span> 回</p>
<?php endif; ?>
<?php if($login_user_data["role"] === "admin" || $can_be_invited_count > 0): ?>
			<p class="actions" id="id_actions"><a href="javascript:;" id="id_create_invite">招待用リンクを作成する</a></p>
			<div id="id_created_link_area" class="displaynone">
				<p>下記リンクをコピーして相手にお知らせください。</p>
				<p><textarea id="id_created_link"></textarea></p>
			</div>
<?php else: ?>
<?php endif ;?>
		</div>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){
	$("#id_create_invite").on("click.js", function(){
		if (!confirm("are you sure ?")){
			return false;
		}

		$.ajax( '<?= h($base_path) ?>Users/CreateInviteNew', {
			method : 'POST',
			dataType : 'json',
			data : {token : "<?= h($token) ?>"}
		})
		.done(function(json){
			if (!json.status) {
				alert(json.messages.join("\n"));
				location.href="<?= h($base_path) ?>Users/CreateInvite";
				return false;
			}
			$("#id_actions").hide();
			$("#id_created_link").val(json.data.created_link);
			$("#id_created_link_area").slideToggle();
			$("#id_can_be_invited_count").text(json.data.can_be_invited_count);
		})
		.fail(function(e){
			alert("system error..");
		})
		.always(function(){
		});

		return false;
	});
});
</script>

</body>
</html>