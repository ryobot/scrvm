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
<title><?= h($base_title) ?> :: About</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<div class="contents">

	<h2>About</h2>
	<div class="about_content">
		<h4>音楽鑑賞日記-共有版です。</h4>
		<p>あなたが聴いた音楽を（できれば全部）ここに記録してみてください。</p>
	</div>

	<h3>何か音楽を聴いたら・・・</h3>
	<div class="about_content">
		<p>● Albums &gt; <img src="<?= h($base_path) ?>img/add_album.svg" alt="add album" title="add album" class="img16x16" /> で追加。</p><p class="about_content_sub">* すでにありそうなものは Albums &gt; artist/title で探す。</p>
		<p>● アルバムページの Write a Review でレビューを残す。</p><p class="about_content_sub">* レビューなし（単なる視聴記録）でも、適当なメモでも、ちゃんとした音楽レビューでもOK。</p><p class="about_content_sub">* あとから編集も可能。</p>
		<p>● 好きなアルバム <img src="<?= h($base_path) ?>img/favalbums_on.svg" class="img16x16" />、好きな曲 <img src="<?= h($base_path) ?>img/favtracks_on.svg" class="img16x16" /> をチェック。</p>
	</div>

	<h3>あなたと同じアルバムを聴いた人、同じ曲が好きな人・・・</h3>
	<div class="about_content">
		<p>● Users &gt; ユーザ &gt; <img src="<?= h($base_path) ?>img/sync.svg" class="img16x16" /> で探せます。</p>
	</div>

	<h3>レビューを同時にtwitterにも・・・</h3>
	<div class="about_content">
		<p>● 右上userの「edit」 &gt; twitter連携 &gt; twitterアカウントを登録。</p>
		<p>● Write a Review で「post to twitter」が選択できるようになります。</p>
	</div>

	<h3>友達を招待・・・</h3>
	<div class="about_content">
		<p>右上 user の「edit」 &gt; 招待リンク生成 &gt; メールに貼って友達に送る。</p>
	</div>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>