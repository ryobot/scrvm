
	<!-- user profile -->
	<h2><?= h($user["username"]) ?></h2>
	<div class="info w100per">
		<div class="displaytable">
			<div class="displaytablecell user_photo">
				<img src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.svg" ?>" alt="<?= h($user["username"]) ?>" />
			</div>
			<div class="displaytablecell vtalgmiddle">
<?php if(isset($user["profile"]) && $user["profile"] !== ""): ?>
				<?= nl2br(linkIt(h($user["profile"]))) ?>
<?php endif;?>
			</div>
		</div>
		<div class="displaytable w100per tacenter user_menu_list">
			<div class="displaytablecell">
				<a href="<?= h("{$base_path}Users/View/id/{$user_id}") ?>">
					<img src="<?= h($base_path) ?>img/reviews.svg" class="img16x16" alt="reviews" />
					<?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?>
				</a>
			</div>
			<div class="displaytablecell">
				<a href="<?= h("{$base_path}Users/FavTracks/id/{$user_id}") ?>">
					<img src="<?= h($base_path) ?>img/favtracks_on.svg" class="img16x16" alt="fav tracks" />
					<?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?>
				</a>
			</div>
			<div class="displaytablecell">
				<a href="<?= h("{$base_path}Users/FavAlbums/id/{$user_id}") ?>">
					<img src="<?= h($base_path) ?>img/favalbums_on.svg" class="img16x16" alt="fav albums" />
					<?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?>
				</a>
			</div>
			<div class="displaytablecell">
				<a href="<?= h("{$base_path}Users/FavReviews/id/{$user_id}") ?>">
					<img src="<?= h($base_path) ?>img/fav_on.svg" class="img16x16" alt="fav reviews" />
					<?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?>
				</a>
			</div>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
			<div class="displaytablecell">
				<a href="<?= h("{$base_path}Users/Syncs/id/{$user_id}") ?>">
					<img src="<?= h($base_path) ?>img/sync.svg" class="img16x16" alt="sync" />
					<?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt
				</a>
			</div>
<?php endif;?>
		</div>
	</div>

