<?php
/**
 * Posts/Index.tpl.php
 * @author mgng
 */

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Posts - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center">
		<h2 class="w3-xlarge">Posts (<?= h($pager["total_count"]) ?>)</h2>
	</div>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="w3-padding w3-center w3-red">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

<?php if($is_login ): ?>
	<div class="w3-form w3-card-2 w3-white w3-margin-bottom">
		<form action="<?= h($base_path) ?>Posts/Add" method="POST" autocomplete="off">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<input type="hidden" name="reply_id" id="id_reply_id" value="" />
			<p><input class="w3-input w3-border" type="text" name="title" id="id_title" value="<?= isset($post_params["title"]) ? h($post_params["title"]) : "" ?>" placeholder="title" required="required" /></p>
			<p><textarea class="w3-input w3-border" rows="4" name="body" id="id_body" placeholder="content" required="required"><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea></p>
			<p><input type="submit" class="w3-btn w3-round" value="Save Post" ></p>
		</form>
	</div>
<?php endif;?>

<?php if(count($lists) > 0): ?>

<?php if(count($pager["nav_list"])>0): ?>
	<!-- pager -->
	<div class="w3-center w3-padding-8">
		<ul class="w3-pagination">
<?php if($pager["prev"]): ?>
			<li><a class="w3-hover-black" href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a class="<?= $nav["active"] ? "w3-black" : "w3-hover-black" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a class="w3-hover-black" href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>
<?php endif; ?>

	<!-- posts -->
	<div class="">
<?php foreach($lists as $list): ?>
		<div class="w3-form w3-card-2 w3-white w3-margin-bottom">
			<h5><a href="<?= h($base_path) ?>Posts/View/id/<?= h($list["id"]) ?>"><?= h($list["title"]) ?></a></h5>
			<p class="post_body"><?= linkIt(nl2br(h($list["body"])), false) ?></p>
			<p class="notice">
				<a href="<?= h($base_path) ?>Users/View/id/<?= h($list["user_id"]) ?>">
					<img class="width_20px" src="<?= h($base_path) ?><?= isset($list["user_img_file"]) ? "files/attachment/photo/{$list["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["username"]) ?>" />
					<?= isset($list["username"]) ? h($list["username"]) : "(delete user)" ?>
				</a>
				-
				<?= h(timeAgoInWords($list["created"])) ?></a>
			</p>
<?php if($is_login ): ?>
			<p><button
				class="post_reply w3-btn w3-green w3-round"
				data-reply_id="<?= h($list["id"]) ?>"
				data-reply_title="<?= h($list["title"]) ?>"
				data-reply_body="<?= h($list["body"]) ?>"
			>返信</button></p>
<?php endif; ?>
		</div>
<?php endforeach; unset($list) ?>
	</div>

<?php if(count($pager["nav_list"])>0): ?>
	<!-- pager -->
	<div class="w3-center w3-padding-8">
		<ul class="w3-pagination">
<?php if($pager["prev"]): ?>
			<li><a class="w3-hover-black" href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a class="<?= $nav["active"] ? "w3-black" : "w3-hover-black" ?>" href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a class="w3-hover-black" href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>
<?php endif; ?>

<?php endif; ?>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>


<script>
;$(function(){

	var BASE_URL = "<?= h($base_path) ?>";

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

	// post_reply
	$(".post_reply").each(function(){
		$(this).on("click.js", function(){
			var replyBody = function(body){
				var quote = "> ";
				body = $.trim(body);
				body = body.replace(/[\r\n]/g, "\n");
				return quote + body.split("\n").join("\n"+quote) + "\n\n";
			};

			var $post = $(this);
			var title = $post.attr("data-reply_title");
			var body = $post.attr("data-reply_body");
			var reply_id = $post.attr("data-reply_id");

			// scroll
      $('body,html').animate({scrollTop:$("body").offset().top}, 400, 'swing');

			// set
			$("#id_reply_id").val(reply_id);
			$("#id_title").val("Re: " + title);
			$("#id_body").val(replyBody(body)).focus();
		});
	});

});
</script>

</body>
</html>