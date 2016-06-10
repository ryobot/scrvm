<?php
/**
 * ogp.tpl.php
 * @author mgng
 */
?>
<!-- ogp -->
<meta property="og:sitename" content="<?= h($base_title) ?>" />
<meta property="og:title" content="<?= h("$view_title ({$view_year})") ?>" />
<meta property="og:locale" content="ja_JP" />
<meta property="og:type" content="<?= h("$view_title ({$view_year})") ?>" />
<meta property="og:url" content="<?= h(\lib\Util\Server::getFullHostUrl() . \lib\Util\Server::env("REQUEST_URI")) ?>" />
<meta property="og:image" content="<?= h(\lib\Util\Server::getFullHostUrl() . $album_image_path) ?>" />
