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
<title>Admin - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center w3-padding">
		<h2 class="w3-xlarge">Admin</h2>
	</div>

	<div class="w3-padding info">
		<ul>
			<li><a href="<?= h($base_path) ?>Admin/EditUsers">EditUsers</a></li>
		</ul>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</body>
</html>