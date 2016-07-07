<?php
/**
 * Users/Chart.tpl.php
 * @author mgng
 */
?>
<!doctype html>
<html lang="ja">
<head>
<?php require __DIR__ . '/../_parts/meta_common.tpl.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
<title><?= h($user["username"]) ?> - Users::Chart - <?= h($base_title) ?></title>
</head>
<body>
<div id="container">

<?php require __DIR__ . '/../_parts/header_menu.tpl.php'; ?>
<div class="contents">
	<?php require __DIR__ . "/_profile.tpl.php" ?>
</div>

<h3>
	<img src="<?= h($base_path) ?>img/chart.svg" class="img16x16" alt="chart" title="chart" />
	Chart
</h3>

<div class="contents">

	<h4>Top <?= count($chart_data["syncs"]["labels"]) ?> Sync Users</h4>
	<canvas id="id_chart_syncs" style="background-color: rgba(255,255,255,0.5);"></canvas>

	<h4>Top <?= count($chart_data["reviews_artist"]["labels"]) ?> Artist Chart</h4>
	<canvas id="id_chart_reviews_artist" style="background-color: rgba(255,255,255,0.5);"></canvas>

	<h4>Reviews (<?= h($from) ?> ～ <?= h($to) ?>)</h4>
	<canvas id="id_chart_reviews" style="background-color: rgba(255,255,255,0.5);"></canvas>

	<h4>Hourly (<?= h($from) ?> ～ <?= h($to) ?>)</h4>
	<canvas id="id_chart_reviews_hourly" style="background-color: rgba(255,255,255,0.5);"></canvas>

	<script>
		;$(function(){

			var chart_data = <?= json_encode($chart_data) ?>;
			var style = {
				syncs : {
					backgroundColor :"rgba(120,0,200,0.5)",
					borderColor : "rgba(120,0,200,0.9)",
					borderWidth : 1
				},
				reviews_artist : {
					backgroundColor :"rgba(120,200,0,0.5)",
					borderColor : "rgba(120,200,0,0.9)",
					borderWidth : 1
				},
				reviews : {
					backgroundColor :"rgba(0,120,200,0.5)",
					borderColor : "rgba(0,120,200,0.9)",
					borderWidth : 1
				},
				reviews_hourly : {
					backgroundColor :"rgba(200,0,120,0.5)",
					borderColor : "rgba(200,0,120,0.9)",
					borderWidth : 1
				}
			};
			var options = {
				syncs : {
					legend:{ display:false }
				},
				reviews_artist : {
					legend:{ display:false },
					scales: {
						xAxes: [{
							ticks: {
								min:0,
								stepSize: 1,
								beginAtZero:true
							}
						}]
					}
				},
				reviews : {
					legend:{display:false},
					scales: {
						yAxes: [{
							ticks: {
								min:0,
								stepSize: 1,
								beginAtZero:true
							}
						}]
					}
				},
				reviews_hourly : {
					legend:{ display:false },
					scale: {
						ticks: {
							min:0,
							beginAtZero:true
						}
					}
				}
			};

			$.extend(chart_data.syncs.datasets[0], style.syncs);
			$.extend(chart_data.reviews_artist.datasets[0], style.reviews_artist);
			$.extend(chart_data.reviews.datasets[0], style.reviews);
			$.extend(chart_data.reviews_hourly.datasets[0], style.reviews_hourly);

			var chartSyncs = new Chart($("#id_chart_syncs"), {
				type : "horizontalBar",
				data : chart_data.syncs,
				options: options.syncs
			});

			var chartArtist = new Chart($("#id_chart_reviews_artist"), {
				type : "horizontalBar",
				data : chart_data.reviews_artist,
				options: options.reviews_artist
			});

			var chartReviews = new Chart($("#id_chart_reviews"), {
				type : "bar",
				data : chart_data.reviews,
				options: options.reviews
			});

			var chartReviewsHour = new Chart($("#id_chart_reviews_hourly"), {
				type : "radar",
				data : chart_data.reviews_hourly,
				options: options.reviews_hourly
			});



		});
	</script>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>