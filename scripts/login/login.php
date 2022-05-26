<?php
	include "../include/log_utils.inc";
	
	if(!isset($_POST["username"]) || !isset($_POST["password"]))
		header("Location: ../../login.php");
	
	if(preg_match("/[^A-Za-z0-9]/", $_POST["username"]) || preg_match("/[^A-Za-z0-9]/", $_POST["password"]))
		return false;
	
	//echo "En mantenimiento";
	//return false;

	$con = new mysqli("127.0.0.1", "database", "password", "username");
	if($con->connect_error) 
	{
		echo "ERR-200 - Conexión a base de datos fallida";
		die();
	}
	$username = mysqli_real_escape_string($con, $_POST["username"]);
	$password = mysqli_real_escape_string($con, $_POST["password"]);
	$useragent = mysqli_real_escape_string($con, $_POST["user-agent"]);
	
	if($username == "demo") 
	{
		echo "En mantenimiento";
		return false;
	}
	
	$sql = "SELECT password, servers FROM accounts WHERE username = '$username'";
	$result = $con->query($sql);
	$row = $result->fetch_assoc();
	if($row)
	{
		if($row["password"] == $password)
		{
			session_start();
			$_SESSION["username"] = $username;
			log_to_file("[AUTH] ip " . getUserIpAddr() . " logged in as '" . $username . ":" . $password . "' ($useragent)");
			echo $row["servers"];
		}
		else
		{
			log_to_file("[AUTH ERROR] ip " . getUserIpAddr() . " failed to login as '" . $username . ":" . $password . "'");
			echo "Nombre de usuario o contraseña incorrectos";
		}
	}
	else
	{
		log_to_file("[AUTH ERROR] ip " . getUserIpAddr() . " failed to login as '" . $username . ":" . $password . "'");
		echo "Nombre de usuario o contraseña incorrectos";
	}
	mysqli_close($con);
?>