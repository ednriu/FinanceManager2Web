<script>
		window.onload = function() {
		 
		 
		var chart = new CanvasJS.Chart("chartContainer", {
			animationEnabled: true,
			title: {
				text: "Usage Share of Desktop Browsers"
			},
			subtitles: [{
				text: "November 2017"
			}],
			data: [{
				type: "pie",
				yValueFormatString: "#,##0.00\"%\"",
				indexLabel: "{label} ({y})",
				dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart.render();
		 
		}
</script>