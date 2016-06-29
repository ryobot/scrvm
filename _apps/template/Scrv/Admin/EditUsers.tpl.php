<?php
/**
 * Admin/EditUsers.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Admin::EditUsers - <?= h($base_title) ?></title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<div class="contents">
		<h2>Admin::EditUsers</h2>
	</div>

	<!-- pager -->
	<div class="pager">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

	<div class="contents">
<?php foreach($users as $user): ?>
		<div
			class="user_info"
			id="id_user_<?= h($user["id"]) ?>"
			data-user_json="<?= h(json_encode($user)) ?>"
		>
			<h3><?= h($user["id"]) ?> :: <?= h($user["username"]) ?></h3>
			<div class="cover">
				<img src="<?= h($base_path) ?>files/attachment/photo/<?= h($user["img_file"]) ?>" alt="" onerror="this.src='<?= h($base_path) ?>img/user.svg';" />
			</div>
			<div class="detail">
				<ul>
					<li><a href="javascript:;" class="open_form_password" data-userid="<?= h($user["id"]) ?>">パスワード変更</a></li>
					<li><a href="javascript:;" class="open_form_invited_count" data-userid="<?= h($user["id"]) ?>">招待人数クリア</a></li>
				</ul>
			</div>
			<div id="id_form_area_<?= h($user["id"]) ?>">
				<div class="displaynone" id="id_form_password_<?= h($user["id"]) ?>">
					<form action="javascript:;" method="post" class="form_password" data-userid="<?= h($user["id"]) ?>">
						<p>新パスワード : <input type="text" name="password" value="" /></p>
						<p>新パスワード (再): <input type="text" name="password_re" value="" /></p>
						<p class="tacenter actions"><input type="submit" value="変更" /></p>
					</form>
				</div>
				<div class="displaynone" id="id_form_invited_count_<?= h($user["id"]) ?>">
					<form action="javascript:;" method="post" class="form_invited_count" data-userid="<?= h($user["id"]) ?>">
						<p class="tacenter">現在の招待人数：<span id="id_current_invited_count_<?= h($user["id"]) ?>"><?= h($user["invited_count"]) ?></span></p>
						<p class="tacenter actions"><input type="submit" value="招待人数をクリアする" /></p>
					</form>
				</div>
			</div>
		</div>
<?php endforeach; ?>
	</div>

	<!-- pager -->
	<div class="pager">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){

	// toggle関連
	$(".open_form_password").on("click.js", function(){
		var user_id = $(this).attr("data-userid");
		$("#id_form_area_"+user_id+" > div").slideUp("fast");
		$("#id_form_password_"+user_id).slideDown("fast");
	});
	$(".open_form_invited_count").on("click.js", function(){
		var user_id = $(this).attr("data-userid");
		$("#id_form_area_"+user_id+" > div").slideUp("fast");
		$("#id_form_invited_count_"+user_id).slideDown("fast");
	});

	// パスワード更新
	$(".form_password").on("submit.js", function(){
		var $this = $(this);
		var user_id = $this.attr("data-userid");
		var user_data = JSON.parse($("#id_user_"+user_id).attr("data-user_json"));
		var password = $.trim($this.find("input[name='password']").val());
		var password_re = $.trim($this.find("input[name='password_re']").val());
		if ( password === "" || password !== password_re ) {
			alert("パスワードが一致しません。");
			return false;
		}
		if (!confirm(user_data.username + " の新パスワードを「"+password+"」に設定しますか？")) {
			return false;
		}
		$.ajax( BASE_PATH + 'Admin/AjaxEditPassword', {
			method : 'POST',
			dataType : 'json',
			data : {
				id: user_data.id,
				username: user_data.username,
				password: user_data.password,
				password_new: password
			}
		})
		.done(function(json){
			if ( !json.status ) {
				alert(json.messages.join("\n\n"));
				return;
			}
			alert("パスワードは正常に更新されました。");
		})
		.fail(function(e){
			alert("システムエラーが発生しました。");
		})
		.always(function(){
		});
		return false;
	});

	// 招待人数クリア
	$(".form_invited_count").on("submit.js", function(){
		var $this = $(this);
		var user_id = $this.attr("data-userid");
		var user_data = JSON.parse($("#id_user_"+user_id).attr("data-user_json"));
		if (!confirm(user_data.username + " の招待人数をクリアしますか？")) {
			return false;
		}
		$.ajax( BASE_PATH + 'Admin/AjaxClearInvitedCount', {
			method : 'POST',
			dataType : 'json',
			data : {
				id: user_data.id,
				username: user_data.username
			}
		})
		.done(function(json){
			if ( !json.status ) {
				alert(json.messages.join("\n\n"));
				return;
			}
			$("#id_current_invited_count_" + user_id).text("0");
			alert("招待人数はクリアされました。");
		})
		.fail(function(e){
			alert("システムエラーが発生しました。");
		})
		.always(function(){
		});
		return false;
	});

});
</script>

</body>
</html>