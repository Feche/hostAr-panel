<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	if(!isset($_SESSION["username"]))
	{
		header("Location: ../login.php");
		die();
	}
	
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["v"];
	$STATICS;
	
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT * FROM servers WHERE username = '$USERNAME' && serverid = '$SID'");
	$row = $result->fetch_assoc();
	if($row)
	{
		$STATICS = json_decode($row["statics"], true);
	}
	else
		header("Location: ../login.php");
?>

<div class="statics-container">
	<center>
		<h1>Estadísticas</h1>
		<p>Este gráfico muestra los jugadores conectados de las últimas 24hs.</p>
		<p style="font-size: 0.8em;"><b>Se actualiza cada 5 minutos.</b></p>
	</center>
	
	<div class="graph-container">
		<canvas id="graph"></canvas>
	<div>
	<br>
	<center>
		<h1>Jugadores online</h1>
		<p>Acá podés ver los jugadores conectados actualmente en el servidor.</p>
	</center>
	
	<div class="flexbox-container">
	
	</div>
</div>

<script>	
	function updatePlayerList() {
		$.get("scripts/statics/get_player_list.php?v=" + sID,
			function(response) {
				console.log(response);
				$(".flexbox-container").append(response);
			}
		);
	}
	updatePlayerList();

	var ctx = document.getElementById('graph').getContext('2d');
	var graph = new Chart(ctx, {
		type: 'line',
		data: {
			labels: [
				<?php 
					for($i = 0; $i < sizeof($STATICS); $i++)
					{
						echo "'" . date("H:i", $STATICS[$i][0]) . "', ";
					}
				?>
			],
			datasets: [{
				label: 'jugadores',
				data: [
					<?php 
						for($i = 0; $i < sizeof($STATICS); $i++)
						{
							echo ($STATICS[$i][1] == "?" ? 0 : $STATICS[$i][1]) . ", ";
						}
					?>
				],
				backgroundColor: [
					'rgba(66, 135, 245, 0.2)'
				],
				borderColor: [
					'rgba(66, 135, 245, 1)'
				],
				borderWidth: 1
			}]
		},
		options: {
			legend: {
				display: false,
				rtl: true
			},
			scales: {
				yAxes: [{
					ticks: {
						beginAtZero: true
					}
				}],
				xAxes: [{
					ticks: {
						reverse: true
					}
				}]
			}
		}
	});
</script>