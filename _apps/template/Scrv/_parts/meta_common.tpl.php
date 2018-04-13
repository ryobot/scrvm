<?php
/**
 * parts/meta_common_2.tpl.php
 * @author mgng
 */
?>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />
<meta name="format-detection" content="telephone=no" />
<meta name="keywords" content="syncreview,scrv,music,diary" />
<?php if ( isset($_description) ): ?>
<meta name="description" content="<?= h($_description) ?>" />
<?php endif;?>
<link href="<?= h($base_path) ?>favicon.ico" type="image/x-icon" rel="icon" />
<link href="<?= h($base_path) ?>favicon.ico" type="image/x-icon" rel="shortcut icon" />
<link rel="alternate" type="application/rss+xml" title="feed" href="<?= h($base_path) ?>Rss" />
<link rel="stylesheet" href="<?= h($base_path) ?>css/w3.css?v20180413" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/mousetrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.1.4/js.cookie.min.js"></script>
<script src="https://twemoji.maxcdn.com/2/twemoji.min.js?2.2.2"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>
<script>
	var BASE_PATH = "<?= h($base_path) ?>";
	$(function(){this.IS_LOGINED = <?= $is_login ? "true" : "false" ?>;});
</script>
<script src="<?= $base_path ?>js/Common.js?v20160708_1300"></script>
