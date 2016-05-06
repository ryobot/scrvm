<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

$review_title = "{$review["artist"]} / {$review["title"]} (".
		(isset($review["year"]) && $review["year"] !== "" ? $review["year"] : "unknown") . ") - by {$review["username"]}";

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($review_title) ?> | <?= h($base_title) ?> :: Reviews</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2><?= h($review_title) ?></h2>

	<table class="w100per every_other_row_odd">
		<tr>
			<td class="w80px tacenter">
				<p>
					<a href="<?= h($base_path) ?>Albums/View?id=<?= h($review["album_id"]) ?>">
						<img class="album_cover" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
					</a>
				</p>
			</td>
			<td>
				<div class="review_comment">
					<p><?= $review["body"] === "" || $review["body"] === "listening log" ? "(no review)" : nl2br(linkIt(h($review["body"]))) ?></p>
					<p>
						<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.png" ?>" alt="<?= h($review["username"]) ?>" /></a>
<?php if($review["listening_last"] === "today"): ?>
						<img class="vtalgmiddle" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>_30.png" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif; ?>
						<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
						-
						<span class="post_date"><a href="<?= h($base_path) ?>Reviews/View?id=<?= h($review["id"]) ?>"><?= date('Y年n月j日 H時i分',strtotime($review["created"])) ?></a></span>
					</p>
				</div>
			</td>
		</tr>
	</table>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>
</div>

<script>
;$(function(){
	$(".review_delete").each(function(){
		var $del = $(this);
		var delete_id = $del.attr("data-delete_id");
		$del.on("click.js", function(){
			if(confirm("are you sure ?")){
				$.ajax( "<?= h($base_path) ?>Reviews/Del", {
					method : 'POST',
					dataType : 'json',
					data : { id : delete_id }
				})
				.done(function(json){
					location.href="<?= h($base_path) ?>Reviews";
				})
				.fail(function(e){
					alert("system error.");
				})
				.always(function(){
				});
			}
			return false;
		});
	});
});
</script>

</body>
</html>