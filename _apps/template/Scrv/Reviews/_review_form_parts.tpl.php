<?php
/**
 * Reviews/_review_form_parts.tpl.php
 * @author mgng
 */
?>

<p id="id_listening_last_group" class="displaynone">
	いつ聴いた？
	<label><input type="radio" name="listening_last" value="today" id="id_listening_last_today" checked="checked">今日</label>
	<label><input type="radio" name="listening_last" value="recently" id="id_listening_last_recently">最近</label>
</p>

<div class="w3-padding w3-center">
	<div id="id_listening_system_group">
		<div class="w3-padding-4">
			<div>situation</div>
			[<strong id="id_selected_listening_system">headphones</strong>]
		</div>
<?php foreach( $situation_list as $list ): ?>
		<input
			type="radio"
			name="listening_system"
			id="id_listening_system_<?= h($list["value"]) ?>"
			class="cl_listening_system"
			value="<?= h($list["value"]) ?>"
<?php		if($list["value"] === "headphones"): ?>
			checked="checked"
<?php		endif; ?>
		/>
		<label for="id_listening_system_<?= h($list["value"]) ?>"><img
			src="<?= h($base_path . $list["path"]) ?>"
			alt="<?= h($list["value"]) ?>"
			title="<?= h($list["value"]) ?>"
		/></label>
<?php endforeach;?>
	</div>

	<p><a href="#" id="id_np_or_ar_area_toggle">レビュー欄を開く</a></p>
	<div class="displaynone" id="id_np_or_ar_area">
		<p>
			<textarea class="w3-input" rows="5" name="body" id="id_body" placeholder="write a review."><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea>
			<div class="notice"><span id="id_review_counter"></span></div>
		</p>
		<!--<p><input type="checkbox" name="published" id="id_published" value="1" <?= $post_params["published"] === 0 ? "" : "checked" ?>><label for="id_published"> published</label> <span id="id_published_notice"></span></p>-->
		<input type="hidden" name="published" id="id_published" value="1" />
<?php if(isset($login_user_data["twitter_user_id"])): ?>
		<p><input type="checkbox" name="send_twitter" id="id_send_twitter" value="1"><label for="id_send_twitter"> Twitterへ投稿する</label></p>
		<p id="id_send_twitter_notice" class="notice displaynone">※Twitterへ投稿する場合、140文字を超えても投稿はできますが一部省略されます。</p>
<?php endif; ?>
	</div>
	<p class="actions mgb10px mgt10px">
		<input type="submit" value="保存する" id="id_form_submit" />
	</p>
</div>

<script>
;$(function(){
	var $np_or_ar_area_toggle = $("#id_np_or_ar_area_toggle");
	$np_or_ar_area_toggle.on("click.js", function(){
		var $this = $(this);
		var $np_or_ar_area = $("#id_np_or_ar_area");
		if ( $np_or_ar_area.is(":visible") ) {
			$this.text("レビュー欄を開く");
			$np_or_ar_area.hide("fast");
		} else {
			$this.text("レビュー欄を閉じる");
			$np_or_ar_area.show("fast");
		}
		return false;
	});
	// レビューが書かれている場合は開く
	if ( $("#id_body").val() !== "" ) {
		$np_or_ar_area_toggle.trigger("click.js");
	}
	$(".cl_listening_system").on("click.js", function(){
		$("#id_selected_listening_system").text($(this).val());
	});
	$("#id_send_twitter").on("click.js", function(){
		var $send_twitter_notice = $("#id_send_twitter_notice");
		if ($(this).prop("checked")) {
			$send_twitter_notice.slideDown("fast");
		} else {
			$send_twitter_notice.slideUp("fast");
		}
	});
});
</script>
