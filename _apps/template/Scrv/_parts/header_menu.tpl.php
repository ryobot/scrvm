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
		<a href="<?= h($base_path) ?>Users/View?id=<?= h($login_user_data["id"]) ?>">
			<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($login_user_data["img_file"]) ? "files/attachment/photo/{$login_user_data["img_file"]}" : "img/user.png" ?>" alt="<?= h($login_user_data["username"]) ?>" title="<?= h($login_user_data["username"]) ?>" />
		</a>
<?php endif; ?>
		<span id="dropmenu_normal">
			<img src="<?= h($base_path)?>img/menu_32.png" alt="menu" title="menu" />
		</span>

		<div class="dropmenu">
			<ul>
<?php if( $is_login ):?>
				<li><a href="<?= h($base_path) ?>Users/View?id=<?= h($login_user_data["id"]) ?>">profile</a></li>
				<li><a href="<?= h($base_path) ?>Users/Edit">edit</a></li>
				<li><a id="id_logout" href="javascript:;">Logout</a></li>
<?php else: ?>
				<li><a href="<?= h($base_path) ?>Auth">Login</a></li>
<?php endif; ?>
				<li><a href="<?= h($base_path) ?>About">About/Help</a></li>
			</ul>
		</div>
		<form id="id_form_logout" action="<?= h($base_path) ?>Auth/Logout" method="POST"></form>
		<script>
			;$(function(){
				$("#dropmenu_normal").on("click",function(){
					$(".dropmenu").toggle();
				});
				$("#id_logout").on("click.js",function(){
					$("#id_form_logout").submit();
				});
			});
		</script>
	</div>
</div>

<div class="menu">
	<div class="menu_block"><a href="<?= h($base_path) ?>">Reviews</a></div>
	<div class="menu_block"><a href="<?= h($base_path) ?>Albums">Albums</a></div>
	<div class="menu_block"><a href="<?= h($base_path) ?>Users">Users</a></div>
	<div class="menu_block"><a href="<?= h($base_path) ?>Posts">Posts</a></div>
</div>

