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
<div id="id_listening_system_group" class="mgb10px">
	<div><label>select : <span id="id_selected_listening_system">headphones</span></label></div>
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

<?php if(isset($login_user_data["twitter_user_id"])): ?>
<p><label><input type="checkbox" name="send_twitter" id="id_send_twitter" value="1"> post to twitter</label></p>
<p>※twitterへ投稿する場合、140文字を超えても投稿はできますが一部省略されます。</p>
<?php endif; ?>
<p><textarea name="body" id="id_body" cols="30" rows="10" placeholder="write a review."><?= isset($post_params["body"]) ? h($post_params["body"]) : "" ?></textarea></p>
<p class="actions">
	<input type="submit" value="Save Review" />
	<span id="id_review_counter"></span>
</p>

<script>
;$(function(){
	$(".cl_listening_system").on("click.js", function(){
		$("#id_selected_listening_system").text($(this).val());
	});
});
</script>
