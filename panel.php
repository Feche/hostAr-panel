<?php
	$VERSION = "1.0.3";
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	if(!isset($_SESSION["username"]))
	{
		header("Location: login.php");
		die();
	}
	
	if($_GET["v"] == "" || !isset($_GET["v"]))
	{
		header("Location: scripts/login/logout.php");
		die();
	}	
	
	header('X-Accel-Buffering: no');
	
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["v"];
?>

<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1">
		<title>hostAR - Panel de control</title>
		
		<link rel="stylesheet" type="text/css" href="panel.css?rnd=<?php echo rand(0, 100); ?>" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"/>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	</head>
	
	<body>
		<div class="main">
			<div class="logo-container-mobile" style="display: none;">
				<img id="logo-mobile" src="img/logo.png"/>
				<div id="responsive-button" class="fa fa-bars" onclick="toggleResponsiveNavbar()"></div>
			</div>
			<div class="nav-container">
				<img id="logo" src="img/logo-180px.png"/>
				<div class="line"></div>
				<div class="p1" style="color: white">Bienvenido, <?php echo $USERNAME ?></div>
				<div style="margin-top: 1em;">
					<div class="p1">Servidores</div>
					<!-- SERVERS -->
					<div>
						<?php
							$con = new mysqli("127.0.0.1", "database", "password", "username");
							$result = $con->query("SELECT serverid, servertp, port FROM servers WHERE username = '$USERNAME'");
							$size = $result->num_rows;
							if($size > 0) 
							{ 
								while($row = $result->fetch_assoc())
								{
									echo '
										<div class="button-container' . ($row["serverid"] == $SID ? " selected" : "") . '">
											<i class="fas fa-server"></i>
											<div class="button' . ($row["serverid"] == $SID ? " selected" : "") . '" style="font-size: 0.95em; box-shadow: none; -moz-box-shadow: none; -webkit-box-shadow: none;" onclick="window.location.href = \'panel.php?v=' . $row["serverid"] . '\';">
												' . $row["servertp"] . '-' . $row["serverid"] . ' 
											</div>
										</div>
									';
								}
							}
						?>
					</div>
					<!-- CONFIG -->
					<div>
						<div class="p1">Configuración</div>
						<div class="button-container control">
							<i class="fas fa-gamepad"></i>
							<div class="button" onclick="switchPanelTo('control')">Control</div>
						</div>
						<div class="button-container plugins">
							<i class="fas fa-plug"></i>
							<div class="button" onclick="switchPanelTo('plugins')">Plugins</div>
						</div>
						<div class="button-container console">
							<i class="fas fa-terminal"></i>
							<div class="button" onclick="switchPanelTo('console')">Consola</div>
						</div>
						<div class="button-container statics">
							<i class="fas fa-chart-bar"></i>
							<div class="button" onclick="switchPanelTo('statics')">Estadísticas</div>
						</div>
						<div class="button-container logout">
							<i class="fas fa-sign-out-alt"></i>
							<div class="button" onclick="switchPanelTo('logout')">Cerrar sesión</div>
						</div>
					</div>
				</div>
				<div id="version">v<?php echo $VERSION; ?> - hostAR</div>
			</div>
			
			<div class="workarea-container">
				<!-- LOADING -->
				<div class="loading-container">
					<div class="loadingio-spinner-dual-ring-bgtl78s4uet"><div class="ldio-zuajeo5xa2">
					<div></div><div><div></div></div>
					</div></div>
					<style type="text/css">
						@keyframes ldio-zuajeo5xa2 {
						  0% { transform: rotate(0) }
						  100% { transform: rotate(360deg) }
						}
						.ldio-zuajeo5xa2 div { box-sizing: border-box!important }
						.ldio-zuajeo5xa2 > div {
						  position: absolute;
						  width: 72px;
						  height: 72px;
						  top: 14px;
						  left: 14px;
						  border-radius: 50%;
						  border: 8px solid #000;
						  border-color: #ffffff transparent #ffffff transparent;
						  animation: ldio-zuajeo5xa2 1s linear infinite;
						}
						.ldio-zuajeo5xa2 > div:nth-child(2) { border-color: transparent }
						.ldio-zuajeo5xa2 > div:nth-child(2) div {
						  position: absolute;
						  width: 100%;
						  height: 100%;
						  transform: rotate(45deg);
						}
						.ldio-zuajeo5xa2 > div:nth-child(2) div:before, .ldio-zuajeo5xa2 > div:nth-child(2) div:after { 
						  content: "";
						  display: block;
						  position: absolute;
						  width: 8px;
						  height: 8px;
						  top: -8px;
						  left: 24px;
						  background: #ffffff;
						  border-radius: 50%;
						  box-shadow: 0 64px 0 0 #ffffff;
						}
						.ldio-zuajeo5xa2 > div:nth-child(2) div:after { 
						  left: -8px;
						  top: 24px;
						  box-shadow: 64px 0 0 0 #ffffff;
						}
						.loadingio-spinner-dual-ring-bgtl78s4uet {
						  width: 64px;
						  height: 64px;
						  display: inline-block;
						  overflow: hidden;
						  background: none;
						  
						  position: absolute;
						  top: 0;
						  bottom: 0;
						  left: 0;
						  right: 0;
						  margin: auto;
						}
						.ldio-zuajeo5xa2 {
						  width: 100%;
						  height: 100%;
						  position: relative;
						  transform: translateZ(0) scale(0.64);
						  backface-visibility: hidden;
						  transform-origin: 0 0; /* see note above */
						}
						.ldio-zuajeo5xa2 div { box-sizing: content-box; }
						/* generated by https://loading.io/ */
					</style>
				</div>
				
				<div class="workarea">
					
				</div>
			</div>
		</div>
		
		<script>
			<?php echo 'var sID = "' . $SID . '";' ?>
			var currPanel = "control";
			var canClick = true;
			var gSet = false;
			
			var gTotalHeigth = 1;
			var gActive = 0;
			
			var consoleTimer = false;
			
			$(document).ready(function() {
				switchPanelTo(currPanel);
			});
			
			function switchPanelTo(panel) {
				if(!canClick)
					return false;
				
				if(isMobile() && gSet == true)
					toggleResponsiveNavbar();
				
				if(consoleTimer)
					clearInterval(consoleTimer);
				
				canClick = false;
				gSet = true;
				currPanel = panel;
				
				var css = { "background-color" : "#333", "color" : "#fff", "border-left" : "none" };
				$(".control").css(css);
				$(".plugins").css(css);
				$(".console").css(css);
				$(".statics").css(css);
				$(".logout").css(css);
				
				var css = { "background-color" : "#444", "color" : "#0fc0fc", "border-left" : "solid 2px #0fc0fc" };
				$("." + panel).css(css);
				
				$(".loading-container").css("visibility", "visible");
				$(".loading-container").css("opacity", "0.7");
				
				$(".workarea").css("opacity", "0");
				
				setTimeout(function() {
					$(".workarea").empty();
					//$(".workarea").remove();
					
					$.get("modules/" + panel + ".php?v=" + sID,
						function(response) {
							$(".workarea").append(response);
						}
					);
				}, 500);
			
				setTimeout(function() {
					$(".loading-container").css("opacity", "0");
					setTimeout(function() { 
						$(".loading-container").css("visibility", "hidden"); 
					}, 500)
					
					if(panel == "logout")
						window.location.href = "scripts/login/logout.php";
					
					$(".workarea").css("opacity", "1");
					setTimeout(function() { canClick = true; }, 500);
				}, 2000); // Fade time
			}
			
			/* NOTIFICATION */
			
			function showNotification(msg, iserror) {
				iserror = iserror == true ? true : false;
	
				var uID = Math.floor(Math.random() * 10000);

				$(".main").append('<div id="notification-' + uID + '" class="notification-container slide-in">' + msg + '</div>');
				$("#notification-" + uID).css("top", (gTotalHeigth + (isMobile() ? 4 : 0)) + "em");
				
				if(iserror) {
					$("#notification-" + uID).css("background-color", "#E74C3C");
				} else {
					$("#notification-" + uID).css("background-color", "#2ECC71");
				}
				
				gTotalHeigth = gTotalHeigth + 5;
				gActive++;

				setTimeout(function() {
					$("#notification-" + uID).css("opacity", "0");
					gActive--;
					if(gActive == 0) 
						gTotalHeigth = 1;
					setTimeout(function() { 
						$("#notification-" + uID).remove(); 
					}, 500);
				}, 5000)
			}
			
			/* RESPONSIVE CSS */
			
			var inView = false;
			function toggleResponsiveNavbar(override) {
				if(!canClick) 
					return false;
				
				inView = !inView;
				$(".nav-container").css("left", inView ? "0%" : "-100%");
				$(".nav-container").css("opacity", inView ? "1" : "0");
			}
			
			function isMobile() {
				return ($(".nav-container").css("box-shadow") == "none" ? true : false);
			}
		</script>
	</body>
</html>