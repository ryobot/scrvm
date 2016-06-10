<?php
/**
 * ogp.tpl.php
 * @author mgng
 */
?>
<!-- ogp -->
<meta property="og:sitename" content="<?= h($base_title) ?>" />
<meta property="og:title" content="<?= h($review_title) ?>" />
<meta property="og:locale" content="ja_JP" />
<meta property="og:type" content="<?= h($review["body"]) ?>" />
<meta property="og:url" content="<?= h(\lib\Util\Server::getFullHostUrl() . \lib\Util\Server::env("REQUEST_URI")) ?>" />
<meta property="og:image" content="<?= h(\lib\Util\Server::getFullHostUrl() . $album_image_path) ?>" />
