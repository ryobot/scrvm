
		<div class="displaytable w100per user_edit_menu">
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/Edit">プロフィール編集</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/EditPassword">パスワード変更</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク作成</a></div>
			<div class="displaytablecell"><a href="<?= h($base_path) ?>Users/ConnectTwitter">twitter 連携</a></div>
		</div>
		<script>
		;$(function(){
			var pathname = location.pathname;
			$(".user_edit_menu > div > a").each(function(){
				if ( pathname === $(this).attr("href") ) {
					$(this).parent().addClass("active");
				}
			});
		});
		</script>
