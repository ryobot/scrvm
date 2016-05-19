<?php
/**
 * Users/Index.tpl.php
 * @author mgng
 */

$_base_url = $base_path . "Users";
$most_prev_link = "{$_base_url}?" . hbq(array(
	"page"  => "1",
	"sort"  => $sort,
	"order" => $order,
));
$prev_link = "{$_base_url}?" . hbq(array(
	"page"  => $pager["now_page"]-1,
	"sort"  => $sort,
	"order" => $order,
));
$next_link = "{$_base_url}?" . hbq(array(
	"page"  => $pager["now_page"]+1,
	"sort"  => $sort,
	"order" => $order,
));
$most_next_link = "{$_base_url}?" . hbq(array(
	"page"  => $pager["max_page"],
	"sort"  => $sort,
	"order" => $order,
));

$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$_base_url}?" . hbq(array(
			"page" => $nav["page"],
			"sort"   => $sort,
			"order"  => $order,
		)),
	);
}

// ソート用リンク
$order_type = $order === "asc" ? "desc" : "asc";
$sort_links = array(
	"username" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"sort"   => "username",
			"order"  => $order_type,
		)),
		"text" => $sort === "username" ? "[Username]" : "Username",
	),
	"review_count" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"sort"   => "review_count",
			"order"  => $order_type,
		)),
		"text" => $sort === "review_count" ? "[Reviews]" : "Reviews",
	),
	"sync_point" => array(
		"link" => "{$_base_url}?" . hbq(array(
			"sort"   => "sync_point",
			"order"  => $order_type,
		)),
		"text" => $sort === "sync_point" ? "[Syncs]" : "Syncs",
	),
);

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
<div class="contents">

	<h2>Users (<?= h($lists_count) ?>)</h2>

<?php if(count($lists) > 0): ?>

	<!-- pager -->
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

	<!-- user lists -->
	<div class="lists">

		<!-- sort -->
		<div class="displaytable w100per">
			<div class="displaytablecell w80px"></div>
			<div class="displaytablecell">
				<a href="<?= h($sort_links["username"]["link"]) ?>"><?= h($sort_links["username"]["text"]) ?></a>
				/
				<a href="<?= h($sort_links["review_count"]["link"]) ?>"><?= h($sort_links["review_count"]["text"]) ?></a>
<?php if($is_login):?>
				/
				<a href="<?= h($sort_links["sync_point"]["link"]) ?>"><?= h($sort_links["sync_point"]["text"]) ?></a>
<?php endif; ?>
			</div>
		</div>

		<!-- lists -->
<?php foreach($lists as $list): ?>
		<div class="w100per user_profile mgb5px">
			<div>
				<a href="<?= h($_base_url) ?>/View?id=<?= h($list["id"]) ?>"><?= h($list["username"]) ?></a>
			</div>
			<div class="displaytable w100per">
				<div class="displaytable w80px">
					<a href="<?= h($_base_url) ?>/View?id=<?= h($list["id"]) ?>"><img class="user_photo" src="<?= h($base_path) ?><?= isset($list["img_file"]) ? "files/attachment/photo/{$list["img_file"]}" : "img/user.png" ?>" alt="<?= h($list["username"]) ?>" /></a>
				</div>
				<div class="displaytablecell w100per vtalgtop">
					<div class="displaytable">
<?php if($list["review_count"] > 0): ?>
						<div class="displaytablecell">
							<a class="reviews" href="<?= h($_base_url) ?>/View?id=<?= h($list["id"]) ?>">
								<img src="<?= h($base_path) ?>img/reviews.svg" class="img16x16" alt="reviews" />
								<?= h($list["review_count"]) ?>
							</a>
							&nbsp;&nbsp;
						</div>
<?php endif; ?>
<?php if($is_login && isset($list["sync_point"]) && $list["sync_point"] !== 0):?>
						<div class="displaytablecell">
							<a class="syncs" href="<?= h($_base_url) ?>/Syncs?id=<?= h($list["id"]) ?>">
								<img src="<?= h($base_path) ?>img/sync.svg" class="img16x16" alt="syncs" />
								<?= h($list["sync_point"]) ?> pt
							</a>
						</div>
<?php endif; ?>
					</div>
<?php if(isset($list["has_invited_user_id"])): ?>
					<div>
						&laquo; invited from <a href="<?= h($_base_url) ?>/View?id=<?= h($list["has_invited_user_id"]) ?>"><?= h($list["has_invited_username"]) ?></a>
						<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($list["has_invited_img_file"]) ? "files/attachment/photo/{$list["has_invited_img_file"]}" : "img/user.png" ?>" alt="<?= h($list["has_invited_username"]) ?>" />
					</div>
<?php endif; ?>
				</div>
			</div>
		</div>
<?php endforeach; unset($list) ?>


	</div>

	<!-- pager -->
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

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>