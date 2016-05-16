
	<!-- user profile -->
	<table class="w100per user_profile">
		<tr>
			<td class="w80px tacenter vtalgmiddle">
				<img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" />
			</td>
			<td class="user_menu_list">
				<ul>
					<li class="reviews"><a href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a></li>
					<li class="fav_tracks"><a href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a></li>
					<li class="fav_albums"><a href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a></li>
					<li class="fav_reviews"><a href="<?= h("{$base_path}Users/FavReviews?id={$user_id}") ?>"><?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?></a></li>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
					<li class="syncs"><a href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a></li>
<?php endif;?>
				</ul>
			</td>
		</tr>
		<tr>
			<td colspan="2">
<?php if(isset($user["profile"]) && $user["profile"] !== ""): ?>
				<?= nl2br(linkIt(h($user["profile"]))) ?>
<?php endif;?>
			</td>
		</tr>
	</table>
