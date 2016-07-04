<?php
/**
 * Reviews/_radio_parts.tpl.php
 * @author mgng
 */
?>

<p id="id_listening_last_group" class="displaynone">
	いつ聴いた？
	<label><input type="radio" name="listening_last" value="today" id="id_listening_last_today" checked="checked">今日</label>
	<label><input type="radio" name="listening_last" value="recently" id="id_listening_last_recently">最近</label>
</p>
<div id="id_listening_system_group" class="mgb10px">
<?php foreach( $situation_list as $list ): ?>
	<input
		type="radio"
		name="listening_system"
		id="id_listening_system_<?= h($list["value"]) ?>"
		value="<?= h($list["value"]) ?>"
<?php		if($list["value"] === "other"): ?>
		checked="checked"
<?php		endif; ?>
	/>
	<label for="id_listening_system_<?= h($list["value"]) ?>"><img src="<?= h($base_path . $list["path"]) ?>" alt="<?= h($list["value"]) ?>" /></label>
<?php endforeach;?>
</div>
