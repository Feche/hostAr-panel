<?php
	include "../include/log_utils.inc";
	
	session_start();
	log_to_file("[AUTH] ip " . getUserIpAddr() . " logged out as " . $_SESSION["username"]);
	session_destroy();
	header("Location: ../../login.php");
?>