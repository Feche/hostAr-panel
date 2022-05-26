<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	if(!isset($_SESSION["username"]))
		header("Location: ../login.php");
	
	include "../../scripts/include/file_utils.inc";
	
	$USERNAME = $_SESSION["username"];
	
	if($USERNAME == "demo")
	{
		echo '[true, "Operacion no permitida"]';
		die();
	}
	
	$SID = $_GET["sid"];
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT * FROM servers WHERE username = '$USERNAME' && serverid = '$SID'");
	$row = $result->fetch_assoc();
	if($row)
	{
		if($row["servertp"] == "cs16" || $row["servertp"] == "fivem" || $row["servertp"] == "csgo" || $row["servertp"] == "mtasa")
		{
			$RCON_CMD = mysqli_real_escape_string($con, $_GET["cmd"]);
		
			if($row["pid"] > 0)
			{
				exec_server_cmd('sudo -u ' . $USERNAME . ' screen -S ' . $row["servertp"] . '-' . $SID . ' -p 0 -X stuff "' . $RCON_CMD . '^M"', $row["node"]);
				echo '[false, "Comando enviado"]';
			}
			else
				echo '[true, "El servidor no está encendido"]';
		}
		else
			echo '[true, "No es posible enviar comandos"]';
	}
	else
		header("Location: login.php");
?>