<?php
/**
 * Reviews/Edit.tpl.php
 * @author mgng
 */
use lib\Scrv\Helper\Reviews\Parse as ReviewsParse;
$ReviewsParse = new ReviewsParse();
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Reviews::Edit - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Edit Your Review</h2>
	</div>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

	<!-- album info -->
	<div class="w3-padding w3-center info">
		<img class="cover w3-card-4" src="<?= h($base_path) ?>files/covers/<?= h($album["img_file"]) ?>" />
		<h5>
			<span><?= h($album["title"]) ?></span>
			<br />
			<span class="w3-small"><?= h($album["artist"]) ?> (<?= isset($album["year"]) ? h($album["year"]) : "?" ?>)</span>
		</h5>
		<form action="<?= h($base_path) ?>Reviews/EditRun" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<input type="hidden" name="album_id" value="<?= h($album_id) ?>">
			<input type="hidden" name="review_id" value="<?= h($review_id) ?>">
<?php require __DIR__ . '/_review_form_parts.tpl.php'; ?>
		</form>
	</div>

	<!-- reviews -->
	<div class="w3-center">
		<h4>Reviews (<?= count($reviews) ?>)</h4>
	</div>
	<div class="flex-container w3-row-padding w3-padding-16 w3-center">
<?php foreach($reviews as $review): ?>
		<div class="w3-padding w3-center w3-white w3-card-2 w3-margin-bottom col">
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
			<p class="w3-left-align">
				<?= $ReviewsParse->replaceHashTagsToLink(nl2br(linkIt(h($review["body"]))), $base_path) ?>
			</p>
			<p>
				<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><img class="width_25px" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.svg" ?>" /></a>
				<a href="<?= h($base_path) ?>Users/View/id/<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
				-
				<a href="<?= h($base_path) ?>Reviews/View/id/<?= h($review["id"]) ?>"><?= h(timeAgoInWords($review["created"])) ?></a>
			</p>
<?php endif; ?>
		</div>
<?php endforeach; ?>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

<script>
;$(function(){
	$("#id_listening_last_<?= h($post_params["listening_last"]) ?>").prop("checked", "checked");
	$("#id_listening_system_<?= h($post_params["listening_system"]) ?>").prop("checked", "checked").trigger("click.js");
});
</script>
<script src="<?= h($base_path)?>js/Reviews.write.js?v1"></script>

</body>
</html>