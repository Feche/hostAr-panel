<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	session_start();

	if(!isset($_SESSION["username"]))
	{
		header("Location: ../login.php");
		die();
	}
?>

<div class="console-container">
	<center>
		<h1>Consola</h1>
		<p>Acá podrás ver en tiempo real, la consola del servidor,<br>también podes enviar comandos utilizando la caja de abajo.</p>
	</center>
	
	<div id="console-text"></div>
	<div id="console-players"></div>
	
	<!-- CMD flexbox -->
	<div id="console-flexbox">
		<div>
			<input type="text" id="command-input" style="width: 20em; height: 2em;">
		</div>
		<div>
			<div id="command-button" onclick="sendRCONCommand()">Enviar</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		consoleTimer = setInterval(function() {
			updateConsole(sID);
			
			// Scroll to bottom
			if(!scrolling)
				var element = document.getElementById("console-text");
				if(element)
					element.scrollTop = element.scrollHeight;
		}, 500);
	});

	function updateConsole(sid) {
		if(scrolling)
			return false;
		
		$.get("scripts/console/read_console_output.php?sid=" + sid, 
			function(response) {
				console.log(response);
				
				var obj = JSON.parse(response);
				$("#console-players").html(obj[0]);
				$("#console-text").html(obj[1]);
			}
		);
	}
	
	function sendRCONCommand() {
		var cmd = $("#command-input").val();
				
		if(cmd == "")
			return false;
		
		$.get("scripts/console/write_console_command.php?sid=" + sID + "&cmd=" + cmd, 
			function(response) { 
				console.log(response);
				
				var rData = JSON.parse(response);
				showNotification(rData[1], rData[0]);
				
				$("#command-input").val("");
			}
		);
	}
	
	var input = document.getElementById("command-input");
	// Execute a function when the user releases a key on the keyboard
	input.addEventListener("keyup", function(event) {
		// Number 13 is the "Enter" key on the keyboard
		if (event.keyCode === 13) {
			// Cancel the default action, if needed
			event.preventDefault();
			// Trigger the button element with a click
			document.getElementById("command-button").click();
		}
	});
	
	var scrolling = false;
	$("#console-text").scroll(function() {
		if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
			scrolling = false;
		} else {
			scrolling = true;
		}
	});
</script>