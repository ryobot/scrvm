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
<title><?= h($base_title) ?> :: Admin</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<div class="contents">

	<h2>Admin</h2>
	<ul>
		<li><a href="<?= h($base_path) ?>Admin/InitPassword">パスワード初期化</a></li>
		<li><a href="<?= h($base_path) ?>Admin/UserEdit">会員編集</a></li>
	</ul>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>