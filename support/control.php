<?php
	date_default_timezone_set('UTC');
	session_start();
	
	//Sanitize all input
	$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	
	$serverTime = date('Y-m-d H:m:s a',time());
	
	$defaultTheme = 'dark';
	
	if($requiresLogin){
		if(!isset($_SESSION['loggedIn'])){
			if(strlen($returnUrl)==0)
				header("Location: $prefix../login/");
			else
				header("Location: $prefix../login/?returnUrl=$returnUrl");
		}
	}
	
	//MySQL
	$server="acesfleetmysql.c2qiwft8er61.us-west-2.rds.amazonaws.com";
	$user= "fleetAdmin";
	$pass= "~ACESSQLP4ssW0rd!";
	$db = "fleet";
	
	// Create connection
	$conn = new mysqli($server, $user, $pass, $db);

	// Check connection
	if ($conn->connect_error) {
		$alert .= createAlert("danger","Failed to connect to database.");
		// die("Connection failed: " . $conn->connect_error);
	}


	function createAlert($type, $message){
		return "\t<div class=\"alert alert-$type\">$message<span class=\"alert-close\"><i class=\"fa fa-times fa-lg\"></i></span></div>\n";
	}
?>