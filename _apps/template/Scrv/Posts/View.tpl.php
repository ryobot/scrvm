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
	<title>Posts::id::<?= h($id) ?> - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Posts::id::<?= h($id) ?></h2>
	</div>

	<div class="w3-form w3-card-2 w3-white w3-margin-bottom">
		<h5><a href="<?= h($base_path) ?>Posts/View/id/<?= h($id) ?>"><?= h($post["title"]) ?></a></h5>
		<p class="post_body"><?= linkIt(nl2br(h($post["body"])), false) ?></p>
		<p class="notice">
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($post["user_id"]) ?>">
				<img class="width_20px" src="<?= h($base_path) ?><?= isset($post["user_img_file"]) ? "files/attachment/photo/{$post["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($post["username"]) ?>" />
				<?= isset($post["username"]) ? h($post["username"]) : "(delete user)" ?>
			</a>
			-
			<?= h(timeAgoInWords($post["created"])) ?></a>
		</p>
	</div>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

<script>
	;$(function(){
		function postQuote(body){
			var arr = body.replace(/\r\n?/g, "\n").split("\n");
			var ret = [];
			for(var i=0,len=arr.length;i<len;i++) {
				ret.push(/^\s*&gt;/.test(arr[i])
					? "<span class='post_quote'>"+arr[i]+"</span>"
					: arr[i]
				);
			}
			return ret.join("\n");
		}

		// post_body
		$(".post_body").each(function(){
			$(this).html(postQuote($(this).html()));
		});
	});
</script>

</body>
</html>