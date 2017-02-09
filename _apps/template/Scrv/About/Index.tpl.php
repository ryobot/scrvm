<?php
/**
 * About/Index.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>About - <?= h($base_title) ?></title>
</head>
<body>

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<!-- main contents  -->
<div class="w3-main w3-content w3-padding-4 main">

	<div class="w3-center w3-padding">
		<h2 class="w3-xlarge">About</h2>
	</div>

	<div class="w3-padding info">
		<h4>音楽鑑賞日記-共有版です。</h4>
		<p>
			生活に音楽が欠かせない全ての人へ。
			あなたの音楽体験を教えてください。
			今日は何を聴きましたか？ どんな気分で聴きましたか？
			レビューや日記も一緒に、ここに記録してみてください。
		</p>
		<p>
			もしかしたら、あなたと同じ頃に同じ音楽を聴いている人がいるかもしれません。
			もしかしたら、あなたのレビューを読んだ誰かが同じ音楽を聴きはじめるかもしれません。
			音楽を通して見知らぬ誰かと同調する不思議な感覚を“Syncポイント”として目に見えるようにしてみました。
		</p>
		<p>
			聴いて、記録して、誰かとSyncする。
			音楽の新しい楽しみ方がここにあります。
		</p>
	</div>

	<h3 class="w3-center w3-padding">how to join</h3>
	<div class="w3-padding info">
		<p>
			Twitterアカウントでログインできるようになりました！
			ログインするとアカウントごとに個別のユーザページが作られます。
		</p>
		<p>お問い合わせは <span class="mail">syncreview.info@gmail.com</span> まで。</p>
	</div>

	<h3 class="w3-center w3-padding">何か音楽を聴いたら・・・</h3>
	<div class="w3-padding info">
		<p>
			<a href="<?= h($base_path) ?>Albums">
				<img class="width_16px" src="<?= h($base_path) ?>img/albums.svg" class="img16x16" />
				Albums
			</a> から検索してみましょう。</p>
		<ul>
			<li>見つからなければ「新しくアルバムを追加する」 から追加も可能。</li>
		</ul>
		<p>その後、アルバムページの「レビューを書く」でレビューを残します。</p>
		<ul>
			<li>レビューなし（単なる視聴記録）でも、適当なメモでも、ちゃんとした音楽レビューでもOK。</li>
			<li>あとから編集も可能です。</li>
			<li>
				好きなアルバム <img class="width_16px" src="<?= h($base_path) ?>img/favalbums_on.svg" />、
				好きな曲 <img class="width_16px" src="<?= h($base_path) ?>img/favtracks_on.svg" /> をチェック。
			</li>
		</ul>
	</div>

	<h3 class="w3-center w3-padding">あなたと同じアルバムを聴いた人、同じ曲が好きな人・・・</h3>
	<div class="w3-padding info">
		<p>
			<a href="<?= h($base_path) ?>Users">
				<img src="<?= h($base_path) ?>img/users.svg" class="width_16px" alt="Users" /> Users</a>
				&raquo; <img src="<?= h($base_path) ?>img/sync.svg" class="width_16px" /> から探せます。
		</p>
	</div>

	<h3 class="w3-center w3-padding">レビューを同時にtwitterにも・・・</h3>
	<div class="w3-padding info">
		<p>
			☰ &raquo;
			<a href="<?= h($base_path) ?>Users/Edit">edit</a> &raquo;
			<a href="<?= h($base_path) ?>Users/ConnectTwitter">twitter連携</a> &raquo;
			twitterアカウントを登録。Add Review で「Twitterへ投稿する」が選択できるようになります。
		</p>
		<ul>
			<li>twitterアカウントでログインしている方は特に設定は必要ありません。</li>
		</ul>
	</div>

	<h3 class="w3-center w3-padding">友達を招待・・・</h3>
	<div class="w3-padding info">
		<p>☰ &raquo; <a href="<?= h($base_path) ?>Users/Edit">edit</a> &raquo; <a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク生成</a> &raquo; メールに貼って友達に送る。</p>
	</div>

	<h3></h3>
	<h3 class="w3-center w3-padding">改善案など・・・</h3>
	<div class="w3-padding info">
		<p>「使いにくい」「わかりにくい」「こういう機能がほしい」「不具合がある」等あれば、<a href="<?= h($base_path) ?>Posts"><img src="<?= h($base_path) ?>img/posts.svg" alt="Posts" class="width_16px" /> Posts</a> に書き込んでください。</p>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</body>
</html>