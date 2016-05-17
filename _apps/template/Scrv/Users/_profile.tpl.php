
	<!-- user profile -->
	<div class="user_profile">
		<div class="displaytable">
			<div class="displaytablecell w80px">
				<img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" />
			</div>
			<div class="displaytablecell vtalgmiddle">
<?php if(isset($user["profile"]) && $user["profile"] !== ""): ?>
				<?= nl2br(linkIt(h($user["profile"]))) ?>
<?php endif;?>
			</div>
		</div>
		<div class="displaytable w100per tacenter user_menu_list">
			<div class="displaytablecell"><a class="reviews" href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a></div>
			<div class="displaytablecell"><a class="fav_tracks" href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a></div>
			<div class="displaytablecell"><a class="fav_albums" href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a></div>
			<div class="displaytablecell"><a class="fav_reviews" href="<?= h("{$base_path}Users/FavReviews?id={$user_id}") ?>"><?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?></a></div>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
			<div class="displaytablecell"><a class="syncs" href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a></div>
<?php endif;?>
		</div>
	</div>

