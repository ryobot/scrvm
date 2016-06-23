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
<title>Albums::Add - <?= h($base_title) ?></title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Add Album</h2>

	<div class="form_info">
		<form id="id_Albums_SearchArtist" action="javascript:;" method="POST">
			<div class="displaytable w100per tacenter">
				<div class="displaytablecell w50per">
					<label><input type="radio" name="search_type" id="id_search_type_gracenote" value="gracenote" checked="checked"> <img src="<?= h($base_path) ?>img/logo_gracenote.png" alt="gracenote" title="gracenote" class="w80px" /></label>
				</div>
				<div class="displaytablecell w50per">
					<label><input type="radio" name="search_type" id="id_search_type_discogs" value="discogs"> <img src="<?= h($base_path) ?>img/logo_discogs.png" alt="discogs" title="discogs" class="w80px" /></label>
				</div>
			</div>

			<p><input type="text" name="artist" id="id_artist" value="<?= $type==="artist" ? h($q) : "" ?>" placeholder="artist name" /></p>
			<p><input type="text" name="title" id="id_title" value="<?= $type==="title" ? h($q) : "" ?>" placeholder="album title" /></p>
			<p><input type="text" name="track" id="id_track" value="" placeholder="track title" /></p>
			<p>※インディーズの新譜はあまりヒットしません。</p>
			<p class="actions tacenter"><input type="submit" id="id_submit" value="search" /></p>
		</form>
	</div>

	<div id="id_Albums_SearchArtist_result"></div>

	<div id="id_Albums_AddRun" class="displaynone">
		<input type="hidden" name="add_img_url" id="id_add_img_url" value="" />
		<h4>album情報を確認・修正してください。</h4>
		<div class="info">
			<table class="w100per">
				<tr>
					<td class="w50px">artist</td>
					<td><input type="text" name="add_artist" id="id_add_artist" value="" /></td>
					<td></td>
				</tr>
				<tr>
					<td>title</td>
					<td><input type="text" name="add_title" id="id_add_title" value="" /></td>
					<td></td>
				</tr>
				<tr>
					<td>year</td>
					<td><input type="text" name="add_year" id="id_add_year" value="" /></td>
					<td></td>
				</tr>
			</table>
			<p> </p>
			<table class="w100per" id="id_add_tracks"></table>
		</div>

		<div id="id_Albums_SearchImage_result_wrapper">
			<h4>album カバーを選択してください（あとで変更も可能です）。</h4>
			<div class="info">
				<form action="javascript:;" id="id_image_search_form">
					<p><input type="text" name="search_q" id="id_search_q" value="" placeholder="search image" /></p>
					<p class="actions tacenter"><input
						type="submit"
						value="image search"
						id="id_image_search_form_submit"
						data-label_on="image search"
						data-label_off="searching..."
					/></p>
				</form>
				<p class="actions" id="id_selected_img_url"></p>
				<div id="id_Albums_SearchImage_result"></div>
			</div>
		</div>

		<form id="id_Albums_AddRun_Form" action="javascript:;" method="POST">
			<p class="actions tacenter"><input type="submit" value=" add album " /></p>
		</form>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){

	var cache_search_result = [];

	// search gracen
	$("#id_Albums_SearchArtist").on("submit.js", function(){

		var val_artist = $.trim($("#id_artist").val());
		var val_title = $.trim($("#id_title").val());
		var val_track = $.trim($("#id_track").val());
		var val_search_type = $.trim($('input[name=search_type]:checked').val());
		if ( val_artist === "" && val_title === "" && val_track === "" ) {
			alert("いずれか必須です。");
			return;
		}

		var $submit = $("#id_submit");
		$submit.attr({disabled:true}).val("searching...");

		$.ajax( "<?= h($base_path) ?>Albums/SearchArtist", {
			method : 'POST',
			dataType : 'json',
			data : {
				artist : val_artist,
				title : val_title,
				track : val_track,
				search_type : val_search_type
			}
		})
		.done(function(json){
			var $result = $("#id_Albums_SearchArtist_result");
			$result.html("").css({display:"none"});
			cache_search_result = json;
			if ( json.length === 0 ) {
				$result.append($("<div class='info error_message tacenter' />").html("見つかりませんでした。")).slideToggle("middle");
				return;
			}
			$result.append($("<h4 />").text("album データを選択してください。"));
			for(var i=0,len=json.length; i<len; i++) {

				// discogs の場合、アーティスト名に (数字) が付くケースがあるので削除しておく
				if ( val_search_type === "discogs" ) {
					json[i].artist = $.trim( json[i].artist.replace(/\([0-9]+\)$/, "") );
				}

				var artist = json[i].artist,
				title = json[i].title,
				year = json[i].year === "" || json[i].year === 0 ? "unknown" : json[i].year,
				tracks = json[i].tracks,
				track_list = [];
				for(var k=0,n=tracks.length; k<n; k++) {
					track_list.push( k+1 + "." + tracks[k] );
				}
				$result.append($("<div class='album_search_result' data-cache_index='"+i+"' />").append(
					// artist / title (year)
					$("<p />").css({"font-weight":"bold"}).text( artist + " / " + title + " (" + year + ")" ),
					// track list
					$("<p />").text(track_list.join(" / ")),
					// 選択ボタン
					$("<p class='tacenter actions' />").append(
						$("<button data-cache_index='"+i+"'> 選 択 </button>").on("click.js",function(){
							var cache_index = $(this).attr("data-cache_index");
							var cache_data = cache_search_result[cache_index];
							// 最初にアルバム検索
							$.ajax( "<?= h($base_path) ?>Albums/SearchAlbumExists", {
								method : 'POST',
								dataType : 'json',
								data : {
									artist : cache_data["artist"],
									title  : cache_data["title"]
								}
							})
							.done(function(json){
								if (!json.status) {
									alert(json.messages.join("\n"));
									return false;
								}
								if (json.data.length > 0) {
									if ( confirm( cache_data["artist"] + " / " + cache_data["title"] + " は登録済みです。\nアルバムページを表示しますか？" ) ) {
										location.href = "<?= h($base_path) ?>Albums/View/id/" + json.data[0].id;
									}
									return false;
								}
								// 情報をformに反映、他の情報は削除
								$result.slideToggle("middle");
								$("#id_add_artist").val(cache_data["artist"]);
								$("#id_add_title").val(cache_data["title"]);
								$("#id_add_year").val(cache_data["year"] === "" || cache_data["year"] === 0 ? "" : cache_data["year"]);
								setTracks(cache_data["tracks"]);
								$("#id_Albums_SearchArtist_result").html("");
								$("#id_Albums_AddRun").show();
								// データを選択してから画像検索
								var search_q = cache_data["artist"] + ' ' + cache_data["title"];
								search_q = search_q.replace("'", "\'").replace('"','\"');
								$("#id_search_q").val(search_q);
								searchImage(search_q);
							})
							.fail(function(e){
								alert("system error.");
							})
							.always(function(){
							});
						})
					)
				));
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

	// image search
	$("#id_image_search_form").on("submit.js", function(){
		searchImage($("#id_search_q").val());
		return false;
	});

	function searchImage(q){
		var $isfs = $("#id_image_search_form_submit");
		$isfs.attr({disabled:true}).val($isfs.attr("data-label_off"));

		$.ajax( "<?= h($base_path) ?>Albums/SearchImage", {
			method : 'POST',
			dataType : 'json',
			data : {q : q}
		})
		.done(function(json){
			var $result = $("#id_Albums_SearchImage_result");
			$result.html("").css({display:"none"});
			if ( json.length === 0 ) {
				$result.append($("<p class='notfound' />").text("not found..."));
				return;
			}
			for(var i=0,len=json.length; i<len; i++) {
				var img = json[i];
				var $img = $("<img class='album_search_cover_result' />").attr({
					src:img
				}).on("click.js", function(){
					var selected_img_url = $(this).attr("src");
					$("#id_add_img_url").val(selected_img_url);
					$("#id_selected_img_url").fadeOut("fast", function(){
						$(this).html("").append(
							$("<img />").attr({src:selected_img_url}),
							$("<span> </span>"),
							$("<button>選択した画像を削除</button>").on("click.js", function(){
								$("#id_selected_img_url").html("");
								$("#id_add_img_url").val("");
								return false;
							})
						).fadeIn("fast");
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
			$isfs.attr({disabled:false}).val($isfs.attr("data-label_on"));
		});
	}

	function setTracks(tracks)
	{
		var tmp_tracks = [].concat(tracks);	// コピー作っておく
		var $id_add_tracks = $("#id_add_tracks").html("");
		var index = 0;
		for(var i=0,len=tracks.length; i<len; i++){
			index = i+1;
			$id_add_tracks.append(
				$("<tr />").attr({}).append(
					$("<td class='td_track_num' />").addClass("w50px").text("tr." + index),
					$("<td class='td_track_title' />").append($('<input class="text_track_title" type="text" />').attr({
						name :"add_track_"+index,
						id :"id_add_track_"+index,
						value : tracks[i]
					})),
					$("<td class='actions' />").append(
						$("<button />").attr({"data-track_num":index}).text("del").on("click.js", function(){
							// 現在表示されているtrack textで配列を再作成
							tmp_tracks = [].concat([]);
							$(".text_track_title").each(function(){
								tmp_tracks.push($(this).val());
							});
							if (tmp_tracks.length <= 1) {
								alert("これ以上削除できません。");
								return false;
							}
							if(confirm("このトラックを削除しますか?")){
								tmp_tracks.splice($(this).attr("data-track_num") - 1, 1);
								setTracks(tmp_tracks);
							}
							return false;
						}),
						$("<span>&nbsp;</span>"),
						$("<button />").attr({"data-track_num":index}).text("add").on("click.js", function(){
							// 現在表示されているtrack textで配列を再作成
							tmp_tracks = [].concat([]);
							$(".text_track_title").each(function(){
								tmp_tracks.push($(this).val());
							});
							if(confirm("トラックを追加しますか？")){
								tmp_tracks.splice($(this).attr("data-track_num"), 0, "");
								setTracks(tmp_tracks);
							}
							return false;
						})
					)
				)
			);
		}
	}

	$("#id_Albums_AddRun_Form").on("submit",function(){
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
				location.href = "<?= h($base_path) ?>Albums/View/id/" + json.data.album_id;
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