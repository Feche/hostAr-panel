<?php
	session_start();
	
	if(!isset($_SESSION["username"]))
		header("Location: ../../login/login.php");
	
	include "../../scripts/include/file_utils.inc";
	include "../../scripts/include/log_utils.inc";
	
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["sid"];
	
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
		if(intval($row["pid"]) > 0)
		{
			$ID = $row["servertp"] . "-" . $SID;
			$SERVER = $row["node"];
			
			$TOKILL = exec_server_cmd("sudo -u $USERNAME ps h --ppid " . $row["pid"] . " -o pid", $SERVER);
			exec_server_cmd("sudo -u $USERNAME kill $TOKILL", $SERVER);
			$con->query("UPDATE servers SET pid = '0' WHERE serverid = '$SID'");
			
			write_to_server_log("Servidor detenido desde el panel.", $row["servertp"], $ID, $SERVER);
			log_to_file("[STOP] $USERNAME stopped server $ID");
			
			echo '[false, "El servidor se ha detenido correctamente", false]';
			
			mysqli_close($con);
		}
		else
		{
			echo '[true, "El servidor ya se encuentra detenido", false]';
		}
	}
	else
	{
		echo '[true, "ERR-199 - Intentalo nuevamente por favor", false]';
	}
?>