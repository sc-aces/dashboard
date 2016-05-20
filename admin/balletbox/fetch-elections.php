<?php
	include "../../support/control.php";
	
	$elections = array();
	// structure for final element in $elections array
	// array(
		// "id" => $sqlRow["id"],
		// "type" => $sqlRow["type"],
		// "name" => $sqlRow["name"],
		// "status" => $sqlRow["status"],
		// "description" => $sqlRow["description"],
		// "adminNotes" => $sqlRow["admin_notes"],
		// "accessTags" => $sqlRow["access_tags"],
		// "start" => $sqlRow["start"],
		// "end" => $sqlRow["end"],
		// "electionGeneric" => array(
				// array(
					// "id" => election_generic['id'],
					// "election_id" => election_generic['election_id'],
					// "name" => election_generic['name'],
					// "canidates" => election_generic['canidates'],
					// "votes" => election_generic['votes'],
					// ),
				// array(
					// "id" => election_generic['id'],
					// "election_id" => election_generic['election_id'],
					// "name" => election_generic['name'],
					// "canidates" => election_generic['canidates'],
					// "votes" => election_generic['votes'],
					// ),
				// ...
			// )
	// )
	
	$result = $conn->query("SELECT * FROM $db.elections");
	if(mysqli_num_rows($result)>0){
		while($row = mysqli_fetch_assoc($result)){
			$electionGenericQuery = $conn->query("SELECT * FROM $db.election_generic WHERE election_id=".$row['id']);
			$electionGeneric = array("election_generic"=>array());
			if(mysqli_num_rows($electionGenericQuery)>0){
				while($electionGenericRow = mysqli_fetch_assoc($electionGenericQuery)){
					array_push($electionGeneric['election_generic'],$electionGenericRow);
				}
			}
			array_push($elections, array_merge($row,$electionGeneric));
		}
		echo json_encode($elections);
	}else{
		echo '[{"status":"error"}]';
	}

?>