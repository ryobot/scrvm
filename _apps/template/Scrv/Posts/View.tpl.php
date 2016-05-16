<?php
/**
 * Posts/View.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Posts :: id:<?= h($id) ?></title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Posts (id:<?= h($id) ?>)</h2>

	<div class="lists">
		<table class="w100per every_other_row_odd">
			<tr>
				<td>
					<h4><?= h($post["title"]) ?></h4>
					<p><?= linkIt(nl2br(h($post["body"]))) ?></p>
					<p><a href="<?= h($base_path) ?>Users/View?id=<?= h($post["user_id"]) ?>"><?= isset($post["username"]) ? h($post["username"]) : "(delete user)" ?></a> - <span class="post_date"><?= h( date("Y年m月d日 H時i分",strtotime($post["created"]))) ?></span></p>
				</td>
			</tr>
		</table>
	</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>