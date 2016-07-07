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
<body><div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<h2>Activity</h2>

<?php if(count($lists) > 0):?>

<div class="info mgb5px">
	<div class="user_menu_list cursorpointer">
		<div class="user_menu_list_block" data-menu="FavReviews" id="id_fav_review">
			<img src="<?= h($base_path) ?>img/fav_on.svg" class="img16x16" alt="reviews" title="reviews" />
			<div>Review</div>
		</div>
		<div class="user_menu_list_block" data-menu="FavTracks" id="id_fav_tracks">
			<img src="<?= h($base_path) ?>img/favtracks_on.svg" class="img16x16" alt="fav tracks" title="fav tracks" />
			<div>Track</div>
		</div>
		<div class="user_menu_list_block" data-menu="FavAlbums" id="id_fav_albums">
			<img src="<?= h($base_path) ?>img/favalbums_on.svg" class="img16x16" alt="fav albums" title="fav albums" />
			<div>Album</div>
		</div>
		<div class="user_menu_list_block" data-menu="Users" id="id_new_user">
			<img src="<?= h($base_path) ?>img/user.svg" class="img16x16" alt="new users" title="new users" />
			<div>User</div>
		</div>
	</div>
</div>

<!-- lists -->
<div class="user_list_activity">
<?php foreach($lists as $list): ?>
	<div class="user_info action_<?= h($list["action"]) ?>">
		<div class="detail">
<?php		if( $list["action"] === "fav_reviews" ): ?>
			<div class="mgb5px">
				<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="user_photo_min" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
				<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><?= h($list["faved_username"]) ?></a>
			</div>
			<div class="mgb5px">
				<img src="<?= h($base_path) ?>img/fav_on.svg" class="img16x16" alt="fav review" />
				<a href="<?= h($base_path) ?>r/<?= h($list["review_id"]) ?>"><?= h($list["artist"] . " / " . $list["title"]) ?></a>
				(reviewed by <a href="<?= h($base_path) ?>u/<?= h($list["user_id"]) ?>"><?= h($list["username"]) ?></a>)
			</div>
<?php		elseif($list["action"] === "fav_tracks"):?>
			<div class="mgb5px">
				<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="user_photo_min" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
				<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><?= h($list["faved_username"]) ?></a>
			</div>
			<div class="mgb5px">
				<img src="<?= h($base_path) ?>img/favtracks_on.svg" class="img16x16" alt="fav track" />
				<a href="<?= h($base_path) ?>a/<?= h($list["album_id"]) ?>">
					<?= h($list["track_num"]) ?>. <?= h($list["track_title"]) ?>
					(<?= h($list["artist"] . "/" . $list["title"]) ?>)
				</a>
			</div>
<?php		elseif($list["action"] === "fav_albums"):?>
			<div class="mgb5px">
				<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="user_photo_min" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
				<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><?= h($list["faved_username"]) ?></a>
			</div>
			<div class="mgb5px">
				<img src="<?= h($base_path) ?>img/favalbums_on.svg" class="img16x16" alt="fav album" />
				<a href="<?= h($base_path) ?>a/<?= h($list["album_id"]) ?>"><?= h($list["artist"] . " / " . $list["title"]) ?></a>
			</div>
<?php		elseif($list["action"] === "new_user"):?>
			<div class="mgb5px">
				<a href="<?= h($base_path) ?>u/<?= h($list["faved_user_id"]) ?>"><img class="user_photo_min" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["faved_username"]) ?>" /></a>
				<a href="<?= h($base_path) ?>u/<?= h($list["id"]) ?>"><?= h($list["username"]) ?></a>
			</div>
			<div class="mgb5px">
				<img src="<?= h($base_path) ?>img/user.svg" class="img16x16" alt="new user" />
				ようこそ <a href="<?= h($base_path) ?>"><?= h($base_title) ?></a> へ！
			</div>
<?php		endif;?>
			<div class="mgb5px">
				<span class="post_date"><?= h(timeAgoInWords($list["created"])) ?></span>
			</div>
		</div>
	</div>
<?php endforeach; ?>
</div>

<?php endif; ?>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){

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