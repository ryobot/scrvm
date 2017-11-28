<?php
/**
 * _parts/header_menu.tpl.php
 * @author mgng
 */
?>

<!-- Sidenav -->
<nav class="w3-sidenav w3-card-2 w3-top w3-large w3-animate-left" style="display:none;z-index:20;width:40%;min-width:300px" id="mySidenav">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-closenav w3-grey">Close</a>
<?php if( !$is_login ):?>
	<a href="<?= h($base_path) ?>Auth">Login</a>
<?php else: ?>
<?php if( $login_user_data["role"] === "admin" ):?>
	<a href="<?= h($base_path) ?>Admin">*admin*</a>
<?php endif; ?>
	<a href="<?= h($base_path) ?>Users/View/id/<?= h($login_user_data["id"]) ?>">Profile</a>
	<a href="<?= h($base_path) ?>Users/Edit">Edit</a>
	<a id="id_logout" href="javascript:;">Logout</a>
	<form id="id_form_logout" action="<?= h($base_path) ?>Auth/Logout" method="POST"></form>
<?php endif; ?>
	<a href="<?= h($base_path) ?>About">About</a>
</nav>


<!-- header -->
<div class="w3-top w3-white header" style="z-index: 10">

	<!-- top -->
  <div class="w3-large w3-padding-xlarge">
    <div class="w3-opennav w3-left w3-hover-text-grey" onclick="w3_open()">☰</div>
		<div class="w3-right">
<?php if( $is_login ):?>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($login_user_data["id"]) ?>"><img class="w3-image w3-round width_30px" src="<?= h($base_path) ?><?= isset($login_user_data["img_file"]) ? "files/attachment/photo/{$login_user_data["img_file"]}" : "img/user.svg" ?>" /></a>
<?php endif; ?>
		</div>
		<div class="w3-center">
			<a href="<?= h($base_path) ?>">
				<img class="width_20px" style="vertical-align: middle;" src="<?= h($base_path) ?>img/headphone_icon_S.png" alt="<?= h($base_title) ?>" />
				<?= h($base_title) ?>
			</a>
		</div>
  </div>

	<!-- top menu -->
	<div class="w3-center flex-container top_menu" id="id_top_menu">
		<div class="menu_block" data-menu="Reviews"><a href="<?= h($base_path) ?>">
			<div><img src="<?= h($base_path) ?>img/reviews.svg" alt="Reviews" /></div>
			<div class="text">Reviews</div>
		</a></div>
		<div class="menu_block" data-menu="Albums"><a href="<?= h($base_path) ?>Albums">
			<div><img src="<?= h($base_path) ?>img/albums.svg" alt="Albums" /></div>
			<div class="text">Albums</div>
		</a></div>
	<?php if( $is_login ):?>
		<div class="menu_block" data-menu="Activity"><a href="<?= h($base_path) ?>Activity">
			<div><img src="<?= h($base_path) ?>img/activity.svg" alt="Activity" /></div>
			<div class="text">Activity</div>
		</a></div>
	<?php endif; ?>
		<div class="menu_block" data-menu="Users"><a href="<?= h($base_path) ?>Users">
			<div><img src="<?= h($base_path) ?>img/users.svg" alt="Users" /></div>
			<div class="text">Users</div>
		</a></div>
	<?php if( $is_login ):?>
		<div class="menu_block" data-menu="Posts"><a href="<?= h($base_path) ?>Posts">
				<div><img src="<?= h($base_path) ?>img/posts.svg" alt="Posts" /></div>
				<div class="text">Posts</div>
		</a></div>
	<?php endif; ?>
		<div class="menu_block" data-menu="Logs"><a href="<?= h($base_path) ?>Logs">
			<div><img src="<?= h($base_path) ?>img/logs.svg" alt="Logs" /></div>
			<div class="text">Logs</div>
		</a></div>
	</div>
</div>
<script>
	;$(function(){
		var action_name = "<?= h($__action_name) ?>";
		$("#id_top_menu .menu_block").each(function(){
			var $this = $(this);
			if ( action_name === $this.attr("data-menu") ) {
				$this.addClass("active");
			} else{
				$this.addClass("w3-grayscale-max w3-opacity-min");
			}
		});

<?php if ($is_login): ?>
			$.ajax(BASE_PATH + "Activity/Counter", {
				method: "GET",
				dataType: "json",
				data: {}
			}).done(function(json){
				var options = {expires: 7, path: BASE_PATH};
				var key = "counter";
				var counter = Cookies.get(key) || false;
				if (counter === false) {
					Cookies.set(key, json, options);
				} else {
					// TODO 現在のcounter と比較して差分があるところをnew表示
					Cookies.set(key, json, options);
				}
			}).fail(function(e){
			}).always(function(){
			});
<?php endif; ?>

	});
</script>

