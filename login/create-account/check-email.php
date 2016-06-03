<?php
	include "../../support/control.php";
	$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
	if(isset($_GET['email'])){
		$result = $conn->query("SELECT email FROM $db.users WHERE `email`='".$_GET['email']."'");
		if(mysqli_num_rows($result)>0)
			echo json_encode(array('status'=>"taken"));
		else
			echo json_encode(array('status'=>"free"));
	}else
		echo json_encode(array('status'=>"error"));
?>