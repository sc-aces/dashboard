<?php
	date_default_timezone_set('UTC');
	session_start();
	$debug = false;
	$currentTime = date("Y/m/d H:i:s");
	$currentDate = date("Y/m/d");
	if(!$debug)
		error_reporting(0);
	
	//Sanitize all input
	$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
	$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	
	$serverTime = date('Y-m-d H:m:s a',time());
	
	$defaultTheme = 'light';
	
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
	
	function convertBytes( $value ) {
		if ( is_numeric( $value ) ) {
			return $value;
		} else {
			$value_length = strlen($value);
			$qty = substr( $value, 0, $value_length - 1 );
			$unit = strtolower( substr( $value, $value_length - 1 ) );
			switch ( $unit ) {
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}
			return $qty;
		}
	}
	$maxFileSize = convertBytes(ini_get('upload_max_filesize'));
	
	function uploadFile($target_dir, $files, $fileType, $uploadedFileName){
		global $maxFileSize;
		
		$target_file = $target_dir . $uploadedFileName .".". $fileType;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$error = -1;
		// Check if image file is a actual image or fake image
		$check = getimagesize($files["fileToUpload"]["tmp_name"]);
		if($check !== false)
			$error = 0;
		else 
			$error = 1;
		// Check if file already exists
		if (file_exists($target_file)) {$error = 2;	}
		// Check file size
		if ($files["fileToUpload"]["size"] > $maxFileSize) {$error = 3;}
		// Allow certain file formats
		if($imageFileType != $fileType) {$error = 4;}
		// Check if $error is set to 0 by an error
		if ($error != 0) {
			switch($error){
				case 1:	$alert .= createAlert("danger","File must be an image."); break;
				case 2: $alert .= createAlert("danger","File already exists."); break;
				case 3: $alert .= createAlert("danger","File is too large."); break;
				case 4: $alert .= createAlert("danger","File must of the file type: <strong>$fileType</strong>."); break;
				default: $alert .= createAlert("danger","Unknown error: $error");
			}
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($files["fileToUpload"]["tmp_name"], $target_file)) {
				//File upload successful
				return true;
			} else {
				$alert .= createAlert("danger","An error occured while attempting the upload. Please try again.");
			}
		}
		return false;
	}
?>