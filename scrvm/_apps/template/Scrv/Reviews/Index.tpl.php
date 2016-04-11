<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

$prev_link = $base_path . "?" . http_build_query(array(
	"offset" => $pager["offset"]-$pager["limit"],
));
$next_link = $base_path . "?" . http_build_query(array(
	"offset" => $pager["offset"]+$pager["limit"],
));
if($pager["offset"]-$pager["limit"] < 0){
	$prev_link = "";
}
if($pager["offset"]+$pager["limit"] >= $pager["total_count"]){
	$next_link = "";
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Reviews</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Reviews (<?= h($pager["total_count"]) ?>)</h2>

	<p class="pager">
<?php if($prev_link !== ""): ?>
		<a href="<?= h($prev_link) ?>">≪prev</a>
<?php else:?>
		<span>≪prev</span>
<?php endif;?>
		<?= h($pager["now_page"]) ?> / <?= h($pager["max_page"]) ?>
<?php if($next_link !== ""): ?>
		<a href="<?= h($next_link) ?>">next≫</a>
<?php else:?>
		<span>next≫</span>
<?php endif;?>
	</p>

	<table>
<?php foreach($reviews as $review): ?>
		<tr>
			<td><img class="album_search_cover_result" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/user.png" ?>" alt="" /></td>
			<td>
				<div><a href="<?= h($base_path) ?>Albums/View?id=<?= h($review["album_id"]) ?>"><?= h( "{$review["artist"]} / {$review["title"]}") ?></a> (<?= isset($review["year"]) && $review["year"] !== "" ? h($review["year"]) : "unknown" ?>)</div>
				<div>reviewd by <a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a></div>
				<div>
					<?= h($review["created"]) ?>
<?php if( $review["user_id"] === $login_user_data["id"] ):?>
					<a href="<?= h($base_path) ?>Reviews/Edit?id=<?= h($review["id"]) ?>">edit</a>
					<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete">delete</a>
<?php endif;?>
				</div>
			</td>
			<td>
				<img src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>_30.png" alt="<?= h($review["listening_system"]) ?>" />
				<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><img class="user_photo_min" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.png" ?>" alt="<?= h($review["username"]) ?>" /></a>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<?= h($review["body"]) ?>
				<hr />
			</td>
		</tr>
<?php endforeach; ?>
	</table>

	<p class="pager">
<?php if($prev_link !== ""): ?>
		<a href="<?= h($prev_link) ?>">≪prev</a>
<?php else:?>
		<span>≪prev</span>
<?php endif;?>
		<?= h($pager["now_page"]) ?> / <?= h($pager["max_page"]) ?>
<?php if($next_link !== ""): ?>
		<a href="<?= h($next_link) ?>">next≫</a>
<?php else:?>
		<span>next≫</span>
<?php endif;?>
	</p>

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