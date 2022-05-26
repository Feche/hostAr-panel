<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();
	
	if(!isset($_SESSION["username"]))
		header("Location: ../../login.php");
	
	include "../../scripts/include/file_utils.inc";
	
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["v"];
	
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT node, port, servertp, pid FROM servers WHERE serverid = '$SID' && username = '$USERNAME'");
	$row = $result->fetch_assoc();
	if($row && $row["pid"] != 0)
	{
		switch($row["servertp"])
		{
			case "csgo":
			case "cs16":
				echo '
					<div class="players-flexbox" style="font-weight: bold;">
						<div>Nombre</div>
						<div>Puntaje</div>
						<div>Tiempo online</div>
					</div>
				';
				
				$PLAYERS = get_player_list($row["node"], $row["port"], $row["servertp"]);
				
				foreach($PLAYERS as $player)
				{
					echo '
						<div class="players-flexbox">
							<div>' . $player["Name"] . '</div>
							<div>' . $player["Frags"] . '</div>
							<div>' . $player["TimeF"] . '</div>
						</div>
					';
				}
				break;
			case "fivem":
				echo '
					<div class="players-flexbox" style="font-weight: bold;">
						<div>ID</div>
						<div>Nombre</div>
						<div>Ping</div>
					</div>
				';
				
				$PLAYERS = get_player_list($row["node"], $row["port"], $row["servertp"]);
				
				foreach($PLAYERS as $player)
				{
					echo '
						<div class="players-flexbox">
							<div>' . $player["id"] . '</div>
							<div>' . $player["name"] . '</div>
							<div>' . $player["ping"] . 'ms</div>
						</div>
					';
				}
				break;
			default:
				break;
		}
	}
?>