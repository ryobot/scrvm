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

<div class="w3-padding-4 w3-center">

	<!-- situation -->
	<div id="id_listening_system_group" class="w3-padding-4 w3-light-gray w3-round">
		<div class="w3-padding">
			<div>situation</div>
			<div class="w3-text-dark-gray">[<strong id="id_selected_listening_system">headphones</strong>]</div>
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

	<div class="displaynone_ w3-container w3-margin-top w3-padding-0" id="id_np_or_ar_area">
		<div>
			<textarea class="w3-input w3-round w3-border" rows="6" name="body" id="id_body" placeholder="write a review."><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea>
			<div class="notice"><span id="id_review_counter"></span></div>
		</div>
		<!--<p><input type="checkbox" name="published" id="id_published" value="1" <?= $post_params["published"] === 0 ? "" : "checked" ?>><label for="id_published"> published</label> <span id="id_published_notice"></span></p>-->
		<input type="hidden" name="published" id="id_published" value="1" />
	</div>

<?php if(isset($login_user_data["twitter_user_id"])): ?>
	<div class="w3-padding">
		<p><label><input class="w3-check" type="checkbox" name="send_twitter" id="id_send_twitter" value="1" /> Twitterへ投稿する</label></p>
		<p id="id_send_twitter_notice" class="notice displaynone">※Twitterへ投稿する場合、140文字を超えても投稿はできますが一部省略されます。</p>
	</div>
<?php endif; ?>

	<p class="actions">
		<input
			class="w3-btn w3-round w3-teal"
			type="submit"
			value="<?= $__run_class_name === "Edit" ? "保存する" : "投稿する" ?>"
			id="id_form_submit"
		/>
	</p>
</div>

<script>
;$(function(){
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

	$("#id_form_submit").on("click", function(){
		$("#id_modal_wait").show();
		return true;
	});
});
</script>
