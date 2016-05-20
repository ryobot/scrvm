<?php
/**
 * Albums/Index.tpl.php
 * @author mgng
 */

$_base_url = "{$base_path}Albums";
$_base_params = array(
	"q" => $q,
	"type" => $type,
	"stype" => $stype,
	"index" => $index,
);
$most_prev_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
	"page" => "1",
	"sort"   => $sort,
	"order"  => $order,
)));
$prev_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
	"page" => $pager["now_page"]-1,
	"sort"   => $sort,
	"order"  => $order,
)));
$next_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
	"page" => $pager["now_page"]+1,
	"sort"   => $sort,
	"order"  => $order,
)));
$most_next_link = "{$_base_url}?" . hbq(array_merge($_base_params, array(
	"page" => $pager["max_page"],
	"sort"   => $sort,
	"order"  => $order,
)));
$nav_list = array();
foreach($pager["nav_list"] as $nav) {
	$nav_list[] = array(
		"active" => $nav["active"],
		"page" => $nav["page"],
		"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"page" => $nav["page"],
			"sort"   => $sort,
			"order"  => $order,
		))),
	);
}

// ソート用リンク
$order_type = $order === "asc" ? "desc" : "asc";
$sort_links = array(
	"reviews" => array(
		"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"sort"   => "reviews",
			"order"  => $order_type,
		))),
		"text" => $sort === "reviews" ? "[Reviews]" : "Reviews",
	),
	"artist" => array(
		"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"sort"   => "artist",
			"order"  => $order_type,
		))),
		"text" => $sort === "artist" ? "[Artist]" : "Artist",
	),
	"title" => array(
		"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"sort"   => "title",
			"order"  => $order_type,
		))),
		"text" => $sort === "title" ? "[Title]" : "Title",
	),
	"year" => array(
		"link" => "{$_base_url}?" . hbq(array_merge($_base_params, array(
			"sort"   => "year",
			"order"  => $order_type,
		))),
		"text" => $sort === "year" ? "[Year]" : "Year",
	),
);

?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<title><?= h($base_title) ?> :: Albums</title>
</head>
<body>

<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">

	<h2>Albums (<?= h($pager["total_count"]) ?>)</h2>

	<!-- search tabs -->
	<div class="search_tab">
		<form id="id_form_Albums_ArtistFilter" action="<?= h($base_path) ?>Albums" method="GET">
			<p>
				<ul class="tab">
					<li id="id_stype_search">Search</li>
					<li id="id_stype_index">Index</li>
<?php if( $is_login ): ?>
					<li class="notab"><span class="actions"><a href="<?= h($base_path) ?>Albums/Add">Add Album</a></span></li>
<?php endif; ?>
				</ul>
			</p>
			<div class="search_type">
				<label><input type="radio" name="type" id="id_search_type_artist" value="artist" />artist</label>
				&nbsp;
				<label><input type="radio" name="type" id="id_search_type_title" value="title" />title</label>
			</div>
			<div class="tabContent active">
				<p><input type="text" name="q" id="id_q" value="<?= h($q) ?>" placeholder="artist search" /></p>
				<p class="actions"><a href="javascript:;" id="id_search">Search</a></p>
			</div>
			<div class="tabContent">
				<ul class="search_index">
		<?php foreach(array_merge(range("a","z"),range(0,9),array("日")) as $alpha): ?>
					<li><a id="id_search_index_<?= h(strtoupper($alpha)) ?>" href="javascript:;" data-search_index="<?= h(strtoupper($alpha)) ?>"><?= h(strtoupper($alpha)) ?></a></li>
		<?php endforeach; unset($alpha); ?>
				</ul>
			</div>
			<input type="hidden" name="stype" id="id_stype" value="search" />
			<input type="hidden" name="index" id="id_search_index" value="" />
		</form>
	</div>

<?php if ( count($lists) > 0 ):?>

	<!-- pager -->
	<div class="tacenter">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

	<!-- album lists -->
	<div class="w100per tacenter">
		<a href="<?= h($sort_links["artist"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort artist" class="img16x16" /><?= h($sort_links["artist"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["title"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort title" class="img16x16" /><?= h($sort_links["title"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["year"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort year" class="img16x16" /><?= h($sort_links["year"]["text"]) ?></a>
		/
		<a href="<?= h($sort_links["reviews"]["link"]) ?>"><img src="<?= h($base_path) ?>img/sort.svg" alt="sort reviews" class="img16x16" /><?= h($sort_links["reviews"]["text"]) ?></a>
	</div>
<?php foreach($lists as $album): ?>
	<div class="displaytable w100per album_info">
		<div class="displaytablecell tacenter w80px">
			<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["id"]) ?>">
				<img
					class="album_cover"
					src="<?= isset($album["img_file"])? "{$base_path}files/covers/{$album["img_file"]}" : "{$base_path}img/no_image.png" ?>"
					alt="<?= h( "{$album["artist"]} / {$album["title"]}") ?>"
				/>
			</a>
<?php if ( $is_login && $album["create_user_id"] === $login_user_data["id"] ): ?>
			<p class="actions"><a href="<?= h($base_path) ?>Albums/Edit?id=<?= h($album["id"]) ?>">Edit</a></p>
<?php endif; ?>
		</div>
		<div class="displaytablecell vtalgmiddle">
			<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["id"]) ?>">
				<?= h($album["artist"]) ?>
				/
				<?= h($album["title"]) ?>
				(<?= isset($album["year"]) && $album["year"] !== "" ? h($album["year"]) : "unknown" ?>)
			</a>
<?php if($album["reviews"] > 0): ?>
			<p>
				<a href="<?= h($base_path) ?>Albums/View?id=<?= h($album["id"]) ?>">
					<img src="<?= h($base_path) ?>img/reviews.svg" alt="reviews" class="img16x16" />
					<span class="vtalgmiddle"><?= h($album["reviews"]) ?></span>
				</a>
			</p>
<?php endif;?>
		</div>
	</div>
<?php endforeach; ?>

	<!-- pager -->
	<div class="tacenter">
		<ul class="pagination">
<?php if($pager["prev"]): ?>
			<li><a href="<?= h($prev_link) ?>">&laquo;</a></li>
<?php endif;?>
<?php foreach($nav_list as $nav): ?>
			<li><a <?= $nav["active"] ? 'class="active"' : '' ?> href="<?= h($nav["link"]) ?>"><?= h($nav["page"]) ?></a></li>
<?php endforeach; ?>
<?php if($pager["next"]): ?>
			<li><a href="<?= h($next_link) ?>">&raquo;</a></li>
<?php endif;?>
		</ul>
	</div>

<?php else:?>
	<p class="error_message tacenter">not found.</p>
<?php endif;?>

</div>
<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>

<script>
;$(function(){
	// tab menu
	$(".tab li").on("click.js", function() {
		if ( $(this).hasClass("notab") ) {
			return;
		}
		var num = $(".tab li").index(this);
		$(".tabContent").removeClass('active');
		$(".tabContent").eq(num).addClass('active');
		$(".tab li").removeClass('active');
		$(this).addClass('active');
		$("#id_stype").val($(this).text().toLowerCase());
	});

	// type click
	$("input[name='type']").on("click.js", function(){
		$("#id_q").attr({placeholder:$(this).val() + " search"});
	});

	// search button click
	$("#id_search").on("click.js", function(){
		$("#id_search_index").val("");
		$("#id_form_Albums_ArtistFilter").submit();
		return false;
	});

	// search index click
	$(".search_index > li > a").on("click.js", function(){
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


});
</script>

</body>
</html>