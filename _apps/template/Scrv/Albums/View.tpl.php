<?php
/**
 * Albums/View.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Albums :: View</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2><?= h("{$album["artist"]} / {$album["title"]}") ?> (<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)</h2>

	<p>
		<img src="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>" alt="<?= h("{$album["artist"]} / {$album["title"]}") ?>" />
		<img
			id="id_fav_album"
			class="fav_album<?= $is_login ? "" : "_nologin" ?>"
<?php if(isset($album["favalbums_count"]) && in_array($album["id"], $own_favalbums, true)):?>
			src="<?= h($base_path) ?>img/star_ylw.png"
<?php else:?>
			src="<?= h($base_path) ?>img/star_gry.png"
<?php endif;?>
			data-fav_on="<?= h($base_path) ?>img/star_ylw.png"
			data-fav_off="<?= h($base_path) ?>img/star_gry.png"
			data-album_id="<?= h($album_id) ?>"
			alt="fav album"
		/>
		<span id="id_fav_album_count"><?= isset($album["favalbums_count"]) ? "({$album["favalbums_count"]})" : "" ?></span>
	</p>

	<table class="w100per every_other_row_odd">
<?php foreach($tracks as $track): ?>
		<tr>
			<td class="w30px"><?= $track["track_num"] ?></td>
			<td><?= $track["track_title"] ?></td>
			<td class="w80px taleft">
				<img
					class="fav_track<?= $is_login ? "" : "_nologin" ?>"
<?php if(isset($track["favtracks_count"]) && in_array($track["id"], $own_favtracks, true)):?>
					src="<?= h($base_path) ?>img/chk_ylw.png"
<?php else:?>
					src="<?= h($base_path) ?>img/chk_gry.png"
<?php endif;?>
					data-fav_on="<?= h($base_path) ?>img/chk_ylw.png"
					data-fav_off="<?= h($base_path) ?>img/chk_gry.png"
					data-track_id="<?= h($track["id"]) ?>"
					alt="fav track"
				/>
				<span id="id_fav_track_count_<?= $track["id"] ?>"><?= isset($track["favtracks_count"]) ? "({$track["favtracks_count"]})" : "" ?></span>
			</td>
		</tr>
<?php endforeach;?>
	</table>

	<h3>Reviews (<?= count($reviews) ?>)</h3>
<?php if($is_login): ?>
	<p class="actions"><a href="<?= h($base_path) ?>Reviews/Add?id=<?= h($album_id) ?>">Write a Review</a></p>
<?php endif; ?>
	<table class="w100per every_other_row_odd">
<?php foreach($reviews as $review): ?>
		<tr>
			<td class="w50px tacenter">
				<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><img class="user_photo_min" src="<?= h($base_path) ?><?= isset($review["img_file"]) ? "files/attachment/photo/{$review["img_file"]}" : "img/user.png" ?>" alt="<?= h($review["username"]) ?>" /></a>
			</td>
			<td>
				<div><?= h($review["body"]) ?></div>
				<p>
					<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
					<span class="post_date"><?= h( timeAgoInWords($review["created"])) ?></span>
				</p>
			</td>
		</tr>
<?php endforeach; ?>
	</table>

	<p id="id_itunes_search_results"></p>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<!-- itunes search 用 -->
<input type="hidden" name="term" id="id_term" value="<?= h("{$album["artist"]} {$album["title"]}") ?>" />

<script>
;$(function(){

	var BASE_PATH = "<?= h($base_path) ?>";

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
		var createLink = function(url,artist,title){
			return $("<a />").attr({
				href:url,
				target:"blank"
			}).text("♪ " + artist + " / " + title);
		};

		var i=0,len=json.results.length;
		$search_results.append($("<h3 />").text("iTunes ("+len+")"));
		var $table = $("<table />").attr({class:"w100per every_other_row_odd"});
		for(; i<len; i++) {
			var result = json.results[i];
			$table.append(
				$("<tr />").append(
					$("<td />").append(
						createLink(result.collectionViewUrl,result.artistName,result.collectionName)
					)
				)
			);
		}
		$search_results.append($table).slideToggle("middle");
	})
	.fail(function(e){
	})
	.always(function(){
	});

<?php if($is_login): ?>

	var location_href = BASE_PATH + "Albums/View?id=<?= h($album_id) ?>";
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