// Common.js

/**
 * html_build_query
 * @param {Object} params
 * @returns {String}
 */
var hbq = function(params){
	var arr = [];
	for(var k in params) {
		arr.push(k + "=" + encodeURIComponent(params[k]));
	}
	return arr.join("&");
};

;$(function(){

	// 未ログイン時の処理 XXX 重要な変更はサーバサイドで行うこと
	if (!this.IS_LOGINED) {
		$(".reaction_area").css({
			filter:"grayscale(100%)",
			"-webkit-filter":"grayscale(100%)"
		});
	}

	// menu
	$("#dropmenu_normal").on("click",function(){
		$(".dropmenu").toggle();
	});
	$("#id_logout").on("click.js",function(){
		$("#id_form_logout").submit();
	});

	// リンクを展開
	$(".review_comment").each(function(){
		var $this = $(this);
		var text = $this.text();
		var regs = {
			'youtube'  : /https?:\/\/.*?\.youtube\.com\/watch\?v=([a-zA-Z0-9\_\-]+)/ig,
			'youtube2' : /https?:\/\/youtu\.be\/([a-zA-Z0-9\_\-]+)/ig,
			'vimeo' : /https?:\/\/vimeo\.com\/([a-zA-Z0-9]+)/ig,
			'twitter'  : /(https:\/\/twitter\.com\/.+?\/status\/[0-9]+)/ig
		};
		var ret_youtube = text.match(regs.youtube)  || [];
		var ret_youtube2 = text.match(regs.youtube2) || [];
		var ret_vimeo = text.match(regs.vimeo) || [];
		var ret_twitter  = text.match(regs.twitter) || [];

		// vimeo
		var arr_vimeo = [];
		for(var i=0,len=ret_vimeo.length; i<len; i++){
			var _tmp = ret_vimeo[i].replace(regs.vimeo, '$1');
			if ( $.inArray( _tmp, arr_vimeo ) === -1 ) {arr_vimeo.push(_tmp);}
		}
		if ( arr_vimeo.length > 0 ) {
			for(var i=0,len=arr_vimeo.length; i<len; i++){
				var $div = $("<div class='playmovie' />").append(
					$("<img />").attr({
						src : BASE_PATH + "img/vimeo.svg",
						class : "img24x24"
					}),
					$("<span />").text(" play movie")
				).on("click.js", function(){
					var vimeo_id = arr_vimeo[i];
					$(this).after(
						$("<div />").append(
							$("<iframe webkitallowfullscreen mozallowfullscreen allowfullscreen />").attr({
								frameborder:0,
								width:640,
								height:480,
								src:"https://player.vimeo.com/video/" + vimeo_id + "?byline=0&portrait=0"
							})
						)
					).remove();
				});
				$this.append($div);
			}
		}

		// twitter
		var arr_twitter = [];
		for(var i=0,len=ret_twitter.length; i<len; i++){
			var _tmp = ret_twitter[i].replace(regs.twitter, '$1');
			if ( $.inArray( _tmp, arr_twitter ) === -1 ) {arr_twitter.push(_tmp);}
		}
		if ( arr_twitter.length > 0 ) {
			for(var i=0,len=arr_twitter.length; i<len; i++){
				var $div = $('<div style="margin:10px;" />').append(
					$('<blockquote class="twitter-tweet" data-lang="ja" />').append(
						$('<p lang="ja" dir="ltr" />').append(
							$("<a />").attr({href:arr_twitter[i]})
						)
					)
				);
				$this.append($div);
			}
			$this.append($('<script async src="https://platform.twitter.com/widgets.js" charset="utf-8" />'));
		}

		// youtube
		var arr = [];
		for(var i=0,len=ret_youtube.length; i<len; i++){
			var _tmp = ret_youtube[i].replace(regs.youtube, 'https://i.ytimg.com/vi/$1/default.jpg#$1');
			if ( $.inArray( _tmp, arr ) === -1 ) {arr.push(_tmp);}
		}
		for(var i=0,len=ret_youtube2.length; i<len; i++){
			var _tmp = ret_youtube2[i].replace(regs.youtube2, 'https://i.ytimg.com/vi/$1/default.jpg#$1');
			if ( $.inArray( _tmp, arr ) === -1 ) {arr.push(_tmp);}
		}
		if ( arr.length > 0 ) {
			for(var i=0,len=arr.length; i<len; i++){
				var _tmp = arr[i].split("#");
				var img_url = _tmp[0];
				var youtube_id = _tmp[1];
				var $div = $("<div class='playmovie' />").append(
					$("<img />").attr({
						src : BASE_PATH + "img/youtube.svg",
						class : "img24x24"
					}),
					$("<span />").text(" play movie")
				).on("click.js", function(){
					$(this).after(
						$("<div />").append(
							$("<iframe allowfullscreen />").attr({
								class : "youtube_iframe",
								frameborder : "0",
								src : "https://www.youtube-nocookie.com/embed/" + youtube_id + "?rel=0"
							})
						)
					).remove();
				});
				$this.append($div);
			}
		}

	});

});
