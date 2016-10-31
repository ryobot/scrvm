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

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<div class="contents">

	<h2>About</h2>
	<div class="about_content">
		<h4>音楽鑑賞日記-共有版です。</h4>
		<div style="white-space: pre-line">
			生活に音楽が欠かせない全ての人へ。
			あなたの音楽体験を教えてください。
			今日は何を聴きましたか？ どんな気分で聴きましたか？
			レビューや日記も一緒に、ここに記録してみてください。
		</div>
		<div style="white-space: pre-line">
			もしかしたら、あなたと同じ頃に同じ音楽を聴いている人がいるかもしれません。
			もしかしたら、あなたのレビューを読んだ誰かが同じ音楽を聴きはじめるかもしれません。
			音楽を通して見知らぬ誰かと同調する不思議な感覚を“Syncポイント”として目に見えるようにしてみました。
		</div>
		<div style="white-space: pre-line">
			聴いて、記録して、誰かとSyncする。
			音楽の新しい楽しみ方がここにあります。
		</div>
	</div>

	<h3>how to join</h3>
	<div class="about_content">
		<p>Twitterアカウントでログインできるようになりました！</p>
		<p>ログインするとアカウントごとに個別のユーザページが作られます。</p>
		<p>お問い合わせは <span class="mail">syncreview.info@gmail.com</span> まで。</p>
	</div>

	<h3>何か音楽を聴いたら・・・</h3>
	<div class="about_content">
		<p><a href="<?= h($base_path) ?>Albums"><img src="<?= h($base_path) ?>img/albums.svg" class="img16x16" alt="Albums" /> Albums</a> &raquo; <img src="<?= h($base_path) ?>img/add_album.svg" alt="add album" title="add album" class="img16x16" /> で追加。</p>
		<ul class="about_content_sub">
			<li>すでにありそうなものは Albums &gt; artist/title で探す。</li>
		</ul>
		<p>アルバムページの Add Review でレビューを残す。</p>
		<ul class="about_content_sub">
			<li>レビューなし（単なる視聴記録）でも、適当なメモでも、ちゃんとした音楽レビューでもOK。</li>
			<li>あとから編集も可能。</li>
		</ul>
		<p>好きなアルバム <img src="<?= h($base_path) ?>img/favalbums_on.svg" class="img16x16" />、好きな曲 <img src="<?= h($base_path) ?>img/favtracks_on.svg" class="img16x16" /> をチェック。</p>
	</div>

	<h3>あなたと同じアルバムを聴いた人、同じ曲が好きな人・・・</h3>
	<div class="about_content">
		<p><a href="<?= h($base_path) ?>Users"><img src="<?= h($base_path) ?>img/users.svg" class="img16x16" alt="Users" /> Users</a> &raquo; <img src="<?= h($base_path) ?>img/sync.svg" class="img16x16" /> で探せます。</p>
	</div>

	<h3>レビューを同時にtwitterにも・・・</h3>
	<div class="about_content">
		<p><img src="<?= h($base_path) ?>img/menu.svg" class="img16x16" alt="menu" /> &raquo; <a href="<?= h($base_path) ?>Users/Edit">edit</a> &raquo; <a href="<?= h($base_path) ?>Users/ConnectTwitter">twitter連携</a> &raquo; twitterアカウントを登録。</p>
		<ul class="about_content_sub">
			<li>Add Review で「post to twitter」が選択できるようになります。</li>
		</ul>
	</div>

	<h3>友達を招待・・・</h3>
	<div class="about_content">
		<p><img src="<?= h($base_path) ?>img/menu.svg" class="img16x16" alt="menu" /> &raquo; <a href="<?= h($base_path) ?>Users/Edit">edit</a> &raquo; <a href="<?= h($base_path) ?>Users/CreateInvite">招待リンク生成</a> &raquo; メールに貼って友達に送る。</p>
	</div>

	<h3>改善案など・・・</h3>
	<div class="about_content">
		<p>「使いにくい」「わかりにくい」「こういう機能がほしい」「不具合がある」等あれば、<a href="<?= h($base_path) ?>Posts"><img src="<?= h($base_path) ?>img/posts.svg" alt="Posts" class="img16x16" /> Posts</a> に書き込んでください。</p>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>