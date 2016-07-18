// MusicSearch.js

;$(function(){

	var ARTIST = $("#id_term").attr("data-artist");
	var TITLE = $("#id_term").attr("data-title");

	// title に []があると検索がうまくいかないから削除
	var _tmp_title = TITLE.replace(/\[.+?\]/, "");
	if ( _tmp_title !== "" ) {
		$("#id_term").val( ARTIST + " " + _tmp_title );
	} else {
		$("#id_term").val( ARTIST + " " + TITLE.replace(/[\[\]]/g, "") );
	}

	// itunes search
	var $search_results = $("#id_itunes_search_results").html("");
	$.ajax( BASE_PATH + 'Itunes/Search', {
		method : 'GET',
		dataType : 'json',
		data : {
			term : $("#id_term").val(),
			country_list : ["jp"]
		}
	})
	.done(function(json){
		if ( json.resultCount === 0 ) {
			return;
		}
		var i=0,len=json.results.length;
		if ( len > 0 ) {
			$search_results.append($("<h3 />").text("iTunes ("+len+")"));
		}
		var $table = $("<div />");
		// 詰め直す
		var results = [];
		for(; i<len; i++) {
			results.push({
				url: json.results[i].collectionViewUrl,
				artist: json.results[i].artistName,
				title: json.results[i].collectionName
			});
		}
		for(i=0; i<len; i++) {
			var result = results[i];
			$table.append(
				$("<div />").attr({class:"itunes_info"}).append(
					createLink(result.url,result.artist,result.title)
				)
			);
		}
		$("#id_to_applemusic").attr({href:results[0].url}).fadeIn();
		$search_results.append($table).slideToggle("middle");
	})
	.fail(function(e){
	})
	.always(function(){
	});

	// google play music search
	var $search_results_gpm = $("#id_gpm_search_results").html("");
	$.ajax( BASE_PATH + 'GooglePlayMusic/Search', {
		method : 'GET',
		dataType : 'json',
		data : {
			q : $("#id_term").val()
		}
	})
	.done(function(json){
		if ( json.length === 0 ) {
			return;
		}
		var i=0,len=json.length;
		if ( len > 0 ) {
			$search_results_gpm.append($("<h3 />").text("Google Play Music ("+len+")"));
		}
		var $table = $("<div />");
		var results = json;
		for(; i<len; i++) {
			var result = results[i];
			var listen_url = createGPMListenUrl(result.url);
			$table.append(
				$("<div />").attr({class:"gpm_info"}).append(
					createLink(listen_url,result.artist,result.title)
				)
			);
		}
		$("#id_to_googlemusic").attr({href:createGPMListenUrl(results[0].url)}).fadeIn();
		$search_results_gpm.append($table).slideToggle("middle");
	})
	.fail(function(e){
	})
	.always(function(){
	});

	function createGPMListenUrl(url) {
		var match = url.match(/id=(.+)/);
		return match ? "https://play.google.com/music/m/" + match[1] : url;
	}

	function createLink(url,artist,title) {
		return $("<a />").attr({
			href:url,
			target:"blank"
		}).text("♪ " + artist + " / " + title);
	}

});
