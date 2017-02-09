<?php
/**
 * _parts/footer.tpl.php
 * @author mgng
 */
?>

<footer class="w3-white w3-row-padding w3-padding-16 w3-center">
	<p>(C) 2016 - <a href="<?= h($base_path) ?>"><?= h($base_title) ?></a></p>
</footer>

<script>
function w3_open() {
	$("#mySidenav").css({"display":"block"});
}
function w3_close() {
	$("#mySidenav").css({"display":"none"});
}

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
