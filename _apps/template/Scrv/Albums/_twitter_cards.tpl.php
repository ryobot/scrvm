<?php
/**
 * twitter_cards.tpl.php
 * @author mgng
 */
?>
<!-- twitter cards -->
<meta name="twitter:card" value="summary" />
<meta name="twitter:site" value="@ryobotnotabot" />
<meta name="twitter:title" value="<?= h("$view_title ({$view_year})") ?>" />
<meta name="twitter:description" content="<?= h("$view_title ({$view_year})") ?>" />
<meta name="twitter:image:src" content="<?= h(\lib\Util\Server::getFullHostUrl() . $album_image_path) ?>" />
