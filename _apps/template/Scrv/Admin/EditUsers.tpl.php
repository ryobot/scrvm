<?php
/**
 * Admin/Index.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Admin::EditUsers - <?= h($base_title) ?></title>
</head>
<body><div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<div class="contents">
		<h2>Admin::EditUsers</h2>
	</div>

	<div class="contents">
<?php foreach($users as $user): ?>
		<pre><?= print_r($user, 1) ?></pre>
<?php endforeach; ?>
	</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div></body>
</html>