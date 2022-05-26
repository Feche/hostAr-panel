<?php
	include "../../scripts/include/file_utils.inc";

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();
	
	if(!isset($_SESSION["username"]))
		header("Location: ../../login/login.php");
	
	$USERNAME = $_SESSION["username"];
	$SID = $_POST["sid"];
	$DATA = json_decode($_POST["data"], true);
	
	$PATH = array("mtasa" => "/servers/mtasa-$SID/mods/deathmatch/mtaserver.conf", 
				   "samp" => "/servers/samp-$SID/server.cfg",
				   "cs16" => "/servers/cs16-$SID/cstrike/server.cfg",
				   "csgo" => "/servers/csgo-$SID/csgo/cfg/default.cfg",
				  "fivem" => "/servers/fivem-$SID/fx-server-data/server.cfg"
	);
	
	if($USERNAME == "demo")
	{
		echo '[true, "Operacion no permitida"]';
		die();
	}
	
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT * FROM servers WHERE username = '$USERNAME' && serverid = '$SID'");
	$row = $result->fetch_assoc();
	if($row)
	{
		$SERVERINFO = json_decode($row["settings"], true);
		$TP = $row["servertp"];
		$cfgpath = $PATH[$TP];
		
		$SERVER = $row["node"];
		
		switch($TP)
		{
			case "samp":
			case "mtasa":
				foreach($DATA as $key => $value)
				{
					$value = mysqli_real_escape_string($con, $value);
					$value = htmlentities($value);
					if($key == "slots")
					{
						if($value > $row["maxplayers"])
						{
							echo '[true, "ERROR: Configuración inválida, slots del servidor mayor a los permitidos"]';
							mysqli_close($con);
							die();
						}
					}
					$SERVERINFO[$key] = $value;	
				}
				//replace_settings($cfgpath, $SERVERINFO, $SERVER);
				break;
			case "csgo":
				foreach($DATA as $key => $value)
				{
					$value = mysqli_real_escape_string($con, $value);
					$value = htmlentities($value);
					if($key == "slots")
					{
						if($value > $row["maxplayers"])
						{
							echo '[true, "ERROR: Configuración inválida, slots del servidor mayor a los permitidos"]';
							mysqli_close($con);
							die();
						}
					}
					$SERVERINFO[$key] = $value;	
				}
				break;
			case "cs16":
				foreach($DATA as $key => $value)
				{
					$value = mysqli_real_escape_string($con, $value);
					$value = htmlentities($value);
					if($key == "slots")
					{
						if($value > $row["maxplayers"])
						{
							echo '[true, "ERROR: Configuración inválida, slots del servidor mayor a los permitidos"]';
							mysqli_close($con);
							die();
						}
					}
					else if($key == "hostname")
					{
						if(strlen($value) > 64)
						{
							echo '[true, "ERROR: Configuración inválida, nombre del servidor excede caracteres permitidos"]';
							mysqli_close($con);
							die();
						}
						exec_server_cmd("sed -i '/hostname/d' $cfgpath", $SERVER);
						exec_server_cmd('printf "\nhostname \"' . $value . '\"" >> ' . $cfgpath, $SERVER);
						exec_server_cmd("sed -i '/^$/d' $cfgpath", $SERVER);
					}
					else if($key == "rcon_password")
					{
						if(strlen($value) > 16)
						{
							echo '[true, "ERROR: Configuración inválida, contraseña RCON excede caracteres permitidos"]';
							mysqli_close($con);
							die();
						}
						exec_server_cmd("sed -i '/rcon_password/d' $cfgpath", $SERVER);
					}
					$SERVERINFO[$key] = $value;
				}
				break;
			case "fivem":
				foreach($DATA as $key => $value)
				{
				    $value = mysqli_real_escape_string($con, $value);
					$value = htmlentities($value);
					$value = htmlspecialchars($value);

					if($key == "slots")
					{
						if($value > $row["maxplayers"])
						{
							echo '[true, "ERROR: Configuración inválida, slots del servidor mayor a los permitidos"]';
							mysqli_close($con);
							die();
						}
					}
					else if($key == "sv_hostname")
					{
						if(strlen($value) > 512)
						{
							echo '[true, "ERROR: Configuración inválida, nombre del servidor excede caracteres permitidos"]';
							mysqli_close($con);
							die();
						}
					}
					else if($key == "rcon_password")
					{
						if(strlen($value) > 16)
						{
							echo '[true, "ERROR: Configuración inválida, contraseña RCON excede caracteres permitidos"]';
							mysqli_close($con);
							die();
						}
					}
					$SERVERINFO[$key] = $value;
				}
				break;
		}
		$ENCODE = json_encode($SERVERINFO);
		$con->query("UPDATE servers SET settings = '" . str_replace("\\", "\\\\", $ENCODE) . "' WHERE serverid = '$SID'");
		echo '[false, "Los cambios han sido guardados. Reiniciá servidor para que tomen efecto"]';
	}
	else
		echo '[true, "ERR-1020 - Intentalo nuevamente por favor"]';
	
	mysqli_close($con);
?>