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
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2 id="id_title_posts">Posts (<?= h($pager["total_count"]) ?>)</h2>

<?php if(isset($error_messages) && count($error_messages) > 0): ?>
	<div class="error_message">
<?php		foreach($error_messages as $key => $message): ?>
		<p><?= h($message) ?></p>
<?php		endforeach; unset($key, $message) ?>
	</div>
<?php endif;?>

<?php if($is_login ): ?>
	<div class="form_info">
		<form action="<?= h($base_path) ?>Posts/Add" method="POST">
			<input type="hidden" name="token" value="<?= h($token) ?>" />
			<input type="hidden" name="reply_id" id="id_reply_id" value="" />
			<p><input type="text" name="title" id="id_title" value="<?= isset($post_params["title"]) ? h($post_params["title"]) : "" ?>" placeholder="title" required="required" /></p>
			<p><textarea name="body" id="id_body" placeholder="content" required="required"><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea></p>
			<p class="actions"><input type="submit" value="Save Post" ></p>
		</form>
	</div>
<?php endif;?>

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

	<!-- posts -->
	<div class="lists w100per">
<?php foreach($lists as $list): ?>
		<div class="post">
			<h4><?= h($list["title"]) ?></h4>
			<p class="post_body"><?= linkIt(nl2br(h($list["body"])), false) ?></p>
<?php if(isset($list["reply_id"])): ?>
<!--					<div>
						<p><a class="post_reply_source" href="<?= h($base_path) ?>Posts/View/id/<?= h($list["reply_id"]) ?>&amp;type=json">返信元</a></p>
					</div>-->
<?php endif;?>
<?php if($is_login ): ?>
			<p><span
				class="post_reply"
				data-reply_id="<?= h($list["id"]) ?>"
				data-reply_title="<?= h($list["title"]) ?>"
				data-reply_body="<?= h($list["body"]) ?>"
			>返信</span></p>
<?php endif; ?>
			<p>
				<a href="<?= h($base_path) ?>Users/View/id/<?= h($list["user_id"]) ?>">
					<img class="user_photo_min vtalgmiddle" src="<?= h($base_path) ?><?= isset($list["user_img_file"]) ? "files/attachment/photo/{$list["user_img_file"]}" : "img/user.svg" ?>" alt="<?= h($list["username"]) ?>" />
					<?= isset($list["username"]) ? h($list["username"]) : "(delete user)" ?>
				</a>
				-
				<span class="post_date"><a href="<?= h($base_path) ?>Posts/View/id/<?= h($list["id"]) ?>"><?= h(timeAgoInWords($list["created"])) ?></a></span>
			</p>
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

//	// post_reply_source
//	$(".post_reply_source").each(function(){
//		var $this = $(this);
//		var href = $this.attr("href");
//		$this.on("click.js", function(){
//			$.ajax( href, {
//				method : 'GET',
//				dataType : 'json'
//			})
//			.done(function(json){
//				$this.parent().append(
//					$("<div class='post_reply_source_result' />").append(
//						$("<h5 />").html(json.title),
//						$("<p />").html(postQuote(json.body)),
//						$("<p />").append(
//							"(",
//							$("<a />").attr({href:BASE_URL + "Users/View/id/" + json.user_id}).html(json.username),
//							" - ",
//							$("<span class='post_date' />").html(json.created),
//							")"
//						)
//					)
//				);
//			})
//			.fail(function(e){
//				alert("system error.");
//			})
//			.always(function(){
//			});
//			return false;
//		});
//	});

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
      $('body,html').animate({scrollTop:$("#id_title_posts").offset().top}, 400, 'swing');

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