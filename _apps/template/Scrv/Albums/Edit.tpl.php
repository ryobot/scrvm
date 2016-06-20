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

	<div class="form_info">
		<form id="id_Albums_EditRun" enctype="multipart/form-data" action="javascript:;" method="POST" autocomplete="off">
			<input type="hidden" name="id" value="<?= h($id) ?>" />
			<input type="hidden" name="token" value="<?= h($token) ?>" />

			<div id="id_album_basic_info">
				<div class="edit_album_base">
					<div class="title">cover</div>
					<div class="data drop_zone" id="id_drop_zone">
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

				<div class="edit_album_base">
					<div class="title">artist</div>
					<div class="data">
						<input type="text" name="artist" id="id_artist" value="<?= h($album["artist"]) ?>" required="require" />
					</div>
				</div>
				<div class="edit_album_base">
					<div class="title">title</div>
					<div class="data">
						<input type="text" name="title" id="id_title" value="<?= h($album["title"]) ?>" required="require" />
					</div>
				</div>
				<div class="edit_album_base">
					<div class="title">year</div>
					<div class="data">
						<input type="text" name="year" id="id_year" value="<?= h($album["year"]) ?>" required="require" />
					</div>
				</div>
			</div>

			<div class="tacenter">- - - - -</div>

			<div id="id_track_list_wrapper">
<?php foreach($tracks as $track): ?>
				<div class="edit_album_base track_wrap">
					<div class="title_num"><?= h($track["track_num"]) ?>.</div>

					<div class="data arrow_up">
						<img src="<?= h($base_path) ?>img/up-arrow.svg" class="img16x16" alt="move up" title="move up" />
					</div>
					<div class="data arrow_down">
						<img src="<?= h($base_path) ?>img/down-arrow.svg" class="img16x16" alt="move down" title="move down" />
					</div>

					<div class="data track_data">
						<input
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
					</div>
<!--					<div class="data remove">
						<img src="<?= h($base_path) ?>img/dustbox.svg" class="img16x16" alt="remove track" title="remove track" />
					</div>-->
				</div>
<?php endforeach; ?>
			</div>

			<div class="tacenter">
				add <img id="id_add_track" class="img16x16" src="<?= h($base_path) ?>img/add_track.svg" alt="add track" title="add track" /> track
			</div>

			<p class="actions tacenter mgt10px"><input type="submit" value=" 保存する " id="id_save" /></p>

			<div id="id_tracks_hidden"></div>
		</form>

		<!-- track template -->
		<div id="id_track_template" class="displaynone">
			<div class="edit_album_base track_wrap">
				<div class="title_num"></div>
<!--				<div class="data arrow_up">
					<img src="<?= h($base_path) ?>img/up-arrow.svg" class="img16x16" alt="move up" title="move up" />
				</div>
				<div class="data arrow_down">
					<img src="<?= h($base_path) ?>img/down-arrow.svg" class="img16x16" alt="move down" title="move down" />
				</div>-->
				<div class="data track_data">
					<input
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
				</div>
				<div class="data remove">
					<img src="<?= h($base_path) ?>img/dustbox.svg" class="img16x16" alt="remove track" title="remove track" />
				</div>
			</div>
		</div>

	</div>

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
		var next_track_count = $track_list.find("div.edit_album_base").length + 1;
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
			$("#id_track_list_wrapper > .edit_album_base > .track_data > input").each(function(){
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