<?php
/**
 * Reviews/Add.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Reviews :: Add</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Write a Review</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<h3><?= h($album["artist"]) ?> / <?= h($album["title"]) ?> (<?= isset($album["year"]) ? h($album["year"]) : "unknown" ?>)</h3>
	<p><img src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h($album["artist"]) ?> / <?= h($album["title"]) ?>" /></p>

	<form action="<?= h($base_path) ?>Reviews/AddRun" method="POST">
		<input type="hidden" name="token" value="<?= h($token) ?>" />
		<input type="hidden" name="album_id" value="<?= h($album_id) ?>">
		<table>
			<tr>
				<td>listening date</td>
				<td>
					<label><input type="radio" name="listening_last" value="today" id="id_listening_last_today" checked="checked">today</label>
					<label><input type="radio" name="listening_last" value="recently" id="id_listening_last_recently">recently</label>
				</td>
			</tr>
			<tr>
				<td>listening system</td>
				<td>
					<label><input type="radio" name="listening_system" value="home" id="id_listening_system_home" checked="checked">home</label>
					<label><input type="radio" name="listening_system" value="headphones" id="id_listening_system_headphones">headphones</label>
					<label><input type="radio" name="listening_system" value="car" id="id_listening_system_car">car</label>
					<label><input type="radio" name="listening_system" value="other" id="id_listening_system_other">other</label>
				</td>
			</tr>
		</table>
<?php if(isset($login_user_data["twitter_user_id"])): ?>
		<p><label><input type="checkbox" name="send_twitter" id="id_send_twitter" value="1"> twitterにも投稿する</label></p>
<?php endif; ?>
		<p><textarea name="body" id="id_body" cols="30" rows="10" placeholder="write a review."><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea></p>
		<p><input type="submit" value="Save Review" /></p>
	</form>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>