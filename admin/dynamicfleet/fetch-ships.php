<?php
	include "../../support/control.php";
	// $returnUrl = "admin/dynamicfleet/";
	// $prefix = "../";
	// include $prefix.'header.php';
	
	$shipsArray = array();
	
	if(isset($_GET['q'])){
		$queryName = "SELECT * FROM $db.shipsData WHERE shipName LIKE '%".$_GET['q']."%'";
		$queryManufacturer = "SELECT * FROM $db.shipsData WHERE manufac LIKE '%".$_GET['q']."%'";
		$queryManufacturerAcronym = "SELECT * FROM $db.shipsData WHERE manufacShortName LIKE '%".$_GET['q']."%'";
		
		$shipsName = $conn->query($queryName);
		$shipsManufacturer = $conn->query($queryManufacturer);
		$shipsManufacturerAcronym = $conn->query($queryManufacturerAcronym);
		
		if(mysqli_num_rows($shipsName)==0
			&& mysqli_num_rows($shipsManufacturer)==0
			&& mysqli_num_rows($shipsManufacturerAcronym)==0){
				$shipsArray = array("notFound"=>true);
		}else{
			if(mysqli_num_rows($shipsName)>0){
				while($row = mysqli_fetch_assoc($shipsName)){
					if(searchShips($shipsArray, $row['shipName'])==-1){
						array_push($shipsArray,createShipObj($row));
					}
				}
			}
			
			if(mysqli_num_rows($shipsManufacturer)>0){
				while($row = mysqli_fetch_assoc($shipsManufacturer)){
					if(searchShips($shipsArray, $row['shipName'])==-1){
						array_push($shipsArray,createShipObj($row));
					}
				}
			}
			
			if(mysqli_num_rows($shipsManufacturerAcronym)>0){
				while($row = mysqli_fetch_assoc($shipsManufacturerAcronym)){
					if(searchShips($shipsArray, $row['shipName'])==-1){
						array_push($shipsArray,createShipObj($row));
					}
				}
			}
		}
	}else{
		$query = "SELECT * FROM $db.shipsData";
		$ships = $conn->query($query);
		if (mysqli_num_rows($ships) > 0) {
			while($row = mysqli_fetch_assoc($ships)){
				$ship = createShipObj($row);
				array_push($shipsArray,$ship);
			}
		}else
			$shipsArray = array("notFound"=>true);
	}
	
	echo(json_encode($shipsArray));
	
	function createShipObj($row){
		return array(
					"shipName" => $row['shipName'],
					"manufacturer" => $row['manufac'],
					"manufacturerAcronym" => $row['manufacShortName'],
					"shipRole" => $row['shipRole'],
					"shipCrew" => $row['shipCrew'],
					"shipLength" => $row['shipLength'],
					"shipMass" => $row['shipMass'],
					"rsiUrl" => $row['rsiUrl'],
					"shipPrice" => $row['shipPrice']
					);
	}
	
	function searchShips($array, $shipName){
		$index = -1;
		for($i=0;$i<count($array);$i++){
			if(strcmp($array[$i]['shipName'],$shipName)==0){
				$index = $i;
				break;
			}
		}
		return $index;
	}
?>