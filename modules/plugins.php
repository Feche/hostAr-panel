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
	
	include "../scripts/include/file_utils.inc";
	
	$PLUGINS = array(
		"cs16" => array(
				"amxmodx" => array("AMX Mod X 1.8.2", "Te permite acceder al servidor como administrador y ejecutar comandos.<br>Tambien es necesario instalarlo para agregar los demás plugins.<br><br><b>¡Recomendado!</b>"),
				   "sxei" => array("SXe injected 17.1", "El anti-cheat número uno para Counter-Strike 1.6<br>Evita que entren cheaters al servidor, y asi mantener un ambiente de juego libre de cheaters.<br><br><b>Recomendado para servidores públicos.</b>"),
				    "vtc" => array("VoiceTranscoder RC2017", "Este plugin mejora la calidad de voz, y te permite que jugadores Steam y no Steam puedan escucharse.<br><br><b>¡Recomendado!</b>"),
			   "antispam" => array("AntiSpam", "Plugin para preevenir el SPAM/FLOOD en el chat y asi mantenerlo limpio de spammers.<br><br><b>Recomendado para servidores públicos</b>"),
				"gungame" => array("Gun Game", "El clásico Gun Game, para Counter-Strike 1.6<br><br><b>¡Recomendado!</b>"),
				    "5ya" => array("Datear CincoYa.net", "Si te falta gente para jugar, tirá /datear mix y el server se publicará en la pagina de <b>CincoYa.net</b> donde otros jugadores podrán unirse al servidor.<br><br><b>¡Recomendado!</b>"),
				    "abd" => array("Advanced Bullet Damage", "Muestra en tiempo real, el daño realizado al enemigo y el recibido por el enemigo.<br><br><b>¡Recomendado!</b>"),
			  "packmapas" => array("Pack de mapas", "Pack de 106 mapas para el servidor.<br><br>Incluye mapas aim, awp, cs, de, dm y fy.<br><br><b>¡Recomendado!</b>")
		),
		"csgo" => array(
			"csgoupdate" => array("Actualización del servidor", "En caso de que haya una actualización, deberás actualizar los archivos del servidor.<br><br><b>Puede demorar hasta 10 minutos</b>")
		)
	);
	
	$SID = $_GET["v"];
	$STP = "";
	$USERNAME = $_SESSION["username"];
	
	$con = new mysqli("127.0.0.1", "database", "username", "password");
	$result = $con->query("SELECT * FROM servers WHERE username = '$USERNAME' && serverid = '$SID'");
	$row = $result->fetch_assoc();
	if($row)
	{
		$STP = $row["servertp"];
	}
	else
	{
		header("Location: https://panel.host-ar.com.ar/logout.php");
	}
	
	mysqli_close($con);
?>

<div class="plugins-container">
	<center>
		<h1>Instalación de plugins</h1>
		<p>Seleccioná el plugin que deseas instalar, y después hace click en 'instalar'.</b></p>
		<h3>Los plugins tendrán efecto en el próximo inicio del servidor.</h3>
	</center>
	
	<script>
		var gPlugins = [];
	</script>
	
	<?php
		if(isset($PLUGINS[$STP]))
		{
			echo '<div id="plugins-flexbox">';
				foreach($PLUGINS[$STP] as $key => $value)
				{
					echo '
						<div id="plugin-' . $key . '" class="plugin-box" onclick="onClickPlugin(\'' . $key . '\')">
							<div style="position: relative; top: 50%; transform: translate(0, -50%);">
								<div style="font-size: 1.4em; margin-bottom: 0.5em;"><b>' . $value[0] . '</b></div>
								' . $value[1] . '
							</div>
						</div>
						<script>
							gPlugins.push(\'' . $key . '\');
						</script>
					';
				}
			echo '</div>';
			echo '<div id="button-install" onclick="onClickInstall()">INSTALAR</div>';
		}
		else
			echo '<center><strong><h2 style="color: red">Plugins no disponibles para este tipo de servidor.</h2></strong></center>';
	?>

	<center>
		<h1>Reinstalación del servidor</h1>
		<p>En caso de tener problemas con la configuración del servidor o algún otro inconveniente,<br>podés realizar la reinstalación del servidor desde acá.</b></p>
		<h3>Se perderán todos los archivos y configuraciones, hacer backup en caso necesario.</h3>
	</center>
	<div id="button-reinstall" onclick="reinstallServer();">REINSTALAR</div>
</div>

<script>				
	var gSelected = "";
	
	function onClickPlugin(tp) {
		gSelected = tp;
		for(var i = 0; i < gPlugins.length; i++) {
			if(gPlugins[i] != tp)
				$("#plugin-" + gPlugins[i]).css("border", "solid 1px #eee");
		}
		$("#plugin-" + tp).css("border", "solid 1px #0fc0fc");
	}
	
	function onClickInstall(tp) {
		if(gSelected == "")
			showNotification("Primero seleccioná un plugin!", true);
		else {
			$.get("scripts/plugins/install_plugin.php?sid=" + sID + "&plugin=" + gSelected, 
				function(response) {
					var rData = JSON.parse(response);
					showNotification(rData[1], rData[0]);
				}
			);
		}
	}
	
	function reinstallServer() {
		if(alertReinstall == 0) {
			$("#button-reinstall").html("CONFIRMAR");
			alert("ATENCION:\nSE BORRARAN TODOS LOS ARCHIVOS Y CONFIGURACIONES DEL SERVIDOR, Y SE VOLVERA 'A FABRICA'.\n\nUNA VEZ REINSTALADO, NO SE PODRAN RECUPERAR LOS ARCHIVOS SUBIDOS Y/O MODIFICADOS.\n\n***** PROCEDER BAJO SU RESPONSABILIDAD *****");
			alertReinstall = 1;
			return false;
		} else if(alertReinstall == 1) {
			alertReinstall = -1;
			
			$("#button-reinstall").html("REINSTALANDO..");
			
			$.get("scripts/plugins/install_plugin.php?sid=" + sID + "&plugin=resetear", 
				function(response) {
					console.log(response);
					var rData = JSON.parse(response);
					
					showNotification(rData[1], rData[0]);
					if(rData[1] != "Apagá el servidor antes de reinstalarlo")
						$("#button-reinstall").html("FINALIZADO");
					else
						$("#button-reinstall").html("ERROR");
				}
			);
		}
	}
</script>