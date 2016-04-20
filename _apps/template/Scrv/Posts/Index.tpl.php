<?php
/**
 * Posts/Index.tpl.php
 * @author mgng
 */

$most_prev_link = $base_path . "Posts";
$prev_link = $base_path . "Posts?" . hbq(array("page" => $pager["now_page"]-1,));
$next_link = $base_path . "Posts?" . hbq(array("page" => $pager["now_page"]+1,));
$most_next_link = $base_path . "Posts?" . hbq(array("page" => $pager["max_page"],));
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => $base_path . "Posts?" . hbq(array("page" => $nav["page"],)),
	);
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Posts</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Posts (<?= h($pager["total_count"]) ?>)</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

<?php if($is_login ): ?>
	<form action="<?= h($base_path) ?>Posts/Add" method="POST">
		<input type="hidden" name="token" value="<?= h($token) ?>" />
		<p><input type="text" name="title" id="id_title" value="<?= isset($post_params["title"]) ? h($post_params["title"]) : "" ?>" placeholder="title" required="required" /></p>
		<p><textarea name="body" id="id_body" placeholder="content" required="required"><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea></p>
		<p class="actions"><input type="submit" value="Save Post" ></p>
	</form>
<?php endif;?>

<?php if(count($lists) > 0): ?>

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

	<div class="lists">
		<table class="w100per every_other_row_odd">
<?php foreach($lists as $list): ?>
			<tr>
				<td>
					<h4><?= h($list["title"]) ?></h4>
					<p><?= nl2br(h($list["body"])) ?></p>
					<p>(<strong><?= isset($list["username"]) ? h($list["username"]) : "(delete user)" ?></strong> <?= h(timeAgoInWords($list["created"])) ?>)</p>
				</td>
			</tr>
<?php endforeach; unset($list) ?>
		</table>
	</div>

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
</body>
</html>