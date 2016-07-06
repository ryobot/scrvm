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

<h4><?= h($type) ?> (<?= h($from) ?> ～ <?= h($to) ?>)</h4>

<div class="contents">

	<canvas id="id_chart" style="background-color: rgba(255,255,255,0.5);"></canvas>
	<script>
		;$(function(){

			var ctx = $("#id_chart");
			var data = <?= json_encode($chartjs_json_data) ?>;
			var options = {
				responsive : true,
				legend:{
					display:false
				},
				scales: {
					yAxes: [{
						ticks: {
							min:0,
							stepSize: 1,
							beginAtZero:true
						}
					}]
				}
			};

			// 各typeに応じて変更？
			$.extend(data.datasets[0], {
				backgroundColor :"rgba(0,120,200,0.5)",
				borderColor : "rgba(0,120,200,0.9)",
				borderWidth : 2
			});

			var myChart = new Chart(ctx, {
				type : "bar",
				data : data,
				options: options
			});

			console.log(myChart);
		});
	</script>

</div>

<?php require __DIR__ . '/../_parts/footer.tpl.php'; ?>

</div>
</body>
</html>