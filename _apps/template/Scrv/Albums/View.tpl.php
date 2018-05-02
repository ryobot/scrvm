<?php
/**
 * Albums/View.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();

$view_year = isset($album["year"]) && $album["year"] !== "" ? $album["year"] : "?";
$view_title = "{$album["title"]} / {$album["artist"]} ({$view_year})";
$album_image_path = !isset($album["img_file"]) || $album["img_file"] === "" ? "{$base_path}img/no_image.png" : "{$base_path}files/covers/{$album["img_file"]}";

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<link rel="canonical" href="<?= h($base_path) ?>Albums/View/id/<?= h($album["id"]) ?>" />
<title><?= h($view_title) ?> - Albums::View - <?= h($base_title) ?></title>
<?php require __DIR__ . '/_ogp.tpl.php'; ?>
<?php require __DIR__ . '/_twitter_cards.tpl.php'; ?>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<!-- album info -->
	<div class="w3-margin-top w3-margin-bottom w3-center w3-white w3-card-2">

		<!-- album info -->
		<div class="w3-container">
			<div class="w3-padding">
				<img class="cover w3-card-2" src="<?= !isset($album["img_file"]) || $album["img_file"] === "" ? h("{$base_path}img/no_image.png") : h("{$base_path}files/covers/{$album["img_file"]}") ?>" alt="<?= h($view_title) ?>" />
			</div>
			<!-- title, artist, year -->
			<div class="w3-padding w3-large">
				<span><?= h($album["title"]) ?></span>
				<br />
				<span class="w3-medium">
				<a href="<?= h($base_path) ?>Albums/Tag/tag/<?= rawurlencode($album["artist"]) ?>"><?= h($album["artist"]) ?></a>
				(<?= h($view_year) ?>)
				</span>
			</div>
			<!-- fav album -->
			<div class="w3-padding">
				<img
					id="id_fav_album"
					class="width_30px cursorpointer fav_album<?= $is_login ? "" : "_nologin" ?>"
<?php if(isset($album["favalbums_count"]) && in_array($album["id"], $own_favalbums, true)):?>
					src="<?= h($base_path) ?>img/favalbums_on.svg"
<?php else:?>
					src="<?= h($base_path) ?>img/favalbums_off.svg"
<?php endif;?>
					data-fav_on="<?= h($base_path) ?>img/favalbums_on.svg"
					data-fav_off="<?= h($base_path) ?>img/favalbums_off.svg"
					data-album_id="<?= h($album_id) ?>"
					title="fav album"
				/>
				<span class="notice" id="id_fav_album_count"><?= isset($album["favalbums_count"]) ? $album["favalbums_count"] : "" ?></span>
			</div>
		</div>

		<!-- tags -->
		<div class="w3-padding w3-margin w3-center w3-white w3-hide">
			<div class=" tags_group">
<?php foreach($tags as $tag):?>
				<span class="w3-tag w3-round-medium w3-indigo w3-margin-bottom"><a
					href="<?= h($base_path) ?>Albums/Tag/tag/<?= urlencode($tag["tag"]) ?>"
					data-id="<?= h($tag["id"]) ?>"
					data-tag="<?= h($tag["tag"]) ?>"
					data-album_id="<?= h($tag["album_id"]) ?>"
					data-is_delete="<?= $tag["create_user_id"] === $login_user_data["id"] ? 1 : 0 ?>"
				><?= h($tag["tag"]) ?></a></span>
<?php endforeach;?>
			</div>
<?php if($is_login): ?>
			<div class="tags_form">
				<form action="<?= h($base_path) ?>Tags/Add" method="POST" autocomplete="off">
					<input type="hidden" name="token" value="<?= h($token) ?>" />
					<input type="hidden" name="album_id" value="<?= h($album_id) ?>" />
					<input class="w3-input w3-border" type="text" name="tag" id="id_tag" value="" required="required" placeholder="タグを入力" />
					<p><input class="w3-btn w3-round" type="submit" value="タグ追加" /></p>
				</form>
			</div>
<?php endif;?>
		</div>

		<!-- tracks -->
		<div class="w3-padding">
			<table class="w3-table-all">
<?php foreach($tracks as $track): ?>
				<tr>
					<td class="displaytablecell"><?= h($track["track_num"]) ?>. <?= h($track["track_title"]) ?></td>
					<td class="displaytablecell width_50px w3-left-align">
						<img
							class="width_20px cursorpointer fav_track<?= $is_login ? "" : "_nologin" ?>"
<?php if(isset($track["favtracks_count"]) && in_array($track["id"], $own_favtracks, true)):?>
							src="<?= h($base_path) ?>img/favtracks_on.svg"
<?php else:?>
							src="<?= h($base_path) ?>img/favtracks_off.svg"
<?php endif;?>
							data-fav_on="<?= h($base_path) ?>img/favtracks_on.svg"
							data-fav_off="<?= h($base_path) ?>img/favtracks_off.svg"
							data-track_id="<?= h($track["id"]) ?>"
							title="fav track"
						/>
						<span class="notice" id="id_fav_track_count_<?= $track["id"] ?>"><?= isset($track["favtracks_count"]) ? "{$track["favtracks_count"]}" : "" ?></span>
					</td>
				</tr>
<?php endforeach;?>
			</table>
		</div>

<?php if($is_login): ?>
		<div class="w3-padding w3-display-container">
			<p><button class="w3-btn w3-round w3-deep-orange add_review"><i class="fas fa-edit"></i> レビューを書く</button></p>
			<div class="w3-display-right w3-margin-right"><a href="javascript:;" id="id_more_edit"><i class="fas fa-ellipsis-h" title="more"></i></a></div>
		</div>
		<div class="w3-container displaynone w3-light-gray" id="id_more_edit_area">
			<p><a class="w3-btn w3-round w3-" href="<?= h($base_path) ?>Albums/Add/type/artist/q/<?= rawurlencode($album["artist"]) ?>"><i class="fas fa-plus-circle"></i> アルバムを追加</a></p>
<?php if ( $login_user_data["role"] === "admin" || ($is_login && $album["create_user_id"] === $login_user_data["id"]) ): ?>
			<p><a class="w3-btn w3-round w3-teal" href="<?= h($base_path) ?>Albums/Edit/id/<?= h($album["id"]) ?>"><i class="fas fa-edit"></i> アルバムを編集</a></p>
<?php endif; ?>
		</div>
		<script>
		;$(function(){
			$("#id_more_edit").on("click.js", function(){
				$("#id_more_edit_area").slideToggle("fast");
				return false;
			});
		});
		</script>
<?php endif;?>
	</div>

	<!-- reviews -->
	<div class="w3-center w3-padding w3-large">Reviews (<?= count($reviews) ?>)</div>
	<div class="w3-margin-bottom">
<?php	 foreach($reviews as $idx => $review): ?>
		<div class="w3-white w3-card w3-border-bottom">

			<!-- username, date -->
			<div class="w3-padding w3-row">
				<div class="s12">
					<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="w3-image w3-round width_25px" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" /></a>
					&nbsp;
					<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
					-
					<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><?= h(timeAgoInWords($review["created"])) ?></a>
				</div>
			</div>

			<!-- comment, reaction area -->
			<div class="w3-padding">
				<!-- comment -->
<?php if( $review["published"] === 0 && ( !$is_login || ($is_login && $review["user_id"] !== $login_user_data["id"]) )): ?>
				<div class="w3-margin-bottom notice">
					(非表示設定)
				</div>
<?php else: ?>
				<div class="w3-margin-bottom">
					<?= $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path) ?>
				</div>

				<!-- reaction_area -->
				<div class="w3-center reaction_area">
<?php if($review["published"] === 0): ?>
					<span><img class="width_20px" src="<?= h($base_path) ?>img/locked.svg" title="非公開" /></span>
<?php endif; ?>
<?php if($review["listening_last"] === "today"): ?>
					<span>
						<a href="<?= h($base_path) ?>Reviews/Index/situation/<?= h($review["listening_system"]) ?>"><img class="situation w3-image width_20px" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" title="<?= h($review["listening_system"]) ?>" /></a>
					</span>
<?php endif; ?>
					<span>
						<img
							class="fav_review cursorpointer w3-image width_20px"
							src="<?= h($base_path) ?>img/fav_off.svg"
							data-img_on="<?= h($base_path) ?>img/fav_on.svg"
							data-img_off="<?= h($base_path) ?>img/fav_off.svg"
							data-review_id="<?= h($review["id"]) ?>"
							data-my_fav="<?= isset($review["my_fav_id"]) ? 1 : 0 ?>"
							data-fav_reviews_count="<?= h($review["fav_reviews_count"]) ?>"
							title="fav review"
						/>
						<span class="fav_reviews_count"></span>
					</span>
<?php if( $review["user_id"] === $login_user_data["id"] ):?>
					<span>
						<a href="javascript:;" class="reaction_more" data-review_id="<?= h($review["id"]) ?>"><i class="fas fa-ellipsis-h" title="more"></i></a>
					</spab>
<?php endif;?>
				</div>
<?php endif; ?>
			</div>

<?php if( $review["user_id"] === $login_user_data["id"] ):?>
			<div class="displaynone w3-container w3-padding w3-center w3-light-gray" id="id_reaction_more_<?= h($review["id"]) ?>">
				<p>
					<a href="<?= h($base_path) ?>Reviews/Edit/id/<?= h($review["id"]) ?>" class="w3-btn w3-teal w3-round"><i class="fas fa-edit"></i> 編集</a>
					<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete w3-btn w3-round"><i class="fas fa-trash-alt"></i> 削除</a>
				</p>
			</div>
<?php endif;?>
		</div>
<?php endforeach; ?>
	</div>

	<!-- music search 用 -->
	<div class="w3-margin-bottom w3-white w3-card">
		<div id="id_itunes_search_results" class="w3-margin"></div>
		<div id="id_spotify_search_results" class="w3-margin"></div>
		<div id="id_gpm_search_results" class="w3-margin"></div>
		<input
			type="hidden"
			name="term"
			id="id_term"
			value="<?= h("{$album["artist"]} {$album["title"]}") ?>"
			data-artist="<?= h($album["artist"]) ?>"
			data-title="<?= h($album["title"]) ?>"
		/>
		<script src="<?= h($base_path) ?>js/MusicSearch.js?v20180413"></script>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


<script>
;$(function(){

	// tag_data
	$(".w3-tag > a").each(function(){
		var $tag = $(this);
		if ($tag.attr("data-is_delete") === "0"){return;}
		var delete_id = $tag.attr("data-id");
		var $del = $("<span class='tag_delete' />").text(" ✖ ").on("click.js", function(){
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

	$(".reaction_more").on("click.js", function(){
		var review_id = $(this).attr("data-review_id");
		$("#id_reaction_more_" + review_id).slideToggle("fast");
		return false;
	});

	// review delete
	$(".review_delete").each(function(){
		var $del = $(this);
		var delete_id = $del.attr("data-delete_id");
		$del.on("click.js", function(){
			if(confirm("このレビューを削除しますか ?")){
				$.ajax( BASE_PATH + "Reviews/Del", {
					method : 'POST',
					dataType : 'json',
					data : { id : delete_id }
				})
				.done(function(json){
					location.href = BASE_PATH + "Albums/View/id/<?= h($album_id) ?>";
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

<?php if($is_login): ?>

	// add_review
	$(".add_review").on("click.js", function(){
		location.href= BASE_PATH + "Reviews/Add/id/<?= h($album_id) ?>";
		return false;
	});

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
				$("#id_fav_album_count").text(fav_count === 0 ? "" : fav_count);
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
					$("#id_fav_track_count_" + track_id).text(fav_count === 0 ? "" : fav_count);
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