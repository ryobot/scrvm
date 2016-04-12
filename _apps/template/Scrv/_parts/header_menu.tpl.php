<?php
/**
 * _parts/header_menu.tpl.php
 * @author mgng
 */

// TODO new user の扱いはどうする？
?>

<h1><a href="<?= h($base_path) ?>"><?= h($base_title) ?></a></h1>

<p class="auth">
<?php if( $is_login ):?>
	user : <?= h($login_user_data["username"]) ?>
	<a href="<?= h($base_path) ?>Auth/Logout">Logout</a>
<?php else: ?>
	user : guest
	<a href="<?= h($base_path) ?>Auth">Login</a>
	<a href="<?= h($base_path) ?>Users/Add">new user</a>
<?php endif; ?>
</p>

<p class="menu">
	<a href="<?= h($base_path) ?>">Reviews</a>
	<a href="<?= h($base_path) ?>Albums">Albums</a>
	<a href="<?= h($base_path) ?>Users">Users</a>
	<a href="<?= h($base_path) ?>Posts">Posts</a>
</p>

