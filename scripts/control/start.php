<?php
	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	
	include "../../scripts/include/file_utils.inc";
	include "../../scripts/include/log_utils.inc";

	session_start();
	
	if(!isset($_SESSION["username"]))
		header("Location: ../../login/login.php");
	
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["sid"];
	
	//echo '[true, "Operacion no permitida, en mantenimiento", false]';
	//die();
	
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT * FROM servers WHERE username = '$USERNAME' && serverid = '$SID'");
	$row = $result->fetch_assoc();
	if($row)
	{
		$SERVERINFO = json_decode($row["settings"], true);
		if(intval($row["pid"]) == 0)
		{
			$ID = $row["servertp"] . "-" . $SID;
			$SERVER = $row["node"];
			
			switch($row["servertp"])
			{
				case "mtasa":
					exec_server_cmd("sed -i '/serverport/d' /servers/mtasa-$SID/mods/deathmatch/mtaserver.conf", $SERVER);
					exec_server_cmd("sed -i '/httpport/d' /servers/mtasa-$SID/mods/deathmatch/mtaserver.conf", $SERVER);
					exec_server_cmd("sed -i '/maxplayers/d' /servers/mtasa-$SID/mods/deathmatch/mtaserver.conf", $SERVER);
					exec_server_cmd("sed -i \"/<\/config>/d\" /servers/mtasa-$SID/mods/deathmatch/mtaserver.conf", $SERVER);
					
					exec_server_cmd('printf "\n    <serverport>' . $row["port"] . '</serverport>" >> /servers/mtasa-' . $SID . '/mods/deathmatch/mtaserver.conf', $SERVER);
					exec_server_cmd('printf "\n    <httpport>' . $row["port"] . '</httpport>" >> /servers/mtasa-' . $SID . '/mods/deathmatch/mtaserver.conf', $SERVER);
					exec_server_cmd('printf "\n    <maxplayers>' . $SERVERINFO["slots"] . '</maxplayers>" >> /servers/mtasa-' . $SID . '/mods/deathmatch/mtaserver.conf', $SERVER);
					exec_server_cmd('printf "\n</config>" >> /servers/mtasa-' . $SID . '/mods/deathmatch/mtaserver.conf', $SERVER);

					exec_server_cmd("sudo -u $USERNAME screen -dmS $ID /servers/$ID/mta-server64", $SERVER);
					break;
				case "samp":
					if($SERVERINFO["rcon_password"] == "changeme")
					{
						echo '[true, "Por favor, cambiá la contraseña RCON para poder encender el servidor", false]';
						die();
					}
				 	exec_server_cmd("cd /servers/$ID && sudo -u $USERNAME screen -dmS $ID ./samp03svr", $SERVER);
					break;
				case "csgo":
					if($SERVERINFO["sv_setsteamaccount"] == "")
					{
						echo '[true, "Por favor, ingresá la Steam key (sv_setsteamaccount)", false]';
						die();
					}
					
					if($SERVERINFO["rcon_password"] == "abc123")
					{
						echo '[true, "Por favor, cambiá la contraseña RCON para poder encender el servidor", false]';
						die();
					}
					
					$CMD = "./srcds_run -debug -game csgo -console -usercon +game_type " . $SERVERINFO["game_type"] . " +game_mode " . $SERVERINFO["game_mode"] . " +mapgroup " . $SERVERINFO["mapgroup"] . " +map " . $SERVERINFO["map"] . " +sv_setsteamaccount " . $SERVERINFO["sv_setsteamaccount"] . " -tickrate 128 -port " . $row["port"] . " -condebug -workshop -maxplayers_override " . $SERVERINFO["slots"];
					
					/* NO BOTS */
					//if($USERNAME != "Feche")
					if(true)
					{
					    $CMD = $CMD . " -nobots";
					}
					
					/* PASSWORD */
					if($SERVERINFO["password"] != "")
					{
						$CMD = $CMD . " +sv_password \"" . $SERVERINFO["password"] . "\"";
					}
					
					/* RCON PASSWORD */
					if($SERVERINFO["rcon_password"] != "")
					{
						$CMD = $CMD . " +rcon_password \"" . $SERVERINFO["rcon_password"] . "\"";
					}
					
					/* GOTV */
					if($SERVERINFO["gotv"] != "")
					{
						$CMD = $CMD . " +tv_port \"" . $SERVERINFO["gotv"] . "\"";
					}
					
					exec_server_cmd('mv /servers/' . $ID . '/screenlog.0 /servers/' . $ID . '/log_' . date("Ymd-Hms", microtime(true)) . ".log", $SERVER); // Backup old log
					
					exec_server_cmd("sed -i '/hostname/d' /servers/csgo-$SID/csgo/cfg/default.cfg", $SERVER);
					exec_server_cmd('printf "\nhostname \"' . $SERVERINFO["servername"] . '\"" >> /servers/csgo-' . $SID . '/csgo/cfg/default.cfg', $SERVER);
					
					exec_server_cmd("cd /servers/$ID/ && sudo -u $USERNAME screen -dmSL $ID $CMD", $SERVER);
					exec_server_cmd('sudo -u ' . $USERNAME . ' screen -r ' . $ID . ' -X colon "logfile flush 1^M"', $SERVER);
					break;
				case "cs16":
					if($SERVERINFO["rcon_password"] == "abc123")
					{
						echo '[true, "Por favor, cambiá la contraseña RCON para poder encender el servidor", false]';
						die();
					}

					$CMD = "./hlds_run -console -insecure -game cstrike +log on +port " . $row["port"] . " +map " . $SERVERINFO["map"] . " +maxplayers " . $SERVERINFO["slots"];
					
					/* PASSWORD */
					if($SERVERINFO["password"] != "")
					{
						$CMD = $CMD . " +sv_password \"" . $SERVERINFO["password"] . "\"";
					}
					
					/* RCON PASSWORD */
					if($SERVERINFO["rcon_password"] != "")
					{
						$CMD = $CMD . " +rcon_password \"" . $SERVERINFO["rcon_password"] . "\"";
					}
					
					exec_server_cmd('mv /servers/' . $ID . '/screenlog.0 /servers/' . $ID . '/log_' . date("Ymd-Hms", microtime(true)) . ".log", $SERVER); // Backup old log
					exec_server_cmd('rm -rf /servers/' . $ID . '/cstrike/logs/', $SERVER); // Remove old log
					exec_server_cmd('cd /servers/' . $ID . '/ && sudo -u ' . $USERNAME . ' screen -dmSL ' . $ID . ' ' . $CMD, $SERVER);
					exec_server_cmd('sudo -u ' . $USERNAME . ' screen -r ' . $ID . ' -X colon "logfile flush 1^M"', $SERVER);
					break;
				case "fivem":
					if($SERVERINFO["rcon_password"] == "abc123")
					{
						echo '[true, "Por favor, cambiá la contraseña RCON para poder encender el servidor", false]';
						die();
					}
					
					if($SERVERINFO["sv_licenseKey"] == "")
					{
						echo '[true, "Por favor, ingresá la license key", false]';
						die();
					}

					$CMD = "bash /servers/fivem-$SID/fx-server/run.sh +exec server.cfg +set onesync legacy";
					
					exec_server_cmd("sed -i '/endpoint_add_tcp/d' /servers/fivem-$SID/fx-server-data/server.cfg", $SERVER);
					exec_server_cmd("sed -i '/endpoint_add_udp/d' /servers/fivem-$SID/fx-server-data/server.cfg", $SERVER);
					exec_server_cmd("sed -i '/sv_hostname/d' /servers/fivem-$SID/fx-server-data/server.cfg", $SERVER);
					exec_server_cmd("sed -i '/rcon_password/d' /servers/fivem-$SID/fx-server-data/server.cfg", $SERVER);
					exec_server_cmd("sed -i '/sv_maxclients/d' /servers/fivem-$SID/fx-server-data/server.cfg", $SERVER);
					exec_server_cmd("sed -i '/sv_licenseKey/d' /servers/fivem-$SID/fx-server-data/server.cfg", $SERVER);
					
					exec_server_cmd('printf "\nendpoint_add_tcp \"0.0.0.0:' . $row["port"] . '\"" >> /servers/fivem-' . $SID . '/fx-server-data/server.cfg', $SERVER);
					exec_server_cmd('printf "\nendpoint_add_udp \"0.0.0.0:' . $row["port"] . '\"" >> /servers/fivem-' . $SID . '/fx-server-data/server.cfg', $SERVER);
					exec_server_cmd('printf "\nsv_hostname \"' . $SERVERINFO["sv_hostname"] . '"\" >> /servers/fivem-' . $SID . '/fx-server-data/server.cfg', $SERVER);
					exec_server_cmd('printf "\nrcon_password \"' . $SERVERINFO["rcon_password"] . '"\" >> /servers/fivem-' . $SID . '/fx-server-data/server.cfg', $SERVER);
					exec_server_cmd('printf "\nsv_maxclients ' . $SERVERINFO["slots"] . '" >> /servers/fivem-' . $SID . '/fx-server-data/server.cfg', $SERVER);
					exec_server_cmd('printf "\nsv_licenseKey ' . $SERVERINFO["sv_licenseKey"] . '" >> /servers/fivem-' . $SID . '/fx-server-data/server.cfg', $SERVER);
					
					exec_server_cmd("sed -i '/^$/d' /servers/fivem-$SID/fx-server-data/server.cfg", $SERVER); // delete whitespaces
					
					exec_server_cmd('mv /servers/' . $ID . '/fx-server-data/screenlog.0 /servers/' . $ID . '/fx-server-data/log_' . date("Ymd-Hms", microtime(true)) . ".log", $SERVER); // Backup old log
					exec_server_cmd('cd /servers/' . $ID . '/fx-server-data && sudo -u ' . $USERNAME . ' screen -dmSL ' . $ID . ' ' . $CMD, $SERVER);
					exec_server_cmd('sudo -u ' . $USERNAME . ' screen -r ' . $ID . ' -X colon "logfile flush 1^M"', $SERVER);
					echo 'cd /servers/' . $ID . '/fx-server-data && sudo -u ' . $USERNAME . ' screen -dmSL ' . $ID . ' ' . $CMD;
					break;
			}
			$PID = intval(exec_server_cmd("sudo -u $USERNAME screen -ls | grep $ID | cut -d. -f1", $SERVER));
			log_to_file("[START] $USERNAME started server $ID, pid: $PID");
			$con->query("UPDATE servers SET pid = '$PID' WHERE serverid = '$SID'");
			if($PID > 0)
				echo '[false, "El servidor se ha iniciado correctamente", true]';
			else
				echo '[true, "Error al iniciar el servidor", false]';
		}
		elseif(intval($row["pid"]) == -740)
			echo '[true, "El servidor se está actualizando", false]';
		else
			echo '[true, "El servidor ya se encuentra iniciado", true]';
	}
	else
		echo '[true, "ERR-205 - Intentalo nuevamente por favor", false]';
	mysqli_close($con);
?>