<?php
/**
 * Reviews/Index.tpl.php
 * @author mgng
 */

$most_prev_link = $base_path;
$prev_link = $base_path . "?" . hbq(array("page" => $pager["now_page"]-1,));
$next_link = $base_path . "?" . hbq(array("page" => $pager["now_page"]+1,));
$most_next_link = $base_path . "?" . hbq(array("page" => $pager["max_page"],));
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => $base_path . "?" . hbq(array("page" => $nav["page"],)),
	);
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

<?php if(count($reviews) > 0):?>

	<div class="tacenter">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

	<table class="w100per every_other_row_odd">
<?php foreach($reviews as $review): ?>
		<tr>
			<td class="w80px tacenter">
				<p>
					<a href="<?= h($base_path) ?>Albums/View?id=<?= h($review["album_id"]) ?>">
						<img class="album_cover" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/user.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
					</a>
				</p>
			</td>
			<td>
				<p>
					<a href="<?= h($base_path) ?>Albums/View?id=<?= h($review["album_id"]) ?>">
						<?= h( "{$review["artist"]} / {$review["title"]}") ?>
						(<?= isset($review["year"]) && $review["year"] !== "" ? h($review["year"]) : "unknown" ?>)
					</a>
				</p>
				<div class="review_comment">
					<p><?= $review["body"] === "" || $review["body"] === "listening log" ? "(no review)" : nl2br(h($review["body"])) ?></p>
					<p>
						<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($review["user_img_file"]) ? "files/attachment/photo/{$review["user_img_file"]}" : "img/user.png" ?>" alt="<?= h($review["username"]) ?>" /></a>
						<img class="vtalgmiddle" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>_30.png" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
						<a href="<?= h($base_path) ?>Users/View?id=<?= h($review["user_id"]) ?>"><?= h($review["username"]) ?></a>
						<span class="post_date"><?= h(timeAgoInWords($review["created"])) ?></span>
					</p>
				</div>
<?php if( $review["reviews_count"] - 1 > 0 ): ?>
				<p class="taright"><a href="<?= h($base_path) ?>Albums/View?id=<?= h($review["album_id"]) ?>">&raquo; other <?= $review["reviews_count"] - 1 ?> review<?= $review["reviews_count"] - 1 > 1 ? "s" : "" ?></a></p>
<?php endif; ?>
<?php if( $is_login && $review["user_id"] === $login_user_data["id"] ):?>
				<p class="actions">
					<a href="<?= h($base_path) ?>Reviews/Edit?id=<?= h($review["id"]) ?>">edit</a>
					<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete">delete</a>
				</p>
<?php endif;?>
			</td>
		</tr>
<?php endforeach; ?>
	</table>

	<div class="tacenter">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>


<?php endif; ?>

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