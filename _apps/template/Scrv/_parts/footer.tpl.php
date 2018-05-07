<?php
/**
 * _parts/footer.tpl.php
 * @author mgng
 */
?>

<footer class="w3-white w3-row-padding w3-padding-16 w3-center">
	<p>(C) 2016 - <a href="<?= h($base_path) ?>"><?= h($base_title) ?></a></p>
</footer>

<!-- modal -->
<div id="id_modal_wait" class="w3-modal">
	<div class="w3-container w3-center">
		<div id="id_modal_wait_text" class="w3-display-middle w3-xlarge">
			<p><img src="<?= h($base_path) ?>img/spinner.svg" class="width_120px w3-spin" alt="loading" /></p>
		</div>
	</div>
</div>

<script>

;$(function(){
//	if ( ! /(iPhone|iPad|iPod|Android)/.test(navigator.userAgent) ) {
//		twemoji.parse(document.body);
//	}
});
</script>
