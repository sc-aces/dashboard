<?php
	$request_headers = apache_request_headers();
	$http_origin = $request_headers;
	$allowed_http_origins = array("https://php-testbed-dlennox.c9users.io/");
	
	if(in_array($http_origin, $allowed_http_origins)){
		@header("Access-Control-Allow-Origin: " . $http_origin);
	}
	
	$server = "devideo-corp.com.mysql";
	$port= 3306;
	$user = "devideo_corp_co";
	$pass = "databasepass";
	$db = "devideo_corp_co";
	
	// Create connection
	$conn = new mysqli($server, $user, $pass, $db);

	// Check connection
	if ($conn->connect_error) {
		echo "FAILED TO CONNECT TO DATABASE";
		// die("Connection failed: " . $conn->connect_error);
	}
	$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
	
	if(isset($_GET['eID']) && isset($_GET['token'])){
		$_GET['eID'] += 0;
		
		$result = $conn->query("SELECT * FROM $db.userTokens WHERE `user_id`=".$_GET['eID']);
		if(mysqli_num_rows($result) > 0) {
			if($row = mysqli_fetch_assoc($result)){
				if($row['token'] == $_GET['token'])
					echo json_encode(array("status" => "success"));
				else
					echo json_encode(array("status" => "incorrect"));
			}
		}
	}else{
		echo json_encode(array("status" => "error"));
	}
?>