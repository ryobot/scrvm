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
<div class="contents">

	<h2>Add Review</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<div class="album_info">

		<h3><?= h($album["artist"]) ?> / <?= h($album["title"]) ?> (<?= isset($album["year"]) ? h($album["year"]) : "unknown" ?>)</h3>

		<div class="info">
			<div class="cover">
				<img src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h($album["artist"]) ?> / <?= h($album["title"]) ?>" />
			</div>
			<div class="detail">
			</div>
		</div>

		<form action="<?= h($base_path) ?>Reviews/AddRun" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<input type="hidden" name="album_id" value="<?= h($album_id) ?>">
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
			<p><textarea name="body" id="id_body" cols="30" rows="10" placeholder="write a review."><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea></p>
			<p class="actions">
				<input type="submit" value="Save Review" />
				<span id="id_review_counter"></span>
			</p>
		</form>
	</div>

	<!-- reviews -->
	<h3>Reviews (<?= count($reviews) ?>)</h3>
<?php foreach($reviews as $review): ?>
	<div class="review">
		<div class="review_comment"><?= $review["body"] === "" || $review["body"] === "listening log" ? "(no review)" : nl2br(linkIt(h($review["body"]))) ?></div>
		<div>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>">
				<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($review["username"]) ?>" />
			</a>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
			-
			<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>">
				<span class="post_date"><?= h( timeAgoInWords($review["created"])) ?></span>
			</a>
<?php if($review["listening_last"] === "today"): ?>
			<img class="vtalgmiddle img16x16" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>.svg" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>

<script src="<?= h($base_path)?>js/Reviews.write.js"></script>

</html>