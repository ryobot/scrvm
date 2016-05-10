<?php
/**
 * _parts/header_menu.tpl.php
 * @author mgng
 */
?>

<div class="header">
	<ul>
		<li class="taleft">
			<h1 class="header_title"><a href="<?= h($base_path) ?>"><?= h($base_title) ?></a></h1>
		</li>
		<li class="taright">
<?php if( $is_login ):?>
			<div>user : <a href="<?= h($base_path) ?>Users/View?id=<?= h($login_user_data["id"]) ?>"><?= h($login_user_data["username"]) ?></a></div>
			<p class="actions">
				<a href="<?= h($base_path) ?>Users/Edit">edit</a>
				<a id="id_logout" href="javascript:;">Logout</a>
			</p>
			<form id="id_form_logout" action="<?= h($base_path) ?>Auth/Logout" method="POST"></form>
			<script>
				;$(function(){
					$("#id_logout").on("click.js",function(){
						$("#id_form_logout").submit();
					});
				});
			</script>
<?php else: ?>
			<div>user : guest</div>
			<p class="actions">
				<a href="<?= h($base_path) ?>Auth">Login</a>
			</p>
<?php endif; ?>
			<p class="actions"><a href="<?= h($base_path) ?>About">About/Help</a></p>
		</li>
	</ul>
</div>

<div class="menu">
	<ul>
		<li><a href="<?= h($base_path) ?>">Reviews</a></li>
		<li><a href="<?= h($base_path) ?>Albums">Albums</a></li>
		<li><a href="<?= h($base_path) ?>Users">Users</a></li>
		<li><a href="<?= h($base_path) ?>Posts">Posts</a></li>
	</ul>
</div>

