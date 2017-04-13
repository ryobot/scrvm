<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();
$title_head = "Listening Log";
$add_title = "Listening Log";
if ( isset($select_user) ) {
	$title_head .= ":: {$select_user["username"]}";
	$add_title .= "<br />{$select_user["username"]} <img class='width_50px w3-circle' src='{$base_path}files/attachment/photo/{$select_user["img_file"]}' />";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($title_head) ?> - <?= h($base_title) ?></title>
<!-- ogp -->
<meta property="og:sitename" content="<?= h($base_title) ?>" />
<meta property="og:title" content="<?= h($base_title) ?>" />
<meta property="og:locale" content="ja_JP" />
<meta property="og:description" content="<?= h($_description) ?>" />
<meta property="og:url" content="<?= h(\lib\Util\Server::getFullHostUrl() . $base_path) ?>" />
<meta property="og:image" content="<?= h(\lib\Util\Server::getFullHostUrl() . "{$base_path}img/headphone_icon_S.png") ?>" />
<!-- twitter cards -->
<meta name="twitter:card" value="summary" />
<meta name="twitter:site" value="@ryobotnotabot" />
<meta name="twitter:title" value="<?= h($base_title) ?>" />
<meta name="twitter:description" content="<?= h($_description) ?>" />
<meta name="twitter:url" content="<?= h(\lib\Util\Server::getFullHostUrl() . $base_path) ?>" />
<meta name="twitter:image:src" content="<?= h(\lib\Util\Server::getFullHostUrl() . "{$base_path}img/headphone_icon_S.png") ?>" />
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge"><?= $add_title ?> (<?= h($pager["total_count"]) ?>)</h2>
	</div>

<?php if(count($reviews) > 0):?>

	<!-- pager -->
	<div class="w3-center w3-padding-8">
		<ul class="w3-pagination">
<?php if($pager["prev"]): ?>
			<li><a class="w3-hover-black" href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a class="<?= $nav["active"] ? "w3-black" : "w3-hover-black" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a class="w3-hover-black" href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

	<div class="flex-container w3-padding-16 w3-center" style="justify-content:flex-start;">
<?php $lastdate = ""; $divcnt = 0; ?>
<?php foreach($reviews as $idx => $review): ?>
<?php $date = dateInWeek($review["created"]); ?>
<?php if ($date !== $lastdate):?>
		<div class="w3-padding-tile flex-item col_tile w3-white w3-card"><h5><?php echo $date; ?></h5></div>
		<?php $lastdate = $date; $divcnt++; ?>
<?php endif;?>

		<div
			id="id_log_<?= h($review["id"]) ?>"
			class="w3-padding-tile flex-item col_tile w3-white w3-card col_tile_cover"
			style="position:relative;"
			data-id="<?= h($review["id"]) ?>"
			data-album_id="<?= h($review["album_id"]) ?>"
			data-body="<?= h($ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path)) ?>"
			data-listening_system="<?= h($review["listening_system"]) ?>"
			data-artist="<?= h($review["artist"]) ?>"
			data-title="<?= h($review["title"]) ?>"
			data-year="<?= h($review["year"] === null ? "?" : $review["year"]) ?>"
			data-img_file="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>"
			data-user_id="<?= h($review["user_id"]) ?>"
			data-username="<?= h($review["username"]) ?>"
			data-user_img_file="<?= isset($review["user_img_file"]) ? "{$base_path}files/attachment/photo/{$review["user_img_file"]}" : "{$base_path}img/user.svg" ?>"
			data-created="<?= h(timeAgoInWords($review["created"])) ?>"
			data-prev_id="<?= isset($reviews[$idx-1]) ? $reviews[$idx-1]["id"] : "" ?>"
			data-next_id="<?= isset($reviews[$idx+1]) ? $reviews[$idx+1]["id"] : "" ?>"
		>
			<div style="position:absolute; top:5px; right:5px; z-index:1">
				<table><tr>
<?php if ($review["body"]):?>
					<td width="20">
						<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><img
							src="<?= h($base_path) ?>img/reviews.svg"
							class="width_20px w3-circle w3-white w3-border w3-border-white"
							alt="reviews"
						/></a>
						</div>
					</td>
<?php endif;?>
<?php if (!isset($select_user)):?>
					<td width="20">
						<a href="<?= h($base_path) ?>Logs/Index/user/<?= h($review["user_id"]) ?>"><img
							class="width_20px w3-circle w3-white w3-border w3-border-white"
							src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>"
							alt="<?= h($review["username"]) ?>"
						/></a>
					</td>
<?php endif;?>
					<!-- <td width="30"><a href="<?= h($base_path) ?>Reviews/Index/situation/<?= h($review["listening_system"]) ?>"><img class="width_12px" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" /></a></td> -->
				</tr></table>
			</div>
			<a href="<?= h($base_path) ?>Albums/View/id/<?= h($review["album_id"]) ?>" class="w3-padding-0 w3-margin-0"><img
				class="cover w3-card-2"
				style="z-index:0"
				src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>"
				alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>"
			/></a>
		</div>
<?php $divcnt++; ?>
<?php endforeach; ?>
	</div>


	<!-- modal template -->
	<div id="id_modal" class="w3-modal" style="z-index: 30;">
		<div class="w3-modal-content w3-animate-top w3-card-16 w3-white w3-display-container">
			<header class="w3-container">
				<span id="id_modal_close" class="w3-btn w3-white w3-display-topright" style="z-index: 31;">&times;</span>
			</header>
			<div class="w3-container w3-center w3-padding w3-margin-left w3-margin-right">
				<div class="w3-padding-right w3-padding-left">
					<div id="id_modal_data_img_file"></div>
					<div id="id_modal_data_artist_title_year"></div>
					<div id="id_modal_data_body"></div>
					<div id="id_modal_data_userinfo"></div>
<?php if ( ! isset($select_user)): ?>
					<hr />
					<div id="id_modal_show_user_log"></div>
<?php endif; ?>
				</div>
			</div>
			<div id="id_modal_button_area"></div>
			<footer class="w3-container"></footer>
		</div>
	</div>

	<!-- pager -->
	<div class="w3-center w3-padding-8">
		<ul class="w3-pagination">
<?php if($pager["prev"]): ?>
			<li><a class="w3-hover-black" href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a class="<?= $nav["active"] ? "w3-black" : "w3-hover-black" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a class="w3-hover-black" href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

<?php endif; ?>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

<script>
;$(function(){

	var $modal = $("#id_modal");

	/**
	 * set modal
	 * @param {jQuery Object} $elm
	 */
	var setModal = function($elm){
		var artist_title_year = $elm.attr("data-artist") + " / " + $elm.attr("data-title") + " (" + $elm.attr("data-year") + ")";
		$("#id_modal_data_img_file").html("").append(
			$("<p />").append(
				$('<img />').attr({
					"src"   : $elm.attr("data-img_file"),
					"class" : "cover w3-card-2",
					"alt"   : artist_title_year
				})
			)
		);

		$("#id_modal_data_artist_title_year").html("").append(
			$('<h5 />').append(
				$('<a />').attr({
					"href" : BASE_PATH + "Albums/View/id/" + $elm.attr("data-album_id")
				}).text(
					artist_title_year
				)
			)
		);

		$("#id_modal_data_body").html("").append(
			$('<p />').html($elm.attr("data-body"))
		);

		$("#id_modal_data_userinfo").html("").append(
			$("<p />").append(
				$('<a />').attr({
					"href" : BASE_PATH + "Users/View/id/" + $elm.attr("data-user_id")
				}).append(
					$('<img />').attr({
						"class" : "width_25px",
						"src" : $elm.attr("data-user_img_file")
					})
				),
				" ",
				$('<a />').attr({
					"href" : BASE_PATH + "Users/View/id/" + $elm.attr("data-user_id")
				}).text(
					$elm.attr("data-username")
				),
				" - ",
				$('<a />').attr({
					"href" : BASE_PATH + "Reviews/View/id/" + $elm.attr("data-id")
				}).text(
					$elm.attr("data-created")
				)
			)
		);

		$("#id_modal_show_user_log").html("").append(
			$("<p />").append(
				$("<a />").attr({
					"class" : "w3-btn w3-teal w3-round",
					"href" : BASE_PATH + "Logs/Index/user/" + $elm.attr("data-user_id")
				}).text(
					$elm.attr("data-username") + " の Logs"
				)
			)
		);

		Mousetrap.unbind("left");
		Mousetrap.unbind("right");
		Mousetrap.bind("esc", function(){
			$modal.hide();
		});
		var $modal_button_area = $("#id_modal_button_area").html("");
		if ( $elm.attr("data-prev_id") !== "" ) {
			$modal_button_area.append(
				$('<button class="w3-btn w3-light-grey w3-display-left" style="height:100%;">&#10094;</button>').on("click.js", function(){
					setModal($("#id_log_" + $elm.attr("data-prev_id")));
				})
			);
			Mousetrap.bind('left', function(){
				setModal($("#id_log_" + $elm.attr("data-prev_id")));
			});
		}
		if ( $elm.attr("data-next_id") !== "" ) {
			$modal_button_area.append(
				$('<button class="w3-btn w3-light-grey w3-display-right" style="height:100%;">&#10095;</button>').on("click.js", function(){
					setModal($("#id_log_" + $elm.attr("data-next_id")));
				})
			);
			Mousetrap.bind('right', function(){
				setModal($("#id_log_" + $elm.attr("data-next_id")));
			});
		}

	};

	$(".col_tile_cover").on("click.js", function(){
		setModal($(this));
		$modal.show();
		return false;
	});

	$("#id_modal_close").on("click.js", function(){
		$modal.hide();
	});

	$(window).on("click.js touchend.js", function(event){
		if (event.target === $modal[0]) {
			$modal.hide();
			event.preventDefault();	// イベント遷移を止めて、直下のイベントを無効にしておく
		}
	});

});
</script>

</body>
</html>