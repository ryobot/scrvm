
	<!-- user profile -->
	<div class="user_info">
		<div class="cover">
			<img src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.svg" ?>" alt="<?= h($user["username"]) ?>" />
		</div>
		<div class="detail">
			<h3><?= h($user["username"]) ?></h3>
<?php if(isset($user["profile"]) && $user["profile"] !== ""): ?>
			<?= nl2br(linkIt(h($user["profile"]))) ?>
<?php endif;?>
<?php if(isset($user["has_invited_username"])): ?>
			<div class="invitedfrom">
				(invited from
				<a href="<?= h($base_path) ?>u/<?= h($user["has_invited_user_id"]) ?>"><?= h($user["has_invited_username"]) ?></a>
				)
			</div>
<?php endif; ?>
		</div>
		<div class="user_menu_list">
			<div class="user_menu_list_block" data-menu="View"><a href="<?= h("{$base_path}Users/View/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/reviews.svg" alt="reviews" title="reviews" /></div>
				<div class="text"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></div>
			</a></div>
			<div class="user_menu_list_block" data-menu="FavTracks"><a href="<?= h("{$base_path}Users/FavTracks/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/favtracks_on.svg" alt="fav tracks" title="fav tracks" /></div>
				<div class="text"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></div>
			</a></div>
			<div class="user_menu_list_block" data-menu="FavAlbums"><a href="<?= h("{$base_path}Users/FavAlbums/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/favalbums_on.svg" alt="fav albums" title="fav albums" /></div>
				<div class="text"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></div>
			</a></div>
			<div class="user_menu_list_block" data-menu="FavReviews"><a href="<?= h("{$base_path}Users/FavReviews/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/fav_on.svg" alt="fav reviews" title="fav reviews" /></div>
				<div class="text"><?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?></div>
			</a></div>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
			<div class="user_menu_list_block" data-menu="Syncs"><a href="<?= h("{$base_path}Users/Syncs/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/sync.svg" alt="sync point" title="sync point" /></div>
				<div class="text"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</div>
			</a></div>
<?php endif;?>
			<div class="user_menu_list_block" data-menu="Chart"><a href="<?= h("{$base_path}Users/Chart/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/chart.svg" alt="chart" title="chart" /></div>
				<div class="text">-</div>
			</a></div>
		</div>
	</div>

<script>
;$(function(){
	// active
	var $user_menu_list_block = $(".user_menu_list_block");
	var path = location.pathname;
	$user_menu_list_block.each(function(){
		if ( path.match(new RegExp("^"+BASE_PATH+"Users/"+$(this).attr("data-menu"))) ) {
			$(this).addClass("user_menu_list_block_active");
		}
	});
});
</script>

