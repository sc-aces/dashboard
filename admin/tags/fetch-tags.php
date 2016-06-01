<?php
	include "../../support/control.php";
	$tags = array();
	if(isset($_GET['q'])){
		$query = "SELECT * FROM $db.tags WHERE name LIKE '%".$_GET['q']."%'";
	}else{
		$query = "SELECT * FROM $db.tags";
	}
	
	$result = $conn->query($query);
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_assoc($result)){
			array_push($tags, $row);
		}
		echo json_encode($tags);
	}else{
		echo '[{"status":"error"}]';
	}
?>