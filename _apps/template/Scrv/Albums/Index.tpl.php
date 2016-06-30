<?php
/**
 * Albums/Index.tpl.php
 * @author mgng
 */

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title>Albums - <?= h($base_title) ?></title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>

<h2>
	Albums (<span id="id_total_count"><?= h($pager["total_count"]) ?></span>)
	<img id="id_loading" src="<?= h($base_path) ?>img/loading.svg" class="img16x16 loading displaynone" alt="now loading" />
</h2>

<!-- search tabs -->
<div class="search_tab">
	<form id="id_form_Albums_ArtistFilter" action="<?= h($base_path) ?>Albums" method="GET" autocomplete="off">

		<!-- search type and add album tabs -->
		<div class="displaytable w100per tacenter">
<?php if( $is_login ): ?>
			<div class="displaytablecell notab"><a href="<?= h($base_path) ?>Albums/Add" class="add_album"><img src="<?= h($base_path) ?>img/add_album.svg" alt="add album" title="add album" class="img24x24" /></a></div>
<?php endif; ?>
			<div class="displaytablecell tab active" id="id_stype_search" data-stype="search"><img src="<?= h($base_path) ?>img/search.svg" alt="search" title="search" class="img24x24" /></div>
			<div class="displaytablecell tab" id="id_stype_index" data-stype="index"><img src="<?= h($base_path) ?>img/index.svg" alt="index" title="index" class="img24x24" /></div>
		</div>

		<!-- artist or album -->
		<div class="displaytable w100per tacenter search_type">
			<div class="displaytablecell"><label><input type="radio" name="type" id="id_search_type_artist" value="artist" /> artist</label></div>
			<div class="displaytablecell"><label><input type="radio" name="type" id="id_search_type_title" value="title" /> title</label></div>
		</div>
		<div class="tabContent active">
			<div class="search_box">
				<div class="search_box_text"><input type="text" name="q" id="id_q" value="<?= h($q) ?>" placeholder="artist search" /></div>
				<div class="search_box_button"><a href="javascript:;" id="id_search"><img src="<?= h($base_path) ?>img/search.svg" alt="search" /></a></div>
			</div>
		</div>

		<div class="tabContent">
			<div class="search_index">
	<?php foreach(array_merge(range("a","z"),range(0,9),array("日")) as $alpha): ?>
				<a
					class="index"
					id="id_search_index_<?= h(strtoupper($alpha)) ?>"
					href="javascript:;"
					data-search_index="<?= h(strtoupper($alpha)) ?>"
				><?= h(strtoupper($alpha)) ?></a>
	<?php endforeach; unset($alpha); ?>
			</div>
		</div>

		<input type="hidden" name="stype" id="id_stype" value="search" />
		<input type="hidden" name="index" id="id_search_index" value="" />
	</form>
</div>

<div class="contents">
	<div id="id_search_results">
<?php require __DIR__ . '/Index_Ajax.tpl.php'; ?>
	</div>
</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){

	// tab menu
	$(".tab").on("click.js", function() {
		if ( $(this).hasClass("notab") ) {
			return;
		}
		var num = $(".tab").index(this);
		$(".tabContent").removeClass('active');
		$(".tabContent").eq(num).addClass('active');
		$(".tab").removeClass('active');
		$(this).addClass('active');
		$("#id_stype").val($(this).attr("data-stype").toLowerCase());
	});

	// type click
	$("input[name='type']").on("click.js", function(){
		$("#id_q").attr({placeholder:$(this).val() + " search"});
	});


	// incremental search
	var incSearch = function(){
		var $q = $("#id_q"), input, type, $loading = $("#id_loading");
		clearTimeout(incSearch.timer);
		incSearch.timer = setTimeout(function(){
			type = $("input[name='type']:checked").val();
			input = $.trim($q.val());
			if ( input.length < 2 || (type === incSearch.preType && input === incSearch.preInput) ) {
				return false;
			}
			$loading.fadeIn("fast");
			$.ajax( BASE_PATH + "Albums", {
				method : "GET",
				dataType : "HTML",
				data : {
					type: type,
					q : input,
					stype : "search",
					ajax : "1"
				}
			})
			.done(function(html){
				// DOM構築
				incSearch.createResult(html);
				// location.href に履歴を残す
				window.history.pushState(null, null, BASE_PATH+"Albums?" + hbq({
					type:type,
					q:input,
					stype:"search"
				}));
			})
			.fail(function(e){
				alert("system error.");
			})
			.always(function(){
				$loading.fadeOut("fast");
			});
			incSearch.preType = type;
			incSearch.preInput = input;
		}, 500);
	};
	incSearch.timer = null;
	incSearch.preType = null;
	incSearch.preInput = null;
	incSearch.createResult = function(html){
		$("#id_search_results").html(html);
		// 再bindしないと + 検索動かない
		bindAddAlbum();
	};
	$("#id_q").on("keyup.js",function(){
		incSearch();
	});
	$("input[name='type']").on("click.js", incSearch);


	// search button click
	$("#id_search").on("click.js", function(){
		$("#id_search_index").val("");
		$("#id_form_Albums_ArtistFilter").submit();
		return false;
	});

	// search index click
	$(".search_index > .index").on("click.js", function(){
		var $this = $(this);
		$("#id_q").val("");
		$("#id_search_index").val($this.attr("data-search_index"));
		$("#id_form_Albums_ArtistFilter").submit();
		return false;
	});

	// stype
	$("#id_stype_<?= h($stype) ?>").trigger("click.js");
	// type
	$("#id_search_type_<?= h($type) ?>").attr({checked:"checked"});
	// search_index
	$("#id_search_index_<?= h($index) ?>").addClass("active");

	// addAlbum
	var bindAddAlbum = function(){
		// 一旦削除してからon
		$(".add_album").off("click.js").on("click.js", function(){
			var href = $(this).attr("href");
			var type = $("input[name='type']:checked").val();
			var q = $.trim($("#id_q").val());
			var query = "";
			if ( q !== "" && /^(artist|title)$/.test(type) ) {
				query = "?" + [
					"type=" + encodeURIComponent(type),
					"q=" + encodeURIComponent(q)
				].join("&");
			}
			location.href=href+query;
			return false;
		});
	};

<?php if($is_login): ?>
	bindAddAlbum();
<?php endif; ?>

});
</script>

</body>
</html>