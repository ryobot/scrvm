<?php
/**
 * _parts/header_menu.tpl.php
 * @author mgng
 */

// event svg list
$_event_svg_list = array();
foreach(glob(__DIR__ . "/../../../../img/event/*.svg") as $path) {
	$_event_svg_list[] = "{$base_path}img/event/".basename($path);
}

?>

<div id="myOverlay" class="w3-overlay" onclick="w3_close()" style="z-index:19"></div>

<!-- Sidenav -->
<nav class="w3-sidenav w3-card-2 w3-top w3-large w3-animate-left" style="display:none;z-index:20;width:50%;" id="mySidenav">
  <a href="javascript:void(0)" onclick="w3_close()" class="w3-closenav w3-grey"><i class="fas fa-times"></i> Close</a>
<?php if( !$is_login ):?>
	<a href="<?= h($base_path) ?>Auth"><i class="fas fa-sign-in-alt"></i> Login</a>
<?php else: ?>
	<a href="<?= h($base_path) ?>Users/View/id/<?= h($login_user_data["id"]) ?>"><i class="fas fa-user"></i> Profile</a>
	<a href="<?= h($base_path) ?>Users/Edit"><i class="fas fa-edit"></i> Edit</a>
	<a id="id_logout" href="javascript:;"><i class="fas fa-sign-out-alt"></i> Logout</a>
	<form id="id_form_logout" action="<?= h($base_path) ?>Auth/Logout" method="POST"></form>
<?php endif; ?>
	<a href="<?= h($base_path) ?>About"><i class="fas fa-question-circle"></i> About</a>
<?php if( $login_user_data["role"] === "admin" ):?>
	<a href="<?= h($base_path) ?>Admin">*admin*</a>
<?php endif; ?>
</nav>

<!-- header -->
<div class="w3-top w3-white header" style="z-index: 10">

	<!-- top -->
  <div class="w3-large w3-padding-xlarge">
    <div class="w3-opennav w3-left w3-text-grey w3-hover-text-grey" onclick="w3_open()"><i class="fas fa-bars"></i></div>
		<div class="w3-right">
<?php if( $is_login ):?>
			<a href="<?= h($base_path) ?>Users/View/id/<?= h($login_user_data["id"]) ?>"><img class="w3-image w3-round width_30px" src="<?= h($base_path) ?><?= isset($login_user_data["img_file"]) ? "files/attachment/photo/{$login_user_data["img_file"]}" : "img/user.svg" ?>" /></a>
<?php endif; ?>
		</div>
		<div class="w3-center">
			<a href="<?= h($base_path) ?>">
				<img class="width_20px" id="id_header_top_icon" style="vertical-align: middle;" src="<?= h($base_path) ?>img/headphone_icon_S.png" alt="<?= h($base_title) ?>" />
				<?= h($base_title) ?>
			</a>
		</div>
  </div>

	<!-- top menu -->
	<div class="w3-center flex-container top_menu" id="id_top_menu">
		<div class="menu_block" data-menu="Reviews"><a href="<?= h($base_path) ?>">
			<div class="w3-large"><i class="fas fa-edit"></i></div>
			<div class="text">Reviews</div>
		</a></div>
		<div class="menu_block" data-menu="Albums"><a href="<?= h($base_path) ?>Albums">
			<div class="w3-large"><i class="fas fa-search"></i></div>
			<div class="text">Albums</div>
		</a></div>
	<?php if( $is_login ):?>
		<div class="menu_block" data-menu="Activity"><a href="<?= h($base_path) ?>Activity">
			<div class="w3-large"><i class="fas fa-bell"></i></div>
			<div class="text">Activity</div>
		</a></div>
	<?php endif; ?>
		<div class="menu_block" data-menu="Users"><a href="<?= h($base_path) ?>Users">
			<div class="w3-large"><i class="fas fa-users"></i></div>
			<div class="text">Users</div>
		</a></div>
	<?php if( $is_login ):?>
		<div class="menu_block" data-menu="Posts"><a href="<?= h($base_path) ?>Posts">
			<div class="w3-large"><i class="fas fa-comment-dots"></i></div>
			<div class="text">Posts</div>
		</a></div>
	<?php endif; ?>
		<div class="menu_block" data-menu="Logs"><a href="<?= h($base_path) ?>Logs">
			<div class="w3-large"><i class="fas fa-th"></i></div>
			<div class="text">Logs</div>
		</a></div>
	</div>
</div>

<script>

	// menu 関連
	function w3_open() {
		$("#mySidenav").css({"display":"block"});
		$("#myOverlay").css({"display":"block"});
	}
	function w3_close() {
		$("#mySidenav").css({"display":"none"});
		$("#myOverlay").css({"display":"none"});
	}
	Mousetrap.bind("esc", function(){
		w3_close();
	});

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

		var event_date_path = new function(){
			// event date
			var event_img_path_list = <?= json_encode($_event_svg_list) ?>;
			var now = new Date();
			var m = (now.getMonth() + 1).toString(), d = now.getDate().toString();
			if (m.length === 1) {m = "0" + m;}
			if (d.length === 1) {d = "0" + d;}
			var img_path = ["<?= $base_path ?>img/event/", m, d, ".svg"].join("");
			if ( $.inArray(img_path, event_img_path_list) !== -1 ) {
				$("#id_header_top_icon").attr({
					"src" : img_path,
					"style" : ""
				});
			}
		};

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

