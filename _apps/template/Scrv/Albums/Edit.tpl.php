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

	<form id="id_Albums_EditRun" enctype="multipart/form-data" action="javascript:;" method="POST" autocomplete="off">
		<input type="hidden" name="id" value="<?= h($id) ?>" />
		<input type="hidden" name="token" value="<?= h($token) ?>" />
		<table class="w100per" id="id_album_basic_info">
			<tr>
				<td class="w50px">cover</td>
				<td>
					<img src="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>" alt="<?= h("{$album["artist"]} / {$album["title"]}") ?>" class="album_view_cover" />
					<p class="actions"><input name="file" type="file" accept="image/*" /></p>
				</td>
			</tr>
			<tr>
				<td>artist</td>
				<td><input type="text" name="artist" id="id_artist" value="<?= h($album["artist"]) ?>" required="require" /></td>
			</tr>
			<tr>
				<td>title</td>
				<td><input type="text" name="title" id="id_title" value="<?= h($album["title"]) ?>" required="require" /></td>
			</tr>
			<tr>
				<td>year</td>
				<td><input type="text" name="year" id="id_year" value="<?= h($album["year"]) ?>" required="require" /></td>
			</tr>
		</table>
		<p> </p>
		<table class="w100per" id="id_tracks">
			<tr>
				<td class="w50px"></td>
				<td></td>
			</tr>
<?php foreach($tracks as $track): ?>
			<tr>
				<td>tr.<?= h($track["track_num"]) ?></td>
				<td><input
					type="text"
					name="tracks[]"
					id="id_track_num_<?= h($track["track_num"]) ?>"
					value="<?= h($track["track_title"]) ?>"
					required="require"
				/></td>
			</tr>
<?php endforeach; ?>
		</table>
		<div id="id_Albums_SearchImage_result"></div>
		<p class="actions tacenter"><input type="submit" value=" save " id="id_save" /></p>
	</form>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){
	var post_url = "<?= h($base_path) ?>Albums/EditRun";
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