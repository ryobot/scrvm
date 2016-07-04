<?php
/**
 * parts/meta_common.tpl.php
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
<link rel="stylesheet" href="<?= h($base_path) ?>css/scrvm.css?v20160704_1500" />
<link rel="stylesheet" href="<?= h($base_path) ?>css/scrvm_media.css?v20160704_1500" />
<script
	src="https://code.jquery.com/jquery-3.0.0.min.js"
	integrity="sha256-JmvOoLtYsmqlsWxa7mDSLMwa6dZ9rrIdtrrVYRnDRH0="
	crossorigin="anonymous"></script>
<script>
	var BASE_PATH = "<?= h($base_path) ?>";
	$(function(){this.IS_LOGINED = <?= $is_login ? "true" : "false" ?>;});
</script>
<script src="<?= $base_path ?>js/Common.js?v20160624_1600"></script>
