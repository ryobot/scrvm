
	<!-- user profile -->
	<div class="w3-padding-top w3-padding-right w3-padding-left w3-center w3-card-2 w3-white _info">
		<p><img class="cover_user" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.svg" ?>" alt="<?= h($user["username"]) ?>" /></p>
		<h5><a href="<?= h($base_path) ?>Users/View/id/<?= h($user["id"]) ?>"><?= h($user["username"]) ?></a></h5>
<?php if(isset($user["profile"]) && $user["profile"] !== ""): ?>
		<p><?= nl2br(linkIt(h($user["profile"]))) ?></p>
<?php endif;?>
<?php if(isset($user["has_invited_username"])): ?>
		<p>(invited from <a href="<?= h($base_path) ?>u/<?= h($user["has_invited_user_id"]) ?>"><?= h($user["has_invited_username"]) ?></a>)</p>
<?php endif; ?>
		<div class="flex-container user_menu">
			<div class="user_menu_list_block" data-menu="View"><a href="<?= h("{$base_path}Users/View/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/reviews.svg" title="reviews" /></div>
				<div class="text"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></div>
			</a></div>
			<div class="user_menu_list_block" data-menu="FavTracks"><a href="<?= h("{$base_path}Users/FavTracks/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/favtracks_on.svg" title="fav tracks" /></div>
				<div class="text"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></div>
			</a></div>
			<div class="user_menu_list_block" data-menu="FavAlbums"><a href="<?= h("{$base_path}Users/FavAlbums/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/favalbums_on.svg" title="fav albums" /></div>
				<div class="text"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></div>
			</a></div>
			<div class="user_menu_list_block" data-menu="FavReviews"><a href="<?= h("{$base_path}Users/FavReviews/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/fav_on.svg" title="fav reviews" /></div>
				<div class="text"><?= isset($user["favreviews_count"]) ? h($user["favreviews_count"]) : "0" ?></div>
			</a></div>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
			<div class="user_menu_list_block" data-menu="Syncs"><a href="<?= h("{$base_path}Users/Syncs/id/{$user_id}") ?>">
				<div><img src="<?= h($base_path) ?>img/sync.svg" title="sync point" /></div>
				<div class="text"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</div>
			</a></div>
<?php endif;?>
		</div>
	</div>

<script>
;$(function(){
	var run_name = "<?= h($__run_class_name) ?>";
	var $user_menu_list_block = $(".user_menu_list_block");
	$user_menu_list_block.each(function(){
		if ( $(this).attr("data-menu") === run_name ) {
			$(this).addClass("active");
		}
	});
});
</script>

