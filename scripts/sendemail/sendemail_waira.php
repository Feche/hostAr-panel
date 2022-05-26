<?php
	$nombre = $_POST["nombre"] ?: "none";
	$email = $_POST["email"] ?:"none";
	$telefono = $_POST["telefono"] ?: "none";
	$comentarios = $_POST["comentarios"] ?: "none";
	
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else 
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$SERVER = "smtp.gmail.com";
	$FROM = $email;
	$TO = "ventas@wairacomunicaciones.com";
	$SUBJ = "'Solicitud de contacto - $nombre'";
	$MESSAGE = "'Nombre: $nombre\r\nEmail: $email\r\nTelefono: $telefono\r\nComentarios: $comentarios\r\nIP: $ip\r\nEND'";
	
	exec("sendemail -f $FROM -t $TO -u $SUBJ -s $SERVER -m $MESSAGE -xu email -xp password -o message-charset=utf-8 tls=yes message-content-type=text reply-to=$email", $output);
	
	/*foreach($output as $value)
		echo "$value<br>";*/
?>