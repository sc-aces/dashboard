<?php
	include "../../support/control.php";
	
	$elections = array();
	//*
	//* structure for final element in $elections array
	/*
		array(
			"id" => $sqlRow["id"],
			"type" => $sqlRow["type"],
			"name" => $sqlRow["name"],
			"status" => $sqlRow["status"],
			"description" => $sqlRow["description"],
			"adminNotes" => $sqlRow["admin_notes"],
			"accessTags" => $sqlRow["access_tags"],
			"start" => $sqlRow["start"],
			"end" => $sqlRow["end"],
			"electionGeneric" => array(
					array(
						"id" => election_generic['id'],
						"election_id" => election_generic['election_id'],
						"name" => election_generic['name'],
						"canidates" => election_generic['canidates'],
						"votes" => election_generic['votes'],
						),
					array(
						"id" => election_generic['id'],
						"election_id" => election_generic['election_id'],
						"name" => election_generic['name'],
						"canidates" => election_generic['canidates'],
						"votes" => election_generic['votes'],
						),
					...
				)
		)
	*/
	if(isset($_GET['status'])){
		$query = "SELECT * FROM $db.elections WHERE status='".$_GET['status']."'";
	}elseif(isset($_GET['q'])){
		$query = "SELECT * FROM $db.elections WHERE name LIKE '%".$_GET['q']."%'";
	}elseif(isset($_GET['o'])){
		$query = "SELECT * FROM $db.elections ORDER BY ".$_GET['o'];
	}elseif(isset($_GET['o']) && isset($_GET['q'])){
		$query = "SELECT * FROM $db.elections WHERE name LIKE '%".$_GET['q']."%' ORDER BY ".$_GET['o'];
	}else{
		$query = "SELECT * FROM $db.elections";
	}
	
	$result = $conn->query($query);
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_assoc($result)){
			$electionGenericQuery = $conn->query("SELECT * FROM $db.election_generic WHERE election_id=".$row['id']);
			$electionGeneric = array("election_generic"=>array());
			if(mysqli_num_rows($electionGenericQuery)>0){
				while($electionGenericRow = mysqli_fetch_assoc($electionGenericQuery)){
					array_push($electionGeneric['election_generic'],$electionGenericRow);
				}
			}
			if($row['status'] != "done" && $row['status'] != "deleted" && $row['status'] != "stopped"){
				if($currentTime >= date($row['end'])){
					//election end date has passed. Set to correct status
					$row['status'] = "done";
					$resultUpdate = $conn->query("UPDATE $db.elections SET `status`='done', `last_status`='done', `last_status_date`='$currentTime' WHERE `id`='".$row['id']."'");
				}
				elseif($currentTime >= date($row['start']) && $currentTime <= date($row['end'])){
					//election start date has passed. Set to correct status
					$row['status'] = "active";
					$resultUpdate = $conn->query("UPDATE $db.elections SET `status`='active' WHERE `id`='".$row['id']."'");
				}
				else{
					//election start date is in the future. Set to correct status
					$row['status'] = "upcoming";
					$resultUpdate = $conn->query("UPDATE $db.elections SET `status`='upcoming' WHERE `id`='".$row['id']."'");
				}
			}
			// echo "<pre>";
			// var_dump($currentTime);
			// var_dump(date($row['start']));
			// echo "<strong>Active</strong> $currentTime >= ".date($row['start'])."\t"; var_dump($currentTime >= date($row['start']));
			// echo "<strong>Active</strong> $currentTime <= ".date($row['end'])."\t"; var_dump($currentTime <= date($row['end']));
			// var_dump(date($row['end']));
			// echo "<strong>Done</strong> $currentTime >= ".date($row['end'])."\t"; var_dump($currentTime >= date($row['end']));
			// var_dump($row);
			// echo "</pre>";
			
			array_push($elections, array_merge($row,$electionGeneric));
		}
		if(isset($_GET['status'])){
			for($i=0; $i<count($elections); $i++){
				$accessTags = explode(",",$elections[$i]['access_tags']);
				if(!(!empty(array_intersect($accessTags, $_SESSION['tags'])))){
					array_splice($elections,$i,$i);
				}
			}
		}
		echo json_encode($elections);
	}else{
		echo '[{"status":"error"}]';
	}
?>