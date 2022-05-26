<?php
	header('X-Accel-Buffering: no');
	
	for ( $i = 0 ; $i < 1 ; $i++ ){
		echo "{\"code\":" . $i . "}\n";
		//SEND OUTPUT TO CLIENT
		flush();
		ob_flush();
		sleep(1);
	}
?>