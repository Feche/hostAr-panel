<?php
	$IP = "127.0.0.1";
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	if(!isset($_SESSION["username"]) || !isset($_GET["v"]))
	{
		header("Location: ../login.php");
		die();
	}
	
	include "../scripts/include/file_utils.inc";
						
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["v"];
	$TP = "";
	$PORT = 0;
	$SERVER = 0;
	$PLAYERS = 0; // Console
	
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT * FROM servers WHERE username = '$USERNAME' && serverid = '$SID'");
	$row = $result->fetch_assoc();
	if($row)
	{
		$SERVERINFO = json_decode($row["settings"], true);
		$TP = $row["servertp"];
		$SERVER = $row["node"];
		$PORT = $row["port"];
	}
	else
		header("Location: ../login.php");
	
	mysqli_close($con);
?>

<script>
	var sData = [];
</script>

<div class="settings-container">
	<center>
		<h1>Configuración del servidor<br><?php echo "$TP-$SID:$PORT"; ?></h1>
		<p>Hacé click la configuración que nesecitas modificar,<br>una vez hecho los cambios, <b>tendrás que guardar la configuración.</b></p>
		<h3>Los cambios tendrán efecto en el próximo inicio del servidor.</h3>
	</center>

	<?php
		foreach($SERVERINFO as $KEY => $VALUE)
		{
			if($KEY != "gotv")
			{
				echo '
				<div class="settings-flexbox" onclick="onConfigurationClick(\'' . $KEY . '\')">
					<div class="config-name">' . $KEY . '</div>
					<div class="config-value">
						<input class="config-input sData-' . $KEY . '" type="input" autocomplete="false" value="' . $VALUE . '"/>
					</div>
				</div>';
			}
			// GOTV
			else
			{
				echo '
				<div class="settings-flexbox" onclick="onConfigurationClick(\'' . $KEY . '\')">
					<div class="config-name">' . $KEY . '</div>
					<div class="config-value">
						' . $VALUE . '
					</div>
				</div>';
			}
			
			echo "<script>sData.push('$KEY');</script>";
		}
	?>

	<!-- STATE -->
	<div class="settings-flexbox">
		<div class="config-name">estado</div>
		<div class="config-value" id="state-input" style="color: <?php echo (is_server_online($row["pid"], $SERVER) ? "green" : "red"); ?>;">
			<?php echo (is_server_online($row["pid"], $SERVER) ? "online" : "offline"); ?>
		</div>
	</div>

	<!-- DUE DATE -->
	<div class="settings-flexbox">
		<div class="config-name">fecha de vencimiento</div>
		<div class="config-value">
			<?php echo date("d/m/Y", $row["duedate"]); ?>
		</div>
	</div>

	<!-- IP PORT -->
	<div class="settings-flexbox">
		<div class="config-name">ip</div>
		<div class="config-value">
			<?php echo /*$row["ip"]*/ $IP . ':' . $PORT; ?>
		</div>
	</div>

	<!-- USED SPACE -->
	<?php
		$STRING = exec_server_cmd("df -m /servers/" . $TP . '-' . $SID, $SERVER);
		$SIZES = explode(",", preg_replace('!\s+!', ',', $STRING));

		$TOTALSIZE = is_numeric($SIZES[1]) ? $SIZES[1] : 1;
		$USEDSIZE = is_numeric($SIZES[2]) ? $SIZES[2] : 1;
		$PERCENTUSED = $SIZES[4];
		//$WIDTH = ($USEDSIZE / $TOTALSIZE) * 100;
		$WIDTH = intval($PERCENTUSED);
	?>

	<div class="settings-flexbox">
		<div class="config-name">espacio usado</div>
		<div class="config-value">
			<div class="totalspace-container">
				<div id="totalspace" style="width: <?php echo $WIDTH; ?>%;"></div>
				<div id="totalspace-text">
						<?php echo "$USEDSIZE MB / " . ($TOTALSIZE - 120) . " MB ($PERCENTUSED)" ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- BOTONES -->
<div class="buttons-container">
	<div id="button-start" onclick="startStopServer('start');">Iniciar</div>
	<div id="button-stop" onclick="startStopServer('stop');">Detener</div>
</div>
<div id="button-save" onclick="saveServerSettings();" style="pointer-events: none;">Guardar</div>

<script>
	var alertReinstall = 0;
	
	function onConfigurationClick(tp) {
		$("#button-save").css("z-index", "1");
		$("#button-save").css("opacity", "1");
		$("#button-save").css("pointer-events", "all");
	}
	
	function startStopServer(tp) {
		$.get("scripts/control/" + tp + ".php?sid=" + sID, 
			function(response) {
				console.log(response);
				
				var rData = JSON.parse(response);
				showNotification(rData[1], rData[0]);

				$("#state-input").text(rData[2] == false ? "offline" : "online");
				$("#state-input").css("color", rData[2] == false ? "red" : "green");
			}
		);
	}
	
	function saveServerSettings() {
		var sSettings = {};
		for (i = 0; i < sData.length; i++)
			sSettings[sData[i]] = $(".sData-" + sData[i]).val();

		$.post("scripts/control/save.php", { sid: sID, data: JSON.stringify(sSettings) },
			function(response) {
				console.log(response);
				var rData = JSON.parse(response);
				showNotification(rData[1], rData[0]);
			}
		);
	}
</script>