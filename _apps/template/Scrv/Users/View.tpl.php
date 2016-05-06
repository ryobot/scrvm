<?php
/**
 * Users/View.tpl.php
 * @author mgng
 */

$most_prev_link = "{$base_path}Users/View?id={$user_id}";
$prev_link = "{$base_path}Users/View?id={$user_id}&" . hbq(array("page" => $pager["now_page"]-1,));
$next_link = "{$base_path}Users/View?id={$user_id}&" . hbq(array("page" => $pager["now_page"]+1,));
$most_next_link = "{$base_path}Users/View?id={$user_id}&" . hbq(array("page" => $pager["max_page"],));
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$base_path}Users/View?id={$user_id}&" . hbq(array("page" => $nav["page"],)),
	);
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($user["username"]) ?> | <?= h($base_title) ?> :: Users :: View</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2><?= h($user["username"]) ?></h2>

	<!-- user profile -->
	<table class="w100per every_other_row_odd">
		<tr>
			<td class="w80px tacenter vtalgmiddle">
				<img class="user_photo" src="<?= h($base_path) ?><?= isset($user["img_file"]) ? "files/attachment/photo/{$user["img_file"]}" : "img/user.png" ?>" alt="<?= h($user["username"]) ?>" />
			</td>
			<td class="user_menu_list">
				<ul>
					<li class="reviews"><a href="<?= h("{$base_path}Users/View?id={$user_id}") ?>"><?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?></a></li>
					<li class="fav_tracks"><a href="<?= h("{$base_path}Users/FavTracks?id={$user_id}") ?>"><?= isset($user["favtracks_count"]) ? h($user["favtracks_count"]) : "0" ?></a></li>
					<li class="fav_albums"><a href="<?= h("{$base_path}Users/FavAlbums?id={$user_id}") ?>"><?= isset($user["favalbums_count"]) ? h($user["favalbums_count"]) : "0" ?></a></li>
<?php if($is_login && $user_id !== $login_user_data["id"]): ?>
					<li class="syncs"><a href="<?= h("{$base_path}Users/Syncs?id={$user_id}") ?>"><?= isset($user["sync_point"]) ? h($user["sync_point"]) : "0" ?> pt</a></li>
<?php endif;?>
				</ul>
			</td>
		</tr>
	</table>
<?php if(isset($user["profile"]) && $user["profile"] !== ""): ?>
	<p class="w100per user_profile"><?= nl2br(linkIt(h($user["profile"]))) ?></p>
<?php endif;?>

	<h3>Reviews (<?= isset($user["reviews_count"]) ? h($user["reviews_count"]) : "0" ?>)</h3>

<?php if (count($reviews) > 0): ?>

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
			<td class="w80px">
				<img class="album_cover" src="<?= isset($review["img_file"])? "{$base_path}files/covers/{$review["img_file"]}" : "{$base_path}img/no_image.png" ?>" alt="<?= h( "{$review["artist"]} / {$review["title"]}") ?>" />
			</td>
			<td>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($review["album_id"]) ?>">
					<?= h("{$review["artist"]} / {$review["title"]}") ?>
					(<?= isset($review["year"]) && $review["year"] !== "" ? h($review["year"]) : "unknown" ?>)
				</a>
				<p><?= $review["body"] === "" || $review["body"] === "listening log" ? "(no review)" : nl2br(linkIt(h($review["body"]))) ?></p>
				<div>
<?php if($review["listening_last"] === "today"): ?>
					<img class="vtalgmiddle" src="<?= h($base_path) ?>img/<?= h($review["listening_system"]) ?>_30.png" alt="<?= h($review["listening_system"]) ?>" title="<?= h($review["listening_system"]) ?>" />
<?php endif;?>
					<a href="<?= h($base_path) ?>Reviews/View?id=<?= h($review["id"]) ?>">
						<span class="post_date"><?= h( timeAgoInWords($review["created"])) ?></span>
					</a>
				</div>
<?php if($is_login && $user_id === $login_user_data["id"]): ?>
				<p class="actions">
					<a href="<?= h($base_path) ?>Reviews/Edit?id=<?= h($review["id"]) ?>">edit</a>
					<a href="javascript:;" data-delete_id="<?= h($review["id"]) ?>" class="review_delete">delete</a>
				</p>
<?php endif;?>
			</td>
		</tr>
<?php		endforeach; ?>
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
					location.href="<?= h($base_path) ?>Users/View?id=<?= $login_user_data["id"] ?>";
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