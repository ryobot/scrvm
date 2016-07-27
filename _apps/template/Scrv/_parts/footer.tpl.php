<?php
/**
 * _parts/footer.tpl.php
 * @author mgng
 */
?>

<div class="link_page_top" id="js-id_page_top"><img src="<?= h($base_path) ?>img/page_top.svg" alt="page top" title="page top" class="img32x32" /></div>

<div class="footer">
	<p class="copyright"><?= date("Y", $now_timestamp) ?> <?= h($base_title) ?></p>
</div>

<script>
;$(function(){
	// pagetop
	var top_btn = $("#js-id_page_top");
	top_btn.hide();
	$(window).scroll(function(){
		if ( $(this).scrollTop() > 100 ) {
			top_btn.fadeIn();
		} else {
			top_btn.fadeOut();
		}
	});
	top_btn.on("click.js", function(){
		$("body,html").animate({
			scrollTop: 0
		}, 500);
		return false;
	});

	if ( ! /(iPhone|iPad|iPod|Android)/.test(navigator.userAgent) ) {
		twemoji.parse(document.body);
	}

});
</script>
