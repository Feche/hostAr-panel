<?php	
	$TYPES = array("mtasa", "samp");
	
	$CONFIG_PATH = array("mtasa" => "mods/deathmatch/mtaserver.conf",
						  "samp" => "server.cfg"
	);
	
	$REPLACE = array("mtasa" => "mtaserver.conf", 
					  "samp" => "server.cfg"
	);
	
	function replace_settings($filepath, $settings, $server)
	{
		global $CONFIG_PATH;
		exec_server_cmd("chattr -i $filepath", $server);
		$TP = get_type($filepath);
		$file_contents = implode("", file("/defaults/default_$TP/" . $CONFIG_PATH[$TP]));
		switch($TP)
		{
			case "mtasa":
				foreach($settings as $key => $value)
				{
					if($key != "pid" and $key != "servertp" and $key != "ip")
					{
						if($key != "resource")
						{
							$file_contents = str_replace("<$key></$key>", "<$key>$value</$key>", $file_contents);
						}
						else
						{
							$file_contents = str_replace('<resource src="default" startup="1" protected="0"/>', '<resource src="' . $value . '" startup="1" protected="0"/>', $file_contents);
						}
					}
				}
				break;
			case "samp":
				foreach($settings as $key => $value)
				{
					if($key != "pid" and $key != "servertp" and $key != "ip")
					{
						$file_contents = str_replace("$key", "$key $value", $file_contents);
					}
				}
				break;
		}
		$file = fopen($filepath, "w");
		fwrite($file, $file_contents);
		fclose($file);
		exec_server_cmd("chattr +i $filepath", $server);
	}
	
	function get_type($filepath)
	{
		global $TYPES;
		foreach($TYPES as $type)
		{
			if(strpos($filepath, $type))
			{
				return $type;
			}
		}
		return "???";
	}
	
	function exec_server_cmd($cmd, $server)
	{
		return exec("sudo ssh root@$server '$cmd' 2>&1");
	}
	
	function is_server_online($pid, $server)
	{
		$str = shell_exec("sudo ssh root@$server 'ps -e | grep -w $pid'");
		if($str != "" and $pid != 0)
		{
			return true;
		}
		return false;
	}
	
	function random_string($length) 
	{
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) 
		{
			$key .= $keys[array_rand($keys)];
		}
		return $key;
	}
	
	function is_updating($sinfo)
	{
		if($sinfo["is_updating"])
		{	
			if((microtime(true) - $sinfo["tick"]) <= 600)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	function get_player_count($SERVER, $PORT, $TP)
	{
		$PLAYERS = "?";
		switch($TP)
		{
			case "csgo":
			case "cs16":
				$PLAYERS = json_decode(file_get_contents('http://10.10.11.201/tools/getplayers/getplayers.php?ip=' . $SERVER . '&port=' . $PORT), true);
				break;
			case "fivem":
				$PLAYERS = json_decode(file_get_contents("http://$SERVER:$PORT/players.json"), true);
				break;
		}
		return is_array($PLAYERS) ? sizeof($PLAYERS) : "?";
	}
	
	function get_player_list($SERVER, $PORT, $TP)
	{
		switch($TP)
		{
			case "csgo":
			case "cs16":
				$PLAYERS = json_decode(file_get_contents('http://10.10.11.201/tools/getplayers/getplayers.php?ip=' . $SERVER . '&port=' . $PORT), true);
				break;
			case "fivem":
				$CONTENT = file_get_contents("http://$SERVER:$PORT/players.json");
				if($CONTENT)
					$PLAYERS = json_decode(file_get_contents("http://$SERVER:$PORT/players.json"), true);
				break;
		}
		return $PLAYERS;
	}
?>