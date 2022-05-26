<?php
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);*/

	session_start();
	
	if(!isset($_SESSION["username"]))
		header("Location: ../../login.php");
	
	include "../../scripts/include/file_utils.inc";
	include "../../scripts/include/log_utils.inc";
	
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["sid"];
	
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT * FROM servers WHERE serverid = '$SID' && username = '$USERNAME'");
	$row = $result->fetch_assoc();
	if($row)
	{
		$SERVERINFO = json_decode($row["settings"], true);
		$LOG = read_server_log($SID, $row["servertp"], $row["node"], 100); // Keep under 150 lines to prevent console bugs
		$PLAYERS = "";
		
		$arrlog = array();
		foreach($LOG as $logline)
			$arrlog[] = $logline . "<br>";
		
		$ARRAY = array("Jugadores: " . get_player_count($row["node"], $row["port"], $row["servertp"]) . " / " . $SERVERINFO["slots"], $arrlog);
		echo json_encode($ARRAY);
	}
	else
		echo 'ERR-502 - Sin permisos';
?>