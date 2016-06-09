<?php
/**
 * _parts/header_menu.tpl.php
 * @author mgng
 */
?>

<div class="header">

	<div class="header_block taleft">
		<h1 class="header_title"><a href="<?= h($base_path) ?>"><?= h($base_title) ?></a></h1>
	</div>
	<div class="header_block taright">
<?php if( $is_login ):?>
		<a href="<?= h($base_path) ?>Users/View/id/<?= h($login_user_data["id"]) ?>">
			<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($login_user_data["img_file"]) ? "files/attachment/photo/{$login_user_data["img_file"]}" : "img/user.svg" ?>" alt="<?= h($login_user_data["username"]) ?>" title="<?= h($login_user_data["username"]) ?>" />
		</a>
<?php endif; ?>
		<span id="dropmenu_normal">
			<img src="<?= h($base_path)?>img/menu.svg" width="32" height="32" alt="menu" title="menu" />
		</span>

		<div class="dropmenu">
			<ul>
<?php if( $is_login ):?>
				<li><a href="<?= h($base_path) ?>Users/View/id/<?= h($login_user_data["id"]) ?>">profile</a></li>
				<li><a href="<?= h($base_path) ?>Users/Edit">edit</a></li>
				<li><a id="id_logout" href="javascript:;">Logout</a></li>
<?php else: ?>
				<li><a href="<?= h($base_path) ?>Auth">Login</a></li>
<?php endif; ?>
				<li><a href="<?= h($base_path) ?>About">About/Help</a></li>
			</ul>
		</div>
		<form id="id_form_logout" action="<?= h($base_path) ?>Auth/Logout" method="POST"></form>
	</div>
</div>

<div class="menu">
	<div class="menu_block" data-menu="Reviews"><a href="<?= h($base_path) ?>">
		<div><img src="<?= h($base_path) ?>img/reviews.svg" alt="Reviews" /></div>
		<span>Reviews</span>
	</a></div>
	<div class="menu_block" data-menu="Albums"><a href="<?= h($base_path) ?>Albums">
		<div><img src="<?= h($base_path) ?>img/albums.svg" alt="Albums" /></div>
		<span>Albums</span>
	</a></div>
	<div class="menu_block" data-menu="Users"><a href="<?= h($base_path) ?>Users">
		<div><img src="<?= h($base_path) ?>img/users.svg" alt="Users" /></div>
		<span>Users</span>
	</a></div>
<?php if( $is_login ):?>
	<div class="menu_block" data-menu="Posts"><a href="<?= h($base_path) ?>Posts">
		<div><img src="<?= h($base_path) ?>img/posts.svg" alt="Posts" /></div>
		<span>Posts</span>
	</a></div>
<?php else: ?>
<?php endif; ?>
	<div class="menu_block" data-menu="About"><a href="<?= h($base_path) ?>About">
		<div><img src="<?= h($base_path) ?>img/about.svg" alt="About" /></div>
		<span>About</span>
	</a></div>
</div>

<script>
;$(function(){
	// menu 幅揃えとactive
	var $div = $(".menu_block");
	var width = Math.floor(100/$div.length);
	var path = location.pathname.replace(new RegExp("^"+BASE_PATH), "");
	if ( path === "" ) {
		path = "Reviews";
	}
	$div.each(function(){
		$(this).css({width : width + "%"});
		if ( path.match($(this).attr("data-menu")) ) {
			$(this).addClass("menu_block_active");
		}
	});
});
</script>

