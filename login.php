<?php
	session_start();
	if(isset($_SESSION["username"]))
		header("Location: panel.php");
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=0.7">
		<meta name="robots" content="noindex, nofollow">
		<meta name="description" content="Panel de control - www.host-ar.com.ar"/>
		
		<link rel="stylesheet" type="text/css" href="login.css?rnd=<?php echo rand(0, 100); ?>" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"/>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="scripts/ua-parser.min.js"></script>
		
		<title>hostAR - Panel de control</title>
	</head>
	<body>
		<div class="login-container">
			<span style="font-size: 4.5em;">Login</span>
			<form action="scripts/login/login.php" method="post" style="margin-top: 0.5em;" autocomplete="on">
				<!-- USERNAME -->
				<div class="userpass-container">
					<i class="far fa-address-card userpass-icon"></i>
					<input type="input" id="username" name="username" placeholder="Usuario"/>
				</div>
				<!-- PASSWORD -->
				<div class="userpass-container">
					<i class="fas fa-key userpass-icon"></i>
					<input type="password" id="password" name="password" placeholder="Contraseña"/>
				</div>
				
				<!-- USER AGENT -->
				<input type="hidden" id="user-agent" name="user-agent"/>
				<!-- STATUS TEXT -->
				<div id="status-text">.</div>
				<!-- SUBMIT BUTTON -->
				<input type="submit" id="login" name="login" value="Iniciar sesión"/>
			</form>
		</div>
		
		<script>
			$(document).ready(function() {
				var parser = new UAParser();
				$("#user-agent").val(parser.getResult().browser.name + " " + parser.getResult().browser.version);
			});

			$("form").submit(function(event) {
				event.preventDefault();
				if (checkForm())
				{
					var post_url = $(this).attr("action");
					var request_method = $(this).attr("method");
					var form_data = new FormData(this);
					
					$.ajax({
						url : post_url,
						type: request_method,
						data : form_data,
						contentType: false,
						cache: false,
						processData: false,
						success:function(response){
							if(response == "Nombre de usuario o contraseña incorrectos") {
								$("#status-text").text(response);
								$("#status-text").css({ "color" : "red" });
								
							} else {
								window.location = "panel.php?v=" + JSON.parse(response)[0];
							}
						}
					});
				}
			});
			
			function checkForm() {
				$("#status-text").css("opacity", "1");
				
				setTimeout(function() { $("#status-text").css("opacity", "0"); }, 5000);
				
				if ($('#username').val() == "") {
					$("#status-text").text("Por favor, ingresá un usuario");
					$("#status-text").css({ "color" : "red" });
					return false;
				} else if ($('#password').val() == "") {
					$("#status-text").text("Por favor, ingresá una contraseña");
					$("#status-text").css({ "color" : "red" });
					return false;
				} else if (!alphanumeric($('#username').val()) || !alphanumeric($('#password').val())) {
					$("#status-text").text("Caracteres inválidos");
					$("#status-text").css({ "color" : "red" });
					return false;
				}
				return true;
			}
			
			function alphanumeric(inputtxt) { 
			  var letters = /^[0-9a-zA-Z]+$/;
			  if (letters.test(inputtxt)) {
				return true;
			  } else {
				return false;
			  }
			}
		</script>
	</body>
</html>