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
<?php require __DIR__ . '/_editmenu.tpl.php'; ?>
		<div class="user_edit_area">
			<form action="<?= h($base_path) ?>Users/Save" enctype="multipart/form-data" method="POST">
				<input type="hidden" name="token" value="<?= h($token) ?>" />
				<h3><?= h($login_user_data["username"]) ?></h3>
				<div class="displaytable w100per drop_zone" id="id_drop_zone">
					<div class="displaytablecell user_photo">
						<img
							src="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.svg" ?>"
							alt="<?= h($login_user_data["username"]) ?>"
							id="id_user_photo"
							data-src_org="<?= isset($login_user_data["img_file"]) ? "{$base_path}files/attachment/photo/{$login_user_data["img_file"]}" : "{$base_path}img/user.svg" ?>"
						/>
					</div>
					<div class="displaytablecell">
						<img src="<?= h($base_path) ?>img/image.svg" class="img16x16" alt="画像を変更する" />
						画像を変更する
					</div>
				</div>
				<p class="actions displaynone"><input type="file" name="file" id="id_file" accept="image/*" /></p>

				<p><textarea name="profile" id="id_profile" placeholder="your profile"><?= isset($login_user_data["profile"]) ? h($login_user_data["profile"]) : "" ?></textarea></p>
				<p class="actions"><input type="submit" value=" 保存する " ></p>
			</form>
		</div>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

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
</script>

</body>
</html>