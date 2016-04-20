<?php
/**
 * Users/Index.tpl.php
 * @author mgng
 */

$_base_url = $base_path . "Users";
$most_prev_link = "{$_base_url}?";
$prev_link = "{$_base_url}?" . hbq(array("page" => $pager["now_page"]-1,));
$next_link = "{$_base_url}?" . hbq(array("page" => $pager["now_page"]+1,));
$most_next_link = "{$_base_url}?" . hbq(array("page" => $pager["max_page"],));
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$_base_url}?" . hbq(array("page" => $nav["page"],)),
	);
}

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Users</title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

	<h2>Users (<?= h($lists_count) ?>)</h2>

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
		<table class="w100per every_other_row_even">
			<tr>
				<th></th>
				<th></th>
				<th></th>
			</tr>
<?php foreach($lists as $list): ?>
			<tr>
				<td class="w80px tacenter vtalgmiddle">
					<a href="<?= h($base_path) ?>Users/View?id=<?= h($list["id"]) ?>"><img class="user_photo" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.png" ?>" alt="<?= h($list["username"]) ?>" /></a>
<?php if($is_login && $login_user_data["id"] === $list["id"]):?>
					<p class="actions"><a href="<?= h($base_path) ?>Users/Edit">Edit</a></p>
<?php endif;?>
				</td>
				<td class="user_menu_list">
					<ul>
						<li><a href="<?= h($base_path) ?>Users/View?id=<?= h($list["id"]) ?>"><?= h($list["username"]) ?></a></li>
<?php if($list["review_count"] > 0): ?>
						<li>Reviews : <?= h($list["review_count"]) ?></li>
<?php endif; ?>
<?php if($is_login && isset($list["sync_point"]) && $list["sync_point"] !== 0):?>
						<li>syncs : <?= h($list["sync_point"]) ?> pt</li>
<?php endif; ?>
<?php if(isset($list["has_invited_user_id"])): ?>
						<li>
							(invited from <a href="<?= h($base_path) ?>Users/View?id=<?= h($list["has_invited_user_id"]) ?>"><?= h($list["has_invited_username"]) ?></a>
							<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($list["has_invited_img_file"]) ? "files/attachment/photo/{$list["has_invited_img_file"]}" : "img/user.png" ?>" alt="<?= h($list["has_invited_username"]) ?>" />)
						</li>
<?php endif; ?>
					</ul>
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