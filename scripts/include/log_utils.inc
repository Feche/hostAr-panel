<?php
	$LOGFILE = "/var/www/html/panel.host-ar.com.ar/logs/auth.log";
	
	$LOG_PATH = array("mtasa" => "mods/deathmatch/logs/server.log", 
					   "samp" => "server_log.txt",
					   "csgo" => "screenlog.0",
					   "cs16" => "screenlog.0",
					   "fivem" => "fx-server-data/screenlog.0"
	);

	function log_to_file($string)
	{
		global $LOGFILE;
		$date = new DateTime();
		$date = $date->format("d/m/y H:i:s");
		$file = fopen($LOGFILE, "a");
		fwrite($file, "[" . $date . "]" . $string . "\r\n");
		fclose($file);
	}
	
	function getUserIpAddr()
	{
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function read_server_log($SID, $TP, $SERVER, $LINES)
	{
		global $LOG_PATH;
		//$CMD = "cat /servers/" . $TP . "-" . $SID . "/" . $LOG_PATH[$TP]; 
		$CMD = "tail -n $LINES /servers/" . $TP . "-" . $SID . "/" . $LOG_PATH[$TP];
		$OUTPUT;
		exec("sudo ssh root@$SERVER '$CMD' 2>&1", $OUTPUT);
		return $OUTPUT;
	}
	
	function write_to_server_log($STRING, $TP, $SID, $SERVER)
	{
		global $LOG_PATH;
		exec_server_cmd("echo '$STRING' >> /servers/$SID/" . $LOG_PATH[$TP], $SERVER);
	}
?>