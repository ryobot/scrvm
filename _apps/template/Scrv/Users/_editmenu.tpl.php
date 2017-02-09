
		<div class="flex-container w3-center info user_menu">
<?php if($is_only_twitter_login): ?>
			<div class="user_menu_list_block" data-run="Edit"><a href="<?= h($base_path) ?>Users/Edit">プロフィール編集</a></div>
			<div class="user_menu_list_block" data-run="CreateInvite"><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク作成</a></div>
<?php else: ?>
			<div class="user_menu_list_block" data-run="Edit"><a href="<?= h($base_path) ?>Users/Edit">プロフィール編集</a></div>
			<div class="user_menu_list_block" data-run="EditPassword"><a href="<?= h($base_path) ?>Users/EditPassword">パスワード変更</a></div>
			<div class="user_menu_list_block" data-run="CreateInvite"><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク作成</a></div>
			<div class="user_menu_list_block" data-run="ConnectTwitter"><a href="<?= h($base_path) ?>Users/ConnectTwitter">twitter 連携</a></div>
<?php endif; ?>
		</div>
		<script>
		;$(function(){
			var run_name = "<?= h($__run_class_name) ?>";
			$(".user_menu_list_block").each(function(){
				if ( run_name === $(this).attr("data-run") ) {
					$(this).addClass("active");
				}
			});
		});
		</script>
