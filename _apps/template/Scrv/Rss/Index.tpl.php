<?php
/**
 * Rss/Index.tpl.php
 * @author mgng
 */

echo "<?xml version='1.0' encoding='UTF-8'?>\n";
?>
<rss version='2.0'>
	<channel>
		<title><?= h( $base_title ) ?></title>
		<link><?= h( $http_host ) ?></link>
		<description><?= h( $base_title ) ?> RSS feed</description>

<?php foreach( $reviews as $review ) : ?>
		<item>
			<title><?= h( $review["artist"] ) ?> / <?= h( $review["title"] ) ?></title>
			<link><?= h( $http_host . $base_path . "Reviews/View/id/" . $review["id"] ) ?></link>
			<pubDate><?= h( date( "c", strtotime($review["created"]) ) ) ?></pubDate>
			<description>
				<?= h( $review["body"] === "" ? "(no review)" : $review["body"] ) ?>
				(by <?= h( $review["username"] ) ?>)
			</description>
		</item>
<?php endforeach; ?>

	</channel>
</rss>