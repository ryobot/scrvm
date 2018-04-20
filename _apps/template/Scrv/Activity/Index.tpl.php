<?php
/**
 * Activity/Index.tpl.php
 * @author mgng
 */

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Activity - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<div class="w3-main w3-content w3-padding-4 main">

	<p class="w3-center w3-xlarge">Activities within 24 hours</p>

<?php if(count($lists) > 0):?>

	<div class="w3-padding w3-center w3-card w3-white">
		<div class="user_menu_list flex-container cursorpointer">
			<div class="user_menu_list_block" data-menu="FavReviews" id="id_fav_review">
				<img src="<?= h($base_path) ?>img/fav_on.svg" class="width_16px" alt="reviews" title="reviews" />
				<div>Review(<span id="id_counter_fav_review"></span>)</div>
			</div>
			<div class="user_menu_list_block" data-menu="FavTracks" id="id_fav_tracks">
				<img src="<?= h($base_path) ?>img/favtracks_on.svg" class="width_16px" alt="fav tracks" title="fav tracks" />
				<div>Track(<span id="id_counter_fav_tracks"></span>)</div>
			</div>
			<div class="user_menu_list_block" data-menu="FavAlbums" id="id_fav_albums">
				<img src="<?= h($base_path) ?>img/favalbums_on.svg" class="width_16px" alt="fav albums" title="fav albums" />
				<div>Album(<span id="id_counter_fav_albums"></span>)</div>
			</div>
			<div class="user_menu_list_block" data-menu="Users" id="id_new_user">
				<img src="<?= h($base_path) ?>img/user.svg" class="width_16px" alt="new users" title="new users" />
				<div>User(<span id="id_counter_new_user"></span>)</div>
			</div>
		</div>
	</div>

	<!-- lists -->
	<div class="w3-padding_ w3-center user_list_activity">
<?php foreach($lists as $list): ?>
		<div class="user_info action_<?= h($list["action"]) ?> w3-padding w3-panel w3-white w3-card">
<?php		if( $list["action"] === "fav_reviews" ): ?>
				<p>
					<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="w3-round width_25px" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
					<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><?= h($list["faved_username"]) ?></a>
				</p>
				<p>
					<img src="<?= h($base_path) ?>img/fav_on.svg" class="w3-round width_25px" alt="fav review" />
					<a href="<?= h($base_path) ?>r/<?= h($list["review_id"]) ?>">
						<?= h($list["artist"]) ?> / <?= h($list["title"]) ?>
					</a>
					<div>(by <a href="<?= h($base_path) ?>u/<?= h($list["user_id"]) ?>"><?= h($list["username"]) ?></a>)</div>
				</p>
<?php		elseif($list["action"] === "fav_tracks"):?>
				<p>
					<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="w3-round width_25px" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
					<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><?= h($list["faved_username"]) ?></a>
				</p>
				<p>
					<img src="<?= h($base_path) ?>img/favtracks_on.svg" class="w3-round width_25px" alt="fav track" />
					<?= h($list["track_num"]) ?>. <?= h($list["track_title"]) ?>
				</p>
				<p>
					<a href="<?= h($base_path) ?>a/<?= h($list["album_id"]) ?>">(<?= h($list["artist"]) ?> / <?= h($list["title"]) ?>)</a>
				</p>
<?php		elseif($list["action"] === "fav_albums"):?>
				<p>
					<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="w3-round width_25px" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
					<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><?= h($list["faved_username"]) ?></a>
				</p>
				<p>
					<img src="<?= h($base_path) ?>img/favalbums_on.svg" class="w3-round width_25px" alt="fav album" />
					<a href="<?= h($base_path) ?>a/<?= h($list["album_id"]) ?>">
						<?= h($list["artist"]) ?> / <?= h($list["title"]) ?>
					</a>
				</p>
<?php		elseif($list["action"] === "new_user"):?>
				<p>
					<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="w3-round width_25px" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
					<a href="<?= h($base_path) ?>u/<?= h($list["id"]) ?>"><?= h($list["username"]) ?></a>
				</p>
				<p>
					ようこそ <a href="<?= h($base_path) ?>"><?= h($base_title) ?></a> へ！
				</p>
<?php		endif;?>
				<p class="notice"><?= h(timeAgoInWords($list["created"])) ?></p>
		</div>
<?php endforeach; ?>
	</div>

<?php else: ?>

	<div class="w3-center w3-padding info">
		<p>Activities not found.</p>
	</div>

<?php endif; ?>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


<script>
;$(function(){

	$("#id_counter_fav_review").text($(".action_fav_reviews").length);
	$("#id_counter_fav_tracks").text($(".action_fav_tracks").length);
	$("#id_counter_fav_albums").text($(".action_fav_albums").length);
	$("#id_counter_new_user").text($(".action_new_user").length);

	// fav_reviews
	$("#id_fav_review").on("click.js", function(){
		$(".user_info:not(.action_fav_reviews)").slideUp();
		$(".action_fav_reviews").slideDown();
	});

	// fav_reviews
	$("#id_fav_tracks").on("click.js", function(){
		$(".user_info:not(.action_fav_tracks)").slideUp();
		$(".action_fav_tracks").slideDown();
	});

	// fav_albums
	$("#id_fav_albums").on("click.js", function(){
		$(".user_info:not(.action_fav_albums)").slideUp();
		$(".action_fav_albums").slideDown();
	});

	// new user
	$("#id_new_user").on("click.js", function(){
		$(".user_info:not(.action_new_user)").slideUp();
		$(".action_new_user").slideDown();
	});

});
</script>

</body>
</html>