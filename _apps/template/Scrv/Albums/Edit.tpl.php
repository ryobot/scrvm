<?php
/**
 * Albums/Edit.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Albums :: Edit</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Edit Album</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<form id="id_Albums_EditRun" enctype="multipart/form-data" action="javascript:;" method="POST" autocomplete="off" class="form_info">
		<input type="hidden" name="id" value="<?= h($id) ?>" />
		<input type="hidden" name="token" value="<?= h($token) ?>" />
		<div class="displaytable w100per mgb5px">
			<div class="displaytablecell w50px">cover</div>
			<div class="displaytablecell drop_zone" id="id_drop_zone">
				<div class="displaytablecell w120px">
					<img
						src="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>"
						alt="<?= h("{$album["artist"]} / {$album["title"]}") ?>"
						class="album_view_cover"
						id="id_album_view_cover"
						data-src_org="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>"
					/>
				</div>
				<div class="displaytablecell pdl10px">
					<div>
						<img src="<?= h($base_path) ?>img/image.svg" class="img16x16" alt="画像を変更する" />
						画像を変更する
					</div>
				</div>
			</div>
			<p class="actions displaynone"><input name="file" type="file" id="id_file" accept="image/*" /></p>
		</div>
		<div class="displaytable w100per">
			<div class="displaytablecell w50px">artist</div>
			<div class="displaytablecell">
				<input type="text" name="artist" id="id_artist" value="<?= h($album["artist"]) ?>" required="require" />
			</div>
		</div>
		<div class="displaytable w100per">
			<div class="displaytablecell w50px">title</div>
			<div class="displaytablecell">
				<input type="text" name="title" id="id_title" value="<?= h($album["title"]) ?>" required="require" />
			</div>
		</div>
		<div class="displaytable w100per">
			<div class="displaytablecell w50px">year</div>
			<div class="displaytablecell">
				<input type="text" name="year" id="id_year" value="<?= h($album["year"]) ?>" required="require" />
			</div>
		</div>
		<p> </p>
<?php foreach($tracks as $track): ?>
		<div class="displaytable w100per">
			<div class="displaytablecell w50px">tr.<?= h($track["track_num"]) ?></div>
			<div class="displaytablecell">
				<input
					type="text"
					name="tracks[]"
					id="id_track_num_<?= h($track["track_num"]) ?>"
					value="<?= h($track["track_title"]) ?>"
					required="require"
				/>
			</div>
		</div>
<?php endforeach; ?>
		<p class="actions tacenter"><input type="submit" value=" 保存する " id="id_save" /></p>
	</form>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>

;$(function(){

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
		$("#id_album_view_cover").attr({src:base64});
	};

	var post_url = BASE_PATH + "Albums/EditRun";
	$("#id_save").on("click.js", function(){
		if ( confirm("are you sure ?") ) {
			$("#id_Albums_EditRun").attr({action:post_url}).submit();
		}
		return false;
	});
});
</script>

</body>
</html>