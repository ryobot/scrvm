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
<title>Users::Edit - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Edit User</h2>
	</div>

<?php require __DIR__ . '/_editmenu.tpl.php'; ?>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
		<div class="w3-padding w3-center w3-red">
<?php		foreach($error_messages as $key => $message): ?>
			<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
		</div>
<?php endif;?>

		<!-- profile -->
		<div class="w3-padding w3-center info">
			<form action="<?= h($base_path) ?>Users/Save" enctype="multipart/form-data" method="POST">
				<input type="hidden" name="token" value="<?= h($token) ?>" />
				<input class="displaynone" type="file" name="file" id="id_file" accept="image/*" />
				<div class="notice w3-small">アイコンを変更するには画像をタップしてください。</div>
				<span id="id_drop_zone">
					<img
						class="cover_user cursorpointer"
						src="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.svg" ?>"
						alt="<?= h($login_user_data["username"]) ?>"
						id="id_user_photo"
						data-src_org="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.svg" ?>"
					/>
				</span>
				<h5><?= h($login_user_data["username"]) ?></h5>
				<p><textarea name="profile" rows="5" class="w3-input" id="id_profile" placeholder="your profile"><?= isset($login_user_data["profile"]) ? h($login_user_data["profile"]) : "" ?></textarea></p>
				<p class="actions"><input type="submit" value=" 保存する " ></p>
			</form>
		</div>

		<!-- username -->
		<div class="w3-padding w3-center info">
			<h5>ユーザ名変更</h5>
			<form action="<?= h($base_path) ?>Users/SaveUsername" method="POST" id="id_form_changeusername">
				<input type="hidden" name="token" value="<?= h($token) ?>" />
				<p><input class="w3-input" type="text" name="username" id="id_username" value="<?= h($login_user_data["username"]) ?>" data-username_org="<?= h($login_user_data["username"]) ?>" placeholder="new username" /></p>
<?php if(!$is_only_twitter_login):?>
				<p><input class="w3-input" type="password" name="password" id="id_password" placeholder="current password" /></p>
<?php endif; ?>
				<p><input type="submit" value=" 変更する " ></p>
			</form>
		</div>

	</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

<script>
	// drop_zoneのみdrag drop可能に
	$("#id_drop_zone").on( "click.js", function(){
		$("#id_file").trigger("click");
	});
	// クリックアップロード時は onChange イベントで処理
	$("#id_file").on("change.js", function(){
		var files = $(this).prop( "files" );
		if ( files.length !== 1 ) {
			return false;
		}
		var file = files[0];
		var data = {
			name : file.name,
			size : file.size,
			type : file.type
		};
		if ( ! checkFileFormat( data.type, data.size ) ) {
			return false;
		}
		var fileReader = new FileReader();
		fileReader.readAsDataURL( file );
		fileReader.onload = function( event ) {
			displayImage( this.result );
			$("#id_drop_zone").removeClass().addClass("drop_zone_dragged");
		};
		fileReader.onloadend = function( event ){
		};
	});
	var checkFileFormat = function( type, size ) {
		if ( ! type.match( /^image\/(gif|png|jpeg)$/i ) ) {
			alert( "画像形式が不正です。\n(allowed jpeg/png/gif)" );
			return false;
		}
		return true;
	};
	var displayImage = function(base64){
		$("#id_user_photo").attr({src:base64});
	};

	// username変更
	$("#id_form_changeusername").on("submit.js", function(){
		var username = $.trim($("#id_username").val());
		var password = $.trim($("#id_password").val());
<?php if(!$is_only_twitter_login):?>
		if ( username === "" || password === "" ) {
			alert("ユーザ名またはパスワードが入力されていません。");
			return false;
		}
<?php else: ?>
		if ( username === "" ) {
			alert("ユーザ名が入力されていません。");
			return false;
		}
<?php endif; ?>
		$(this).submit();
		return false;
	});

</script>

</body>
</html>