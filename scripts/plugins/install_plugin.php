<?php
	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	
	include "../include/file_utils.inc";
	include "../include/log_utils.inc";

	session_start();
	
	if(!isset($_SESSION["username"]))
		header("Location: login.php");
	
	$USERNAME = $_SESSION["username"];
	$SID = $_GET["sid"];
	$PLUGIN = $_GET["plugin"];
	
	if($USERNAME == "demo")
	{
		echo '[true, "Operación no permitida"]';
		die();
	}
	
	$con = new mysqli("127.0.0.1", "database", "password", "username");
	$result = $con->query("SELECT * FROM servers WHERE serverid = '$SID' && username = '$USERNAME'");
	$row = $result->fetch_assoc();
	if($row)
	{
		if($row["pid"] > 0)
		{
			echo '[true, "El servidor debe estar apagado!"]';
			die();
		}
		
		$SERVERINFO = json_decode($row["settings"], true);
		$SERVER = $row["node"];

		switch($PLUGIN)
		{
			/* ----------------- CS 1.6 ----------------- */
			
			/* AMX Mod X 1.8.2 */
			case "amxmodx":
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/amxmodx /servers/cs16-$SID/cstrike/addons/", $SERVER);
				/* Configure metamod plugins.ini */
				exec_server_cmd("sed -i '/amxmodx_mm_i386/d' /servers/cs16-$SID/cstrike/addons/metamod/plugins.ini", $SERVER);
				exec_server_cmd('printf "\nlinux addons/amxmodx/dlls/amxmodx_mm_i386.so" >> /servers/cs16-' . $SID . '/cstrike/addons/metamod/plugins.ini', $SERVER);
				
				echo '[false, "AMX Mod X v1.8.2 instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* SXe Injected 17.1 */
			case "sxei":
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/sxei /servers/cs16-$SID/cstrike/addons/", $SERVER);
				/* Configure metamod plugins.ini */
				exec_server_cmd("sed -i '/sxei_mm_i386/d' /servers/cs16-$SID/cstrike/addons/metamod/plugins.ini", $SERVER);
				exec_server_cmd('printf "\nlinux addons/sxei/dlls/sxei_mm_i386.so" >> /servers/cs16-' . $SID . '/cstrike/addons/metamod/plugins.ini', $SERVER);
				/* Configure server.cfg */
				exec_server_cmd("sed -i '/ip/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
				exec_server_cmd('printf "\nip 127.0.0.1" >> /servers/cs16-' . $SID . '/cstrike/server.cfg', $SERVER);
				
				echo '[false, "SXe Injected 17.1 instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* Mix Maker v9.6 */
			case "mixmaker":
				/* Check AMX Mod X presence */
				if(!check_amxmodx($SID, $SERVER))
				{
					echo '[true, "Primero instalá AMX Mod X para poder instalar este plugin."]';
					return false;
				}
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/mixmaker/* /servers/cs16-$SID/", $SERVER);
				/* Configure server.cfg */
				exec_server_cmd("sed -i '/mixm_enable/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
				exec_server_cmd('printf "\nmixm_enable 0" >> /servers/cs16-' . $SID . '/cstrike/server.cfg', $SERVER);
				/* Configure amxmodx plugins.ini */
				exec_server_cmd("sed -i '/Mix_Maker/d' /servers/cs16-$SID/cstrike/addons/amxmodx/configs/plugins.ini", $SERVER);
				exec_server_cmd('printf "\nMix_Maker.amxx" >> /servers/cs16-' . $SID . '/cstrike/addons/amxmodx/configs/plugins.ini', $SERVER);
				
				echo '[false, "Mix Maker v9.6 instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* VoiceTranscoder 2017RC5 */
			case "vtc":
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/vtc/ /servers/cs16-$SID/cstrike/addons/", $SERVER);
				/* Configure metamod plugins.ini */
				exec_server_cmd("sed -i '/vtc_i386/d' /servers/cs16-$SID/cstrike/addons/metamod/plugins.ini", $SERVER);
				exec_server_cmd('printf "\nlinux addons/vtc/dlls/vtc_i386.so" >> /servers/cs16-' . $SID . '/cstrike/addons/metamod/plugins.ini', $SERVER);
				/* Configure server.cfg */
				exec_server_cmd("sed -i '/sv_voicequality/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
				exec_server_cmd("sed -i '/sv_voiceenable/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
				exec_server_cmd("sed -i '/sv_voicecodec/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
				exec_server_cmd('printf "\nsv_voicequality 5" >> /servers/cs16-' . $SID . '/cstrike/server.cfg', $SERVER);
				exec_server_cmd('printf "\nsv_voiceenable 1" >> /servers/cs16-' . $SID . '/cstrike/server.cfg', $SERVER);
				exec_server_cmd('printf "\nsv_voicecodec voice_speex" >> /servers/cs16-' . $SID . '/cstrike/server.cfg', $SERVER);
				
				echo '[false, "VoiceTranscoder 2017RC5 instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* Anti-spam */
			case "antispam":
				/* Check AMX Mod X presence */
				if(!check_amxmodx($SID, $SERVER))
				{
					echo '[true, "Primero instalá AMX Mod X para poder instalar este plugin."]';
					return false;
				}
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/antispam/* /servers/cs16-$SID/cstrike/addons/amxmodx/plugins/", $SERVER);
				/* Configure amxmodx plugins.ini */
				exec_server_cmd("sed -i '/anti-spam/d' /servers/cs16-$SID/cstrike/addons/amxmodx/configs/plugins.ini", $SERVER);
				exec_server_cmd('printf "\nanti-spam.amxx" >> /servers/cs16-' . $SID . '/cstrike/addons/amxmodx/configs/plugins.ini', $SERVER);
				
				echo '[false, "Anti-spam instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* GunGame */
			case "gungame":
				/* Check AMX Mod X presence */
				if(!check_amxmodx($SID, $SERVER))
				{
					echo '[true, "Primero instalá AMX Mod X para poder instalar este plugin."]';
					return false;
				}
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/gungame/* /servers/cs16-$SID/cstrike/", $SERVER);
				/* Configure amxmodx plugins.ini */
				exec_server_cmd("sed -i '/gungame/d' /servers/cs16-$SID/cstrike/addons/amxmodx/configs/plugins.ini", $SERVER);
				exec_server_cmd('printf "\ngungame.amxx" >> /servers/cs16-' . $SID . '/cstrike/addons/amxmodx/configs/plugins.ini', $SERVER);
				
				echo '[false, "Gungame instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* Datear 5ya */
			case "5ya";
				/* Check AMX Mod X presence */
				if(!check_amxmodx($SID, $SERVER))
				{
					echo '[true, "Primero instalá AMX Mod X para poder instalar este plugin."]';
					return false;
				}
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/datear/* /servers/cs16-$SID/cstrike/addons/amxmodx/", $SERVER);
				/* Configure amxmodx plugins.ini */
				exec_server_cmd("sed -i '/datear/d' /servers/cs16-$SID/cstrike/addons/amxmodx/configs/plugins.ini", $SERVER);
				exec_server_cmd('printf "\ndatear.amxx" >> /servers/cs16-' . $SID . '/cstrike/addons/amxmodx/configs/plugins.ini', $SERVER);
				
				echo '[false, "Datear 5Ya instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* Pack mapas */
			case "packmapas":
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/maps/* /servers/cs16-$SID/", $SERVER);
				
				echo "Pack de mapas instalado, reiniciá el servidor para que tome efecto.";
				break;
			/* Advanced Bullet Damage */
			case "abd":
				/* Check AMX Mod X presence */
				if(!check_amxmodx($SID, $SERVER))
				{
					echo '[true, "Primero instalá AMX Mod X para poder instalar este plugin."]';
					return false;
				}
				/* Copy files */
				exec_server_cmd("cp -rf /defaults/plugins/cs16/advancedbulltetdamage/* /servers/cs16-$SID/cstrike/addons/amxmodx/plugins/", $SERVER);
				/* Configure amxmodx plugins.ini */
				exec_server_cmd("sed -i '/advanced/d' /servers/cs16-$SID/cstrike/addons/amxmodx/configs/plugins.ini", $SERVER);
				exec_server_cmd('printf "\nadvanced_bullet_damage.amxx" >> /servers/cs16-' . $SID . '/cstrike/addons/amxmodx/configs/plugins.ini', $SERVER);
				
				echo '[false, "Advanced Bullet Damage instalado, reiniciá el servidor para que tome efecto."]';
				break;
			/* ----------------- CS GO ----------------- */
			
			/* SteamCMD update */
			case "csgoupdate":
				if($row["pid"] == -740)
				{
					echo '[true, "El servidor ya se está actualizando"]';
					die();
				}
				
				session_write_close();
				echo '[false, "Servidor actualizándose, puede demorar hasta 10 minutos en finalizar"]';
				fastcgi_finish_request();
				
				$con->query("UPDATE servers SET pid = '-740' WHERE serverid = '$SID' && username = '$USERNAME'");
				
				exec_server_cmd("cd /defaults/default_csgo/steamcmd/ && ./steamcmd.sh +runscript update.bat", $SERVER);
				
				exec_server_cmd("chattr -i /servers/csgo-$SID/srcds_run", $SERVER);
				exec_server_cmd("chattr -i /servers/csgo-$SID/srcds_linux", $SERVER);
				exec_server_cmd("chattr -i /servers/csgo-$SID/bin", $SERVER);
				exec_server_cmd("chattr -i /servers/csgo-$SID/csgo/bin", $SERVER);

				exec_server_cmd("cp -rf /defaults/default_csgo/* /servers/csgo-$SID/", $SERVER);
				
				exec_server_cmd("chown -R $USERNAME:$USERNAME /servers/csgo-$SID/", $SERVER);
				exec_server_cmd("chmod -R 0755 /servers/csgo-$SID", $SERVER);
				
				exec_server_cmd("chattr +i /servers/csgo-$SID/srcds_run", $SERVER);
				exec_server_cmd("chattr +i /servers/csgo-$SID/srcds_linux", $SERVER);
				exec_server_cmd("chattr +i /servers/csgo-$SID/bin", $SERVER);
				exec_server_cmd("chattr +i /servers/csgo-$SID/csgo/bin", $SERVER);
				
				$con->query("UPDATE servers SET pid = '0' WHERE serverid = '$SID' && username = '$USERNAME'");
				break;
			/* Reset server */
			case "resetear":				
				switch($row["servertp"])
				{
					case "cs16":
						/* Copy files */
						exec_server_cmd("chattr -i /servers/cs16-$SID/hlds_run", $SERVER);
						exec_server_cmd("chattr -i /servers/cs16-$SID/hlds_linux", $SERVER);
						
						exec_server_cmd("rm -rf /servers/cs16-$SID/*", $SERVER);
						exec_server_cmd("cp -rf /defaults/default_cs16/* /servers/cs16-$SID/", $SERVER);
						
						exec_server_cmd("chown -R $USERNAME:$USERNAME /servers/cs16-$SID/", $SERVER);
						exec_server_cmd("chmod -R 0755 /servers/cs16-$SID/", $SERVER);
						
						exec_server_cmd("chattr +i /servers/cs16-$SID/hlds_run", $SERVER);
						exec_server_cmd("chattr +i /servers/cs16-$SID/hlds_linux", $SERVER);
						
						exec_server_cmd("sed -i '/hostname/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
						exec_server_cmd('printf "\nhostname \"' . $SERVERINFO["hostname"] . '\"" >> /servers/cs16-' . $SID . '/cstrike/server.cfg', $SERVER);
						exec_server_cmd("sed -i '/^$/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
						
						exec_server_cmd("sed -i '/^$/d' /servers/cs16-$SID/cstrike/addons/metamod/plugins.ini", $SERVER);
						exec_server_cmd("sed -i '/^$/d' /servers/cs16-$SID/cstrike/server.cfg", $SERVER);
						break;
					case "fivem":
						exec_server_cmd("chattr -i /servers/fivem-$SID/fx-server/run.sh", $SERVER);
						
						exec_server_cmd("rm -rf /servers/fivem-$SID/*", $SERVER);
						exec_server_cmd("cp -rf /defaults/default_fivem/* /servers/fivem-$SID/", $SERVER);
						
						exec_server_cmd("chown -R $USERNAME:$USERNAME /servers/fivem-$SID/", $SERVER);
						exec_server_cmd("chmod -R 0755 /servers/fivem-$SID/", $SERVER);
						
						exec_server_cmd("chattr +i /servers/fivem-$SID/fx-server/run.sh", $SERVER);
					case "csgo":
						exec_server_cmd("chattr -i /servers/csgo-$SID/srcds_run", $SERVER);
						exec_server_cmd("chattr -i /servers/csgo-$SID/srcds_linux", $SERVER);
						exec_server_cmd("chattr -i /servers/csgo-$SID/bin", $SERVER);
						exec_server_cmd("chattr -i /servers/csgo-$SID/csgo/bin", $SERVER);
						
						exec_server_cmd("rm -rf /servers/csgo-$SID/*", $SERVER);
						exec_server_cmd("cp -rf /defaults/default_csgo/* /servers/csgo-$SID/", $SERVER);
						
						exec_server_cmd("chown -R $USERNAME:$USERNAME /servers/csgo-$SID/", $SERVER);
						exec_server_cmd("chmod -R 0755 /servers/csgo-$SID", $SERVER);
						
						exec_server_cmd("chattr +i /servers/csgo-$SID/srcds_run", $SERVER);
						exec_server_cmd("chattr +i /servers/csgo-$SID/srcds_linux", $SERVER);
						exec_server_cmd("chattr +i /servers/csgo-$SID/bin", $SERVER);
						exec_server_cmd("chattr +i /servers/csgo-$SID/csgo/bin", $SERVER);
						break;
				}
				
				echo '[false, "Servidor reinstalado correctamente."]';
				break;
			default:
				echo '[true, "ERR-24 - Intentalo nuevamente por favor"]';
				break;
		}
		exec_server_cmd("chown -R $USERNAME:$USERNAME /servers/" . $row["servertp"] . "-$SID/", $SERVER);
		exec_server_cmd("chmod -R 0755 /servers/" . $row["servertp"] . "-$SID", $SERVER);
		log_to_file("[PLUGIN] $USERNAME installed plugin: $PLUGIN");
	}
	else
		echo '[true, "ERR-404 - Sin permisos"]';
	
	function check_amxmodx($SID, $SERVER)
	{	
		$CMD = exec_server_cmd("cd /servers/cs16-$SID/cstrike/addons/amxmodx/", $SERVER);
		return (strlen($CMD) > 0 ? false : true);
	}
?>