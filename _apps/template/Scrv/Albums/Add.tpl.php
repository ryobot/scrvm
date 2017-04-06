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

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Add Album</h2>
	</div>

	<div class="w3-center w3-padding w3-margin-bottom w3-card-2 w3-white">
		<form id="id_Albums_SearchArtist" action="javascript:;" method="POST" autocomplete="off">
			<div class="w3-hide">
				<div class="displaytablecell w50per">
					<label><input type="radio" name="search_type" id="id_search_type_gracenote" value="gracenote" checked="checked"> <img src="<?= h($base_path) ?>img/logo_gracenote.png" alt="gracenote" title="gracenote" class="width_80px" /></label>
				</div>
				<div class="displaytablecell w50per">
					<label><input type="radio" name="search_type" id="id_search_type_discogs" value="discogs"> <img src="<?= h($base_path) ?>img/logo_discogs.png" alt="discogs" title="discogs" class="width_80px" /></label>
				</div>
			</div>
			<p><input class="w3-input" type="text" name="artist" id="id_artist" value="<?= $type==="artist" ? h($q) : "" ?>" placeholder="アーティスト名" /></p>
			<p><input class="w3-input" type="text" name="title" id="id_title" value="<?= $type==="title" ? h($q) : "" ?>" placeholder="アルバム名" /></p>
			<p><input class="w3-input" type="text" name="track" id="id_track" value="" placeholder="トラック名" /></p>
			<p class="notice w3-small">※インディーズの新譜はあまりヒットしません。</p>
			<p><input class="w3-btn" type="submit" id="id_submit" value=" 検索 " /></p>
		</form>
	</div>

	<div id="id_Albums_SearchArtist_result" class="flex-container w3-row-padding w3-padding-16 w3-margin-bottom w3-center"></div>

	<div id="id_Albums_AddRun" class="displaynone">
		<input type="hidden" name="add_img_url" id="id_add_img_url" value="" />

		<div class="w3-center w3-padding w3-margin-bottom w3-card-2 w3-white">
			<h5 class="w3-center w3-text-red">アルバム情報を確認・修正してください。</h5>
			<table class="width_100per">
				<tr>
					<td>artist</td>
					<td><input class="w3-input" type="text" name="add_artist" id="id_add_artist" value="" /></td>
				</tr>
				<tr>
					<td>title</td>
					<td><input class="w3-input" type="text" name="add_title" id="id_add_title" value="" /></td>
				</tr>
				<tr>
					<td>year</td>
					<td><input class="w3-input" ype="text" name="add_year" id="id_add_year" value="" /></td>
				</tr>
			</table>
			<p>track list</p>
			<table class="width_100per" id="id_add_tracks">
			</table>
		</div>

		<div class="w3-center w3-padding w3-margin-bottom w3-card-2 w3-white">
			<h5 class="w3-center w3-text-red">アルバムカバーを選択してください（あとで変更も可能です）。</h5>
			<form action="javascript:;" id="id_image_search_form">
				<p><input class="w3-input w3-border" type="text" name="search_q" id="id_search_q" value="" placeholder="search image" /></p>
				<p><input
					class="w3-btn"
					type="submit"
					value="カバー検索"
					id="id_image_search_form_submit"
					data-label_on="カバー検索"
					data-label_off="検索中…"
				/></p>
			</form>
			<p class="actions" id="id_selected_img_url"></p>
			<div id="id_Albums_SearchImage_result"></div>
		</div>

		<div class="w3-center">
			<form id="id_Albums_AddRun_Form" action="javascript:;" method="POST">
				<p><input class="w3-btn w3-orange w3-card-2" type="submit" value=" この内容で追加する " /></p>
			</form>
		</div>

	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

<script>
;$(function(){

	var cache_search_result = [];
	var $result = $("#id_Albums_SearchArtist_result");
	var $submit = $("#id_submit");

	/**
	 * jQuery.ajax を jQuery.Defferd でラップした関数
	 * @param {type} opt
	 * @returns {Object}
	 */
	var myAjax = function(opt){
		var $ajax = $.ajax(opt);
		var defer = new $.Deferred();
		$ajax.done(function(data, status, $ajax){
			defer.resolveWith(this, arguments);
		});
		$ajax.fail(function(data, status, $ajax){
			defer.resolveWith(this, arguments);
		});
		return $.extend({}, $ajax, defer.promise());
	};

	/**
	 * discogs search
	 * @param array url_list
	 */
	var discogsSearch = function(url_list){
		var ajax=[], json=[];
		// $.ajax をリクエスト数だけ作成
		for(var i=0,len=url_list.length; i<len; i++){
			var $ajax = myAjax({
				url:url_list[i],
				data:{}
			}).done(function(res, status){
				if ( status === "success" ) {
					json.push(res);
				}
			});
			ajax.push($ajax);
		}
		// すべて成功時の処理
		$.when.apply(null, ajax).done(function(){
			// discogsデータ作成
			var result = [];
			for(var i=0,len=json.length; i<len; i++){
				var data = json[i];
				if ( ! data.tracklist || data.tracklist.length === 0 ) {
					continue;
				}
				var tracks = [];
				for( var j=0,k=data.tracklist.length; j<k; j++ ){
					tracks.push(data.tracklist[j].title);
				}
				result.push({
					artist: $.trim( data.artists[0].name.replace(/\([0-9]+\)$/, "") ),
					title : data.title,
					year : data.year,
					tracks: tracks
				});
			}
			cache_search_result = result;
			showAlbumsData(result);
		});
		// どこかで失敗時の処理
		$.when.apply(null, ajax).fail(function(){
			alert("ajax request error.");
		});
	};

	/**
	 * show albums data
	 * @param {json} json
	 */
	var showAlbumsData = function(json){
		for(var i=0,len=json.length; i<len; i++) {
			var artist = json[i].artist,
			title = json[i].title,
			year = json[i].year === "" || json[i].year === 0 ? "unknown" : json[i].year,
			tracks = json[i].tracks,
			track_list = [];
			for(var k=0,n=tracks.length; k<n; k++) {
				track_list.push( k+1 + "." + tracks[k] );
			}
			$result.append($("<div class='album_search_result w3-center w3-padding w3-margin-bottom w3-card-2 w3-white col' data-cache_index='"+i+"' />").append(
				// artist / title (year)
				$("<p />").css({"font-weight":"bold"}).text( artist + " / " + title + " (" + year + ")" ),
				// track list
				$("<p />").text(track_list.join(" / ")),
				// 選択ボタン
				$("<p />").append(
					$("<button data-cache_index='"+i+"' class='w3-btn'> 選 択 </button>").on("click.js",function(){
						var cache_index = $(this).attr("data-cache_index");
						var cache_data = cache_search_result[cache_index];
						// 最初にアルバム検索
						$.ajax( BASE_PATH + "Albums/SearchAlbumExists", {
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
									location.href = BASE_PATH + "Albums/View/id/" + json.data[0].id;
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
		$submit.attr({disabled:false}).val(" 検索 ");
		$result.slideToggle("middle");
	};

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
		$submit.attr({disabled:true}).val(" 検索中... ");
		$.ajax( BASE_PATH + "Albums/SearchArtist", {
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
			$result.html("").css({display:"none"});
			cache_search_result = json;
			if ( json.length === 0 ) {
				alert("見つかりませんでした。");
				return;
			}
			// discogs Search の場合は resourse_url 配列が返されるので、個別に ajax search
			if ( val_search_type === "discogs" ) {
				discogsSearch(json);
				return;
			}
			showAlbumsData(json);
		})
		.fail(function(e){
			alert("system error.");
		})
		.always(function(){
			if ( $submit.attr("disabled") ) {
				$submit.attr({disabled:false}).val(" 検索 ");
			}
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

		$.ajax( BASE_PATH + "Albums/SearchImage", {
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
				var $img = $("<img class='album_search_cover_result width_80px w3-padding-tiny' />").attr({
					src:img
				}).on("click.js", function(){
					var selected_img_url = $(this).attr("src");
					$("#id_add_img_url").val(selected_img_url);
					$("#id_selected_img_url").fadeOut("fast", function(){
						$(this).html("").append(
							$("<img />").attr({src:selected_img_url}),
							$("<p></p>"),
							$("<button class='w3-btn'>選択した画像を削除する</button>").on("click.js", function(){
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

	function setTracks(tracks) {
		var tmp_tracks = [].concat(tracks);	// コピー作っておく
		var $id_add_tracks = $("#id_add_tracks").html("");
		var index = 0;
		for(var i=0,len=tracks.length; i<len; i++){
			index = i+1;
			$id_add_tracks.append(
				$("<tr />").attr({}).append(
					$("<td class='td_track_num' />").text("tr." + index),
					$("<td class='td_track_title' />").append($('<input class="text_track_title w3-input" type="text" />').attr({
						name :"add_track_"+index,
						id :"id_add_track_"+index,
						value : tracks[i]
					})),
					$("<td />").append(
						$("<button class='w3-btn w3-small'>削除</button>").attr({"data-track_num":index}).on("click.js", function(){
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
						})
					),
					$("<td />").append(
						$("<button class='w3-btn w3-small'>追加</button>").attr({"data-track_num":index}).on("click.js", function(){
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
		if(!confirm("アルバムを追加しますか？")) {
			return false;
		}
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
		$.ajax( BASE_PATH + "Albums/AddRun", {
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
				location.href = BASE_PATH + "Albums/View/id/" + json.data.album_id;
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