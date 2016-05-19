<?php
/**
 * Reviews/Edit.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Reviews :: Edit</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Edit Your Review</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="review">
		<h3><?= h($album["artist"]) ?> / <?= h($album["title"]) ?> (<?= isset($album["year"]) ? h($album["year"]) : "unknown" ?>)</h3>
		<p><img src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h($album["artist"]) ?> / <?= h($album["title"]) ?>" /></p>

		<form action="<?= h($base_path) ?>Reviews/EditRun" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<input type="hidden" name="album_id" value="<?= h($album_id) ?>">
			<input type="hidden" name="review_id" value="<?= h($review_id) ?>">
			<p id="id_listening_last_group">
				いつ聴いた？
				<label><input type="radio" name="listening_last" value="today" id="id_listening_last_today" checked="checked">今日</label>
				<label><input type="radio" name="listening_last" value="recently" id="id_listening_last_recently">最近</label>
			</p>
			<p id="id_listening_system_group">
				再生方法は？
				<label><input type="radio" name="listening_system" value="home" id="id_listening_system_home" checked="checked"> <img src="<?= h($base_path) ?>img/home.svg" alt="home" class="img32x32" /></label>
				<label><input type="radio" name="listening_system" value="headphones" id="id_listening_system_headphones"> <img src="<?= h($base_path) ?>img/headphones.svg" alt="headphones" class="img32x32" /></label>
				<label><input type="radio" name="listening_system" value="car" id="id_listening_system_car"> <img src="<?= h($base_path) ?>img/car.svg" alt="car" class="img32x32" /></label>
				<label><input type="radio" name="listening_system" value="other" id="id_listening_system_other"> <img src="<?= h($base_path) ?>img/other.svg" alt="other" class="img32x32" /></label>
			</p>
<?php if(isset($login_user_data["twitter_user_id"])): ?>
			<p><label><input type="checkbox" name="send_twitter" id="id_send_twitter" value="1"> post to twitter</label></p>
			<p>※twitterへ投稿する場合、140文字を超えても投稿はできますが一部省略されます。</p>
<?php endif; ?>
			<p><textarea name="body" id="id_body" cols="30" rows="10" placeholder="write a review."><?= h($post_params["body"]) ?></textarea></p>
			<p class="actions">
				<input type="submit" value="Save Review" />
				<span id="id_review_counter"></span>
			</p>
		</form>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){
	$("#id_listening_last_<?= h($post_params["listening_last"]) ?>").prop("checked", "checked");
	$("#id_listening_system_<?= h($post_params["listening_system"]) ?>").prop("checked", "checked");
});
</script>
<script src="<?= h($base_path)?>js/Reviews.write.js?v1"></script>

</body>
</html>