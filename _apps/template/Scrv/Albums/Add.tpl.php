<?php
/**
 * Albums/Add.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Albums :: Add</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Add Album</h2>

	<form id="id_Albums_SearchArtist" action="javascript:;" method="POST">
		<table><tbody>
			<tr>
				<th>artist</th>
				<td><input type="text" name="artist" id="id_artist" value="" placeholder="king crimson"></td>
			</tr>
			<tr>
				<th>title</th>
				<td><input type="text" name="title" id="id_title" value="" placeholder="red"></td>
			</tr>
			<tr>
				<th></th>
				<td><input type="submit" id="id_submit" value="search" /></td>
			</tr>
		</tbody></table>
	</form>

	<div id="id_Albums_SearchArtist_result"></div>
	<div id="id_Albums_SearchImage_result"></div>

	<form id="id_Albums_AddRun" action="javascript:;" method="POST" style="display:none;">
		<input type="hidden" name="add_img_url" id="id_add_img_url" value="" />
		<p id="id_selected_img_url"></p>
		<table>
			<tr>
				<td>artist</td>
				<td><input type="text" name="add_artist" id="id_add_artist" value="" readonly="readonly" /></td>
				<td></td>
			</tr>
			<tr>
				<td>title</td>
				<td><input type="text" name="add_title" id="id_add_title" value="" readonly="readonly" /></td>
				<td></td>
			</tr>
			<tr>
				<td>year</td>
				<td><input type="text" name="add_year" id="id_add_year" value="" /></td>
				<td></td>
			</tr>
		</table>
		<table id="id_add_tracks"></table>
		<p><input type="submit" value="add album" /></p>
	</form>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){

	var cache_search_result = [];

	// search gracen
	$("#id_Albums_SearchArtist").on("submit.js", function(){

		var val_artist = $.trim($("#id_artist").val());
		var val_title = $.trim($("#id_title").val());
		if ( val_artist === "" ) {
			alert("artist名 は必須です。");
			return;
		}

		var $submit = $("#id_submit");
		$submit.attr({disabled:true}).val("searching...");

		$.ajax( "<?= h($base_path) ?>Albums/SearchArtist", {
			method : 'POST',
			dataType : 'json',
			data : {
				artist : val_artist,
				title : val_title
			}
		})
		.done(function(json){
			var $result = $("#id_Albums_SearchArtist_result");
			$result.html("").css({display:"none"});
			cache_search_result = json;
			if ( json.length === 0 ) {
				$result.append($("<p class='notfound' />").text("not found...")).slideToggle("middle");
				return;
			}
			$result.append($("<h4 />").text("album データを選択してください。"));
			for(var i=0,len=json.length; i<len; i++) {
				var artist = json[i].artist,
				title = json[i].title,
				year = json[i].year === "" ? "unknown" : json[i].year,
				tracks = json[i].tracks,
				track_list = [];
				for(var k=0,n=tracks.length; k<n; k++) {
					track_list.push( k+1 + "." + tracks[k] );
				}
				$result.append($("<div class='album_search_result' data-cache_index='"+i+"' />").append(
					$("<p />").text( artist + " / " + title + " (" + year + ")" ),
					$("<p />").text(track_list.join(" / "))
				).on("click.js", function(){

					$result.slideToggle("middle");

					// clickしたらその情報をformに反映、他の情報は削除
					var cache_index = $(this).attr("data-cache_index");
					var cache_data = cache_search_result[cache_index];

					$("#id_add_artist").val(cache_data["artist"]);
					$("#id_add_title").val(cache_data["title"]);
					$("#id_add_year").val(cache_data["year"]);
					setTracks(cache_data["tracks"]);

					$("#id_Albums_SearchArtist_result").html("");
					$("#id_Albums_AddRun").show();

					// データを選択してから画像検索
					var search_q = cache_data["artist"] + ' ' + cache_data["title"];
					searchImage(search_q.replace("'", "\'").replace('"','\"'));
				}));
			}
			$result.slideToggle("middle");

		})
		.fail(function(e){
			alert("system error.");
		})
		.always(function(){
			$submit.attr({disabled:false}).val("search");
		});
		return false;
	});

	function searchImage(q){
		$.ajax( "<?= h($base_path) ?>Albums/SearchImage", {
			method : 'POST',
			dataType : 'json',
			data : {q : q}
		})
		.done(function(json){
			var $result = $("#id_Albums_SearchImage_result");
			$result.html("").css({display:"none"});;
			if ( json.length === 0 ) {
				$result.append($("<p class='notfound' />").text("not found..."));
				return;
			}
			$result.append($("<h4 />").text("album カバーを選択してください。"));
			for(var i=0,len=json.length; i<len; i++) {
				var img = json[i];
				var $img = $("<img class='album_search_cover_result' />").attr({
					src:img
				}).on("click.js", function(){
					var selected_img_url = $(this).attr("src");
					$("#id_add_img_url").val(selected_img_url);
					$("#id_selected_img_url").fadeOut("fast", function(){
						$(this).html("").append($("<img />").attr({src:selected_img_url})).fadeIn("fast");
					});
				});
				$result.append($img);
			}
			$result.slideToggle("middle");
		})
		.fail(function(e){
			alert("image search system error.");
		})
		.always(function(){
		});
	}

	function setTracks(tracks)
	{
		var $id_add_tracks = $("#id_add_tracks").html("");
		var index = 0;
		for(var i=0,len=tracks.length; i<len; i++){
			index = i+1;
			$id_add_tracks.append(
				$("<tr />").attr({}).append(
					$("<td />").text("track " + index),
					$("<td />").append($('<input type="text" />').attr({
						name :"add_track_"+index,
						id :"id_add_track_"+index,
						value : tracks[i]
					})),
					$("<td />").append(
//						$("<button />").text("delete").on("click.js", function(){})
					)
				)
			);
		}
	}

	$("#id_Albums_AddRun").on("submit",function(){
		var artist = $.trim($("#id_add_artist").val());
		var title = $.trim($("#id_add_title").val());
		var year = $.trim($("#id_add_year").val());
		var tracks = [];
		$("[id^=id_add_track_]").each(function(){
			var trackname = $.trim($(this).val());
			if (trackname !== "") {
				tracks.push(trackname);
			}
		});
		var img_url = $.trim($("#id_add_img_url").val());

		$.ajax( "<?= h($base_path) ?>Albums/AddRun", {
			method : 'POST',
			dataType : 'json',
			data : {
				artist : artist,
				title : title,
				year : year,
				"tracks[]" : tracks,
				img_url : img_url
			}
		})
		.done(function(json){
			if (!json.status) {
				alert(json.messages.join("\n"));
			} else {
				location.href = "<?= h($base_path) ?>Albums/View?id=" + json.data.album_id;
			}
		})
		.fail(function(e){
			alert("system error.");
		})
		.always(function(){
		});
		return false;
	});

});
</script>

</body>
</html>