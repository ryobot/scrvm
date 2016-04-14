<?php
/**
 * _parts/header_menu.tpl.php
 * @author mgng
 */

// TODO new user の扱いはどうする？
?>

<table class="w100per">
	<tr>
		<td class="w50per taleft">
			<h1 class="header_title"><a href="<?= h($base_path) ?>"><?= h($base_title) ?></a></h1>
		</td>
		<td class="w50per taright auth">
<?php if( $is_login ):?>
			<div>user : <a href="<?= h($base_path) ?>Users/View?id=<?= h($login_user_data["id"]) ?>"><?= h($login_user_data["username"]) ?></a></div>
			<p class="actions">
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
				<a href="<?= h($base_path) ?>Users/Add">new user</a>
			</p>
<?php endif; ?>
		</td>
	</tr>
</table>

<table class="menu w100per">
	<tr>
		<td class="w25per"><a href="<?= h($base_path) ?>">Reviews</a></td>
		<td class="w25per"><a href="<?= h($base_path) ?>Albums">Albums</a></td>
		<td class="w25per"><a href="<?= h($base_path) ?>Users">Users</a></td>
		<td class="w25per"><a href="<?= h($base_path) ?>Posts">Posts</a></td>
	</tr>
</table>

