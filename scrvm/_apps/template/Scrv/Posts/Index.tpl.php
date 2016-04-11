<?php
/**
 * Posts/Index.tpl.php
 * @author mgng
 */

$prev_link = $base_path . "Posts?" . http_build_query(array(
	"offset" => $pager["offset"]-$pager["limit"],
));
$next_link = $base_path . "Posts?" . http_build_query(array(
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
		<p><input type="text" name="title" id="id_title" value="<?= isset($post_params["title"]) ? h($post_params["title"]) : "" ?>" placeholder="title" /></p>
		<p><textarea name="body" id="id_body" placeholder="body"><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea></p>
		<p><input type="submit" value="Save Post" ></p>
	</form>
<?php endif;?>

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

	<div class="lists">
<?php foreach($lists as $list): ?>
		<div>
			<h4><?= h($list["title"]) ?></h4>
			<p><?= h($list["body"]) ?></p>
			<p>(posted by <strong><?= isset($list["username"]) ? h($list["username"]) : "(delete user)" ?></strong> <?= h($list["created"]) ?>)</p>
		</div>
		<hr />
<?php endforeach; unset($list) ?>
	</div>

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
</body>
</html>