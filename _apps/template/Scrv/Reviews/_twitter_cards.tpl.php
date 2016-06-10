<?php
/**
 * twitter_cards.tpl.php
 * @author mgng
 */
?>
<!-- twitter cards -->
<meta name="twitter:card" value="summary" />
<meta name="twitter:site" value="@ryobotnotabot" />
<meta name="twitter:title" value="<?= h($review_title) ?>" />
<meta name="twitter:description" content="<?= h($review["body"]) ?>" />
<meta name="twitter:image:src" content="<?= h(\lib\Util\Server::getFullHostUrl() . $album_image_path) ?>" />
<meta name="twitter:url" content="<?= h(\lib\Util\Server::getFullHostUrl() . \lib\Util\Server::env("REQUEST_URI")) ?>" />
