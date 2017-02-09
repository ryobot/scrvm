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
<title>Albums::Edit - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Edit Album</h2>
	</div>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="w3-center w3-padding info">
		<form class="w3-form" id="id_Albums_EditRun" enctype="multipart/form-data" action="javascript:;" method="POST" autocomplete="off">
			<input type="hidden" name="id" value="<?= h($id) ?>" />
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<div id="id_tracks_hidden"></div>

			<div id="id_album_basic_info">
				<div class="edit_album_base">
					<p class="notice w3-small">カバーを変更するには画像をタップしてください。</p>
					<span class="drop_zone cursorpointer" id="id_drop_zone">
						<img
							src="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>"
							class="album_view_cover"
							id="id_album_view_cover"
							data-src_org="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>"
						/>
					</span>
					<p class="displaynone"><input name="file" type="file" id="id_file" accept="image/*" /></p>
				</div>

				<div class="edit_album_base">
					<table class="width_100per">
						<tr>
							<td>artist</td>
							<td><input class="w3-input" type="text" name="artist" id="id_artist" value="<?= h($album["artist"]) ?>" required="require" /></td>
						</tr>
						<tr>
							<td>title</td>
							<td><input class="w3-input" type="text" name="title" id="id_title" value="<?= h($album["title"]) ?>" required="require" /></td>
						</tr>
						<tr>
							<td>year</td>
							<td><input class="w3-input" type="text" name="year" id="id_year" value="<?= h($album["year"]) ?>" required="require" /></td>
						</tr>
					</table>
				</div>
			</div>

			<p>track list</p>

			<table id="id_track_list_wrapper" class="width_100per">
<?php foreach($tracks as $track): ?>
				<tr class="edit_album_base track_wrap">
					<td class="title_num"><?= h($track["track_num"]) ?>.</td>
					<td class="arrow_up"><img src="<?= h($base_path) ?>img/up-arrow.svg" class="width_16px cursorpointer" title="move up" /></td>
					<td class="arrow_down"><img src="<?= h($base_path) ?>img/down-arrow.svg" class="width_16px cursorpointer" title="move down" /></td>
					<td class="track_data">
						<input
							class="w3-input"
							type="text"
							name="_tracks[]"
							id="id_track_num_<?= h($track["track_num"]) ?>"
							data-id="<?= h($track["id"]) ?>"
							data-artist="<?= h($track["artist"]) ?>"
							data-album_id="<?= h($track["album_id"]) ?>"
							data-track_num="<?= h($track["track_num"]) ?>"
							data-track_title="<?= h($track["track_title"]) ?>"
							value="<?= h($track["track_title"]) ?>"
							required="require"
						/>
					</td>
				</tr>
<?php endforeach; ?>
			</table>

			<p><a href="javascript:void(0)" id="id_add_track">トラックを追加する</a></p>

			<p><input class="w3-btn" type="submit" value=" 保存する " id="id_save" /></p>

		</form>

		<!-- track template -->
		<table id="id_track_template" class="displaynone">
			<tr class="edit_album_base track_wrap">
				<td class="title_num"></td>
				<td class="arrow_up"><img src="<?= h($base_path) ?>img/up-arrow.svg" class="width_16px cursorpointer" title="move up" /></td>
				<td class="arrow_down"><img src="<?= h($base_path) ?>img/down-arrow.svg" class="width_16px cursorpointer" title="move down" /></td>
				<td class="track_data">
					<input
						class="w3-input"
						type="text"
						name="_tracks[]"
						id="id_track_num_"
						data-id=""
						data-artist="<?= h($tracks[0]["artist"]) ?>"
						data-album_id="<?= h($tracks[0]["album_id"]) ?>"
						data-track_num=""
						data-track_title=""
						value=""
						required="require"
					/>
				</td>
			</tr>
		</table>

	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


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

	// up and down
	$(".track_wrap").each(function(){
		var $row = $(this);
		$row.find(".arrow_up").on("click.js", function(){
			$row.insertBefore($row.prev());
		});
		$row.find(".arrow_down").on("click.js", function(){
			$row.insertAfter($row.next());
		});
	});

	// add track
	$("#id_add_track").on("click.js", function(){
		var $track_list = $("#id_track_list_wrapper");
		var $track_template = $("#id_track_template").children().clone(true);
		var next_track_count = $track_list.find("tr.edit_album_base").length + 1;
		$track_template.find(".title_num").html(next_track_count + ".");
		$track_template.find(".track_data > input").attr({id:"id_track_num_" + next_track_count});
		$track_list.append( $track_template );
	});

	// remove track
	$(".remove").on("click.js", function(){
		$(this).parent().remove();
	});

	// 保存時
	var post_url = BASE_PATH + "Albums/EditRun";
	$("#id_save").on("click.js", function(){
		if (!JSON.stringify){
			alert("ブラウザが未対応のため保存できません。\n最新のブラウザをお使いください…");
			return false;
		}

		if ( confirm("保存しますか？") ) {
			// tracks.value を json形式に {id:id, track_num:num, track_title:title}
			var $tracks_hidden = $("#id_tracks_hidden");
			$tracks_hidden.html("");
			$("#id_track_list_wrapper").find(".track_data > input").each(function(){
				var $track = $(this);
				var value = JSON.stringify({
					id:$track.attr("data-id"),
					album_id:$track.attr("data-album_id"),
					artist:$track.attr("data-artist"),
					track_num:$track.attr("data-track_num"),
					track_title_org:$track.attr("data-track_title"),
					track_title:$track.val()
				});
				$tracks_hidden.append(
					$('<input type="hidden" name="tracks[]" />').attr({value:value})
				);
			});
			$("#id_Albums_EditRun").attr({action:post_url}).submit();
		}
		return false;
	});
});
</script>

</body>
</html>