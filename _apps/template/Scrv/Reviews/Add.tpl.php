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
<title>Reviews::Add - <?= h($base_title) ?></title>
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

		<div>
			<div class="cover">
				<img src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" alt="<?= h($album["artist"]) ?> / <?= h($album["title"]) ?>" />
			</div>
			<div class="detail">
			</div>
		</div>

		<form action="<?= h($base_path) ?>Reviews/AddRun" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<input type="hidden" name="album_id" value="<?= h($album_id) ?>">

<?php require __DIR__ . '/_review_form_parts.tpl.php'; ?>

		</form>
	</div>

	<!-- reviews -->
	<h3>Reviews (<?= count($reviews) ?>)</h3>
<?php foreach($reviews as $review): ?>
	<div class="review">
<?php if(
	($review["published"] === 0 && !$is_login)
	||
	($review["published"] === 0 && $is_login && $review["user_id"] !== $login_user_data["id"])
): ?>
		<div class="notice">
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($review["username"]) ?>" /></a>
			この投稿は非表示にされています。
		</div>
<?php else: ?>
		<div class="review_comment"><?= nl2br(linkIt(h($review["body"]))) ?></div>
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
			<img class="situation" src="<?= h($base_path) ?>img/situation/<?= h($review["listening_system"]) ?>.svg" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
		</div>
<?php endif; ?>
	</div>
<?php endforeach; ?>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>

<script src="<?= h($base_path)?>js/Reviews.write.js"></script>

</html>