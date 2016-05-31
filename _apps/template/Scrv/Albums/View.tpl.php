<?php
/**
 * Albums/View.tpl.php
 * @author mgng
 */

$view_title = "{$album["artist"]} / {$album["title"]}";
$view_year = isset($album["year"]) && $album["year"] !== "" ? $album["year"] : "unknown";

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($view_title) ?> | <?= h($base_title) ?> :: Albums :: View</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<!-- album info -->
	<div class="album_info">
		<h3><?= h($view_title) ?> (<?= h($view_year) ?>)</h3>
<?php if ( $is_login && $album["create_user_id"] === $login_user_data["id"] ): ?>
		<p class="actions mgb10px"><a href="<?= h($base_path) ?>Albums/Edit?id=<?= h($album["id"]) ?>">Edit Album Data</a></p>
<?php endif; ?>
		<div class="displaytable w100per">
			<div class="displaytablecell w120px">
				<img src="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>" alt="<?= h($view_title) ?>" class="album_view_cover" />
			</div>
			<div class="displaytablecell vtalgmiddle pdl10px">
				<img
					id="id_fav_album"
					class="img32x32 fav_album<?= $is_login ? "" : "_nologin" ?>"
				<?php if(isset($album["favalbums_count"]) && in_array($album["id"], $own_favalbums, true)):?>
					src="<?= h($base_path) ?>img/favalbums_on.svg"
				<?php else:?>
					src="<?= h($base_path) ?>img/favalbums_off.svg"
				<?php endif;?>
					data-fav_on="<?= h($base_path) ?>img/favalbums_on.svg"
					data-fav_off="<?= h($base_path) ?>img/favalbums_off.svg"
					data-album_id="<?= h($album_id) ?>"
					alt="fav album"
					title="fav album"
				/>
				<span id="id_fav_album_count"><?= isset($album["favalbums_count"]) ? "({$album["favalbums_count"]})" : "" ?></span>
				<a href="#" id="id_to_applemusic" class="displaynone" target="blank"><img src="<?= h($base_path) ?>img/applemusic.svg" class="img32x32" alt="apple music" title="apple music" /></a>
				<a href="#" id="id_to_googlemusic" class="displaynone" target="blank"><img src="<?= h($base_path) ?>img/google.svg" class="img32x32" alt="google play music" title="google play music" /></a>
			</div>
		</div>

<?php if(count($tags) > 0): ?>
			<!-- tags -->
			<div class="mgt10px mgb10px">
				<p class="tags_group">
<?php foreach($tags as $tag):?>
					<span class="tags"><a
						href="<?= h($base_path) ?>Albums/Tag?tag=<?= urlencode($tag["tag"]) ?>"
						data-id="<?= h($tag["id"]) ?>"
						data-tag="<?= h($tag["tag"]) ?>"
						data-album_id="<?= h($tag["album_id"]) ?>"
						data-is_delete="<?= $tag["create_user_id"] === $login_user_data["id"] ? 1 : 0 ?>"
					><?= h($tag["tag"]) ?></a></span>
<?php endforeach;?>
				</p>
			</div>
<?php endif; ?>

<?php if($is_login): ?>
		<!-- add tag form -->
		<form action="<?= h($base_path) ?>Tags/Add" method="POST" autocomplete="off">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<input type="hidden" name="album_id" value="<?= h($album_id) ?>" />
			<dl class="search">
				<dt><input type="text" name="tag" id="id_tag" value="" required="required" placeholder="add tag" /></dt>
				<dd><input type="submit" value="add tag" /></dd>
			</dl>
		</form>
<?php endif;?>
	</div>

	<!-- track_info -->
	<div class="track_info w100per">
<?php foreach($tracks as $track): ?>
		<div class="displaytable w100per track">
			<div class="displaytablecell w30px">
				<?= $track["track_num"] ?>.
			</div>
			<div class="displaytablecell">
				<?= $track["track_title"] ?>
			</div>
			<div class="displaytablecell w80px taleft">
				<img
					class="img32x32 fav_track<?= $is_login ? "" : "_nologin" ?>"
<?php if(isset($track["favtracks_count"]) && in_array($track["id"], $own_favtracks, true)):?>
					src="<?= h($base_path) ?>img/favtracks_on.svg"
<?php else:?>
					src="<?= h($base_path) ?>img/favtracks_off.svg"
<?php endif;?>
					data-fav_on="<?= h($base_path) ?>img/favtracks_on.svg"
					data-fav_off="<?= h($base_path) ?>img/favtracks_off.svg"
					data-track_id="<?= h($track["id"]) ?>"
					alt="fav track"
					title="fav track"
				/>
				<span id="id_fav_track_count_<?= $track["id"] ?>"><?= isset($track["favtracks_count"]) ? "({$track["favtracks_count"]})" : "" ?></span>
			</div>
		</div>
<?php endforeach;?>
	</div>

	<!-- reviews -->
	<h3>Reviews (<?= count($reviews) ?>)</h3>
<?php if($is_login): ?>
	<p class="actions tacenter mgt10px mgb10px"><a href="<?= h($base_path) ?>Reviews/Add?id=<?= h($album_id) ?>">Write a Review</a></p>
<?php endif; ?>
	<div class="w100per">
<?php foreach($reviews as $review): ?>
		<div class="review">
			<div class="review_comment"><?= $review["body"] === "" || $review["body"] === "listening log" ? "(no review)" : nl2br(linkIt(h($review["body"]))) ?></div>
			<p>
				<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>">
					<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($review["username"]) ?>" />
				</a>
				<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
				-
				<a href="<?= h($base_path) ?>Reviews/View?id=<?= h($review["id"]) ?>">
					<span class="post_date"><?= h( timeAgoInWords($review["created"])) ?></span>
				</a>
<?php if($review["listening_last"] === "today"): ?>
				<img class="vtalgmiddle img16x16" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>.svg" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
				<span class="fav_reviews_wrapper">
					<img
						class="fav_review vtalgmiddle img16x16"
						src="<?= h($base_path) ?>img/fav_off.svg"
						data-img_on="<?= h($base_path) ?>img/fav_on.svg"
						data-img_off="<?= h($base_path) ?>img/fav_off.svg"
						data-review_id="<?= h($review["id"]) ?>"
						data-my_fav="<?= isset($review["my_fav_id"]) ? 1 : 0 ?>"
						data-fav_reviews_count="<?= h($review["fav_reviews_count"]) ?>"
						alt="fav review"
						title="fav review"
					/>
					<span class="fav_reviews_count"></span>
				</span>
			</p>
<?php if( $review["user_id"] === $login_user_data["id"] ):?>
			<p class="actions mgt10px">
				<a href="<?= h($base_path) ?>Reviews/Edit?id=<?= h($review["id"]) ?>">edit</a>
				<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete">delete</a>
			</p>
<?php endif;?>
		</div>
<?php endforeach; ?>
	</div>

	<div id="id_itunes_search_results"></div>
	<div id="id_gpm_search_results"></div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<!-- itunes search 用 -->
<input
	type="hidden"
	name="term"
	id="id_term"
	value="<?= h("{$album["artist"]} {$album["title"]}") ?>"
	data-artist="<?= h($album["artist"]) ?>"
	data-title="<?= h($album["title"]) ?>"
/>

<script>
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

	// tag_data
	$(".tags > a").each(function(){
		var $tag = $(this);
		if ($tag.attr("data-is_delete") === "0"){return;}
		var delete_id = $tag.attr("data-id");
		var $del = $("<span class='tag_delete' />").text("×").on("click.js", function(){
			if (!confirm("「"+$tag.attr("data-tag")+"」タグを削除しますか？")){
				return false;
			}
			$.ajax( "<?= h($base_path) ?>Tags/Del", {
				method : 'POST',
				dataType : 'json',
				data : { id : delete_id }
			})
			.done(function(json){
				if (!json.status) {
					alert(json.messages.join("\n"));
					return false;
				}
				$tag.parent().hide();
			})
			.fail(function(e){
				alert("system error.");
			})
			.always(function(){
			});
			return false;
		});
		$tag.parent().prepend($del);
	});

	// review delete
	$(".review_delete").each(function(){
		var $del = $(this);
		var delete_id = $del.attr("data-delete_id");
		$del.on("click.js", function(){
			if(confirm("are you sure ?")){
				$.ajax( "<?= h($base_path) ?>Reviews/Del", {
					method : 'POST',
					dataType : 'json',
					data : { id : delete_id }
				})
				.done(function(json){
					location.href="<?= h($base_path) ?>Albums/View?id=<?= h($album_id) ?>";
				})
				.fail(function(e){
					alert("system error.");
				})
				.always(function(){
				});
			}
			return false;
		});
	});

	// fav review
	$(".fav_review").each(function(){
		var $this = $(this);
		var fav_reviews_count = parseInt($this.attr("data-fav_reviews_count"), 10);
		var my_fav = parseInt($this.attr("data-my_fav"), 10);
		if ( fav_reviews_count > 0 ) {
			$this.next().text(fav_reviews_count);
		}
		if (my_fav === 1) {
			$this.attr("src",$this.attr("data-img_on"));
		}

<?php if($is_login): ?>
		$this.on("click.js", function(){
			var review_id = $(this).attr("data-review_id");
			$.ajax( "<?= h($base_path) ?>Reviews/Fav", {
				method : 'POST',
				dataType : 'json',
				data : { review_id : review_id }
			})
			.done(function(json){
				if (!json.status) {
					alert("system error.");
				} else {
					$this.attr("src",$this.attr("data-img_" + json.data.operation));
					$this.next().text(json.data.fav_count > 0 ? json.data.fav_count : "");
				}
			})
			.fail(function(e){
				alert("system error.");
			})
			.always(function(){
			});
		});
<?php endif; ?>

	});

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
		var $table = $("<div />").attr({class:"w100per itunes_info"});
		// 詰め直す
		var results = [];
		for(; i<len; i++) {
			results.push({
				url: json.results[i].collectionViewUrl,
				artist: json.results[i].artistName,
				title: json.results[i].collectionName
			});
		}
		// ソート
		results = sortSearchLists(ARTIST, TITLE, results);
		for(i=0; i<len; i++) {
			var result = results[i];
			$table.append(
				$("<div />").attr({class:"data"}).append(
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
		var $table = $("<div />").attr({class:"w100per gpm_info"});
		var results = sortSearchLists(ARTIST, TITLE, json);
		for(; i<len; i++) {
			var result = results[i];
			var listen_url = createGPMListenUrl(result.url);
			$table.append(
				$("<div />").attr({class:"data"}).append(
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
		return match ? "https://play.google.com/music/listen?view=" + match[1] + "_cid&authuser=0" : url;
	}

	function createLink(url,artist,title) {
		return $("<a />").attr({
			href:url,
			target:"blank"
		}).text("♪ " + artist + " / " + title);
	}

	// うーん…
	function sortSearchLists(artist, title, lists) {
		return lists;
		var escapeRegExp = function(string) {
			return string.replace(/([.*+?^=!:${}()|[\]\/\\])/g, "\\$1");
		};
		var _lists = [].concat(lists);
		var results = [];
		var reg1 = new RegExp( "^" + escapeRegExp(artist) + "$", "i" ),
		reg2 = new RegExp( "^" + escapeRegExp(title) + "$", "i" ),
		reg3 = new RegExp( "^" + escapeRegExp(artist), "i" );
		// 完全一致
		for(var i=0,len=_lists.length; i<len; i++) {
			if ( reg1.test(_lists[i].artist) && reg2.test(_lists[i].title) ) {
				results.push(_lists[i]);
				_lists.splice(i,1);
				len--;
			}
		}
		// artist 前方一致
		for(var i=0,len=_lists.length; i<len; i++) {
			if ( reg3.test(_lists[i].artist) ) {
				results.push(_lists[i]);
				_lists.splice(i,1);
				len--;
			}
		}
		// 残り
		for(var i=0,len=_lists.length; i<len; i++) {
			results.push(_lists[i]);
		}
		return results;
	}


<?php if($is_login): ?>

	// fav.album
	$("#id_fav_album").on("click.js", function(){
		var $this = $(this);
		var album_id = $this.attr("data-album_id");
		$.ajax( BASE_PATH + 'Albums/Fav', {
			method : 'POST',
			dataType : 'json',
			data : {album_id : album_id	}
		})
		.done(function(json){
			if (!json.status) {
				alert("system error.");
			} else {
				var operation = json.data.operation;
				var fav_count = json.data.fav_count;
				var img_src = $this.attr( operation === "delete" ? "data-fav_off" : "data-fav_on" );
				$this.attr({src : img_src});
				$("#id_fav_album_count").text(fav_count === 0 ? "" : "("+fav_count+")");
			}
		})
		.fail(function(e){
			alert("system error..");
		})
		.always(function(){
		});
	});

	// fav.tracks
	$(".fav_track").each(function(){
		var $this = $(this);
		$this.on("click.js", function(){
			var track_id = $this.attr("data-track_id");
			$.ajax( BASE_PATH + 'Tracks/Fav', {
				method : 'POST',
				dataType : 'json',
				data : {track_id : track_id	}
			})
			.done(function(json){
				if (!json.status) {
					alert("system error.");
				} else {
					var operation = json.data.operation;
					var fav_count = json.data.fav_count;
					var img_src = $this.attr( operation === "delete" ? "data-fav_off" : "data-fav_on" );
					$this.attr({src : img_src});
					$("#id_fav_track_count_" + track_id).text(fav_count === 0 ? "" : "("+fav_count+")");
				}
			})
			.fail(function(e){
				alert("system error...");
			})
			.always(function(){
			});
		});
	});
<?php endif; ?>

});
</script>


</body>
</html>