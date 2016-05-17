<?php
/**
 * twitter_cards.tpl.php
 * @author mgng
 */
?>
<!-- twitter cards -->
<meta name="twitter:card" value="summary_large_image" />
<meta name="twitter:site" value="@ryobotnotabot" />
<meta name="twitter:title" value="<?= h($review_title) ?>" />
<meta name="twitter:description" content="<?= h($review["body"]) ?>" />
<meta name="twitter:image:src" content="<?= h(\lib\Util\Server::getFullHostUrl() . $album_image_path) ?>" />
