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
		<img src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h("{$album["artist"]} / {$album["title"]}") ?>" />
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
		<?= isset($album["favalbums_count"]) ? "({$album["favalbums_count"]})" : "" ?>
	</p>

<?php if($is_login): ?>
	<p class="actions"><a href="<?= h($base_path) ?>Reviews/Add?id=<?= h($album_id) ?>">Write a Review</a></p>
<?php endif; ?>

	<table>
<?php foreach($tracks as $track): ?>
		<tr>
			<td><?= $track["track_num"] ?></td>
			<td><?= $track["track_title"] ?></td>
			<td>
				<img
					class="fav_track<?= $is_login ? "" : "_nologin" ?>"
<?php if(isset($track["favtracks_count"]) && in_array($track["id"], $own_favtracks, true)):?>
					src="<?= h($base_path) ?>img/chk_ylw.png"
<?php else:?>
					src="<?= h($base_path) ?>img/chk_gry.png"
<?php endif;?>
					data-fav_on="<?= h($base_path) ?>img/chk_gry.png"
					data-fav_off="<?= h($base_path) ?>img/chk_ylw.png"
					data-track_id="<?= h($track["id"]) ?>"
					alt="fav track"
				/>
			</td>
			<td><?= isset($track["favtracks_count"]) ? "({$track["favtracks_count"]})" : "" ?></td>
		</tr>
<?php endforeach;?>
	</table>

	<p id="id_itunes_search_results"></p>

	<h3>Reviews ( <?= count($reviews) ?> )</h3>
	<table>
<?php foreach($reviews as $review): ?>
		<tr>
			<td>
				<div>reviewed by <a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a></div>
				<div><?= h($review["created"]) ?></div>
			</td>
			<td>
				<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><img class="user_photo_min" src="<?= h($base_path) ?><?= isset($review["img_file"]) ? "files/attachment/photo/{$review["img_file"]}" : "img/user.png" ?>" alt="<?= h($review["username"]) ?>" /></a>
			</td>
			<td></td>
		</tr>
		<tr>
			<td colspan="3">
				<?= h($review["body"]) ?>
				<hr />
			</td>
		</tr>
<?php endforeach; ?>
	</table>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<!-- itunes search 用 -->
<input type="hidden" name="term" id="id_term" value="<?= h("{$album["artist"]} {$album["title"]}") ?>" />

<script>
;$(function(){

	var BASE_PATH = "<?= h($base_path) ?>";

	// itunes search
	$.ajax( BASE_PATH + 'Itunes/Search', {
		method : 'GET',
		dataType : 'json',
		data : {term : $("#id_term").val()}
	})
	.done(function(json){
		if ( json.resultCount === 0 ) {
			return;
		}
		var createLink = function(url,artist,title){
			return $("<a />").attr({
				href:url,
				target:"blank"
			}).text("♪ iTunes - " + artist + " / " + title);
		};
		var $search_results = $("#id_itunes_search_results").html("");
		for(var i=0,len=json.results.length; i<len; i++) {
			var result = json.results[i];
			var $itunes_link = $("<p />").append(
				createLink(result.collectionViewUrl,result.artistName,result.collectionName)
			);
			$search_results.append($itunes_link);
		}
	})
	.fail(function(e){
	})
	.always(function(){
	});

<?php if($is_login): ?>

	var location_href = BASE_PATH + "Albums/View?id=<?= h($album_id) ?>";

	// fav.album
	$("#id_fav_album").on("click.js", function(){
		var album_id = $(this).attr("data-album_id");
		$.ajax( BASE_PATH + 'Albums/Fav', {
			method : 'POST',
			dataType : 'json',
			data : {album_id : album_id	}
		})
		.done(function(json){
			if (!json.status) {
				alert("system error.");
			} else {
				location.href=location_href;
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
		var $fav_track = $(this);
		$fav_track.on("click.js", function(){
			var track_id = $fav_track.attr("data-track_id");
			$.ajax( BASE_PATH + 'Tracks/Fav', {
				method : 'POST',
				dataType : 'json',
				data : {track_id : track_id	}
			})
			.done(function(json){
				if (!json.status) {
					alert("system error.");
				} else {
					location.href=location_href;
				}
			})
			.fail(function(e){
				alert("system error..");
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