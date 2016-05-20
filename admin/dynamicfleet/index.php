<?php
	$requiresLogin = true;
	$returnUrl = "admin/dynamicfleet/";
	$prefix = "../";
	$pageName = "Dynamic Fleet";
	$customHeaderIcon = "<i class=\"fa fa-space-shuttle\"></i>";
	include $prefix.'header.php';
	
	if(count($_POST)>1 && $_POST['formName']!="add-ship-form"
		&& substr($_POST['formName'],0,7) != "delete-"
		&& substr($_POST['formName'],0,5) != "edit-"){
		var_dump($_POST);
	}
	
	if($_POST['formName']=="add-ship-form"){
		if(!$_POST['acronym'])
			$acronym = "NULL";
		else
			$acronym = "'".$_POST['acronym']."'";
		$queryResult = addShip($conn, $_POST['name'], $_POST['manufacturer'], $acronym, $_POST['role'], $_POST['crew'], $_POST['length'], $_POST['mass'], $_POST['url'], $_POST['price']);
	}
	elseif(substr($_POST['formName'],0,7) == "delete-" && $_POST['deleteShip'] == "on"){
		$existing = explode("-",$_POST['formName']);
		$existing = str_replace("__","-",$existing[1]);
		$existing = str_replace("_"," ",$existing);
		$queryResult = deleteShip($conn, $existing);
	}
	elseif(substr($_POST['formName'],0,5) == "edit-"){
		$existing = explode("-",$_POST['formName']);
		$existing = str_replace("__","-",$existing[1]);
		$existing = str_replace("_"," ",$existing);
		$queryResult = editShip($conn, $existing, $_POST['name'], $_POST['hasAcronym'], $_POST['manufacturer'], $_POST['acronym'], $_POST['role'], $_POST['crew'], $_POST['length'], $_POST['mass'], $_POST['url'], $_POST['price']);
	}
	
	$ships = $conn->query("SELECT * FROM fleet.shipsData");
	if (mysqli_num_rows($ships) > 0) {
		$html = "";
		while($row = mysqli_fetch_assoc($ships)) {
			$html .= "<div id=\"ship-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."\" class=\"ship pull-left\">\n"
						."\t<div class=\"ship-header\">\n"
							."\t\t<img src=\"".$supportPrefix."images/shipImages/".$row["shipName"].".jpg\"/>\n"
							."\t\t<div id=\"manufacturer-logo-container\"><img class=\"manufacturer-logo\" src=\"".$supportPrefix."images/manufacIcons/".$row["manufac"].".png\"/></div>"
							."\t\t<div class=\"ship-info-header\">\n"
								."\t\t\t<h3>".$row["shipName"]."</h3>\n"
								."\t\t\t<p><em>".$row["manufac"]."</em></p>\n"
							."\t\t</div>\n"
						."\t</div>\n"
						."\t<div class=\"ship-info-body\">\n"
							."\t\t<hr class=\"custom-hr\">\n";
			if($row["manufacShortName"] && $row["manufacShortName"]!="NULL")				
				$html .=	"\t\t<div class=\"row\">\n"
								."\t\t\t<div class=\"col-xs-3\">Acronym</div>\n"
								."\t\t\t<div class=\"col-xs-6\">".$row["manufacShortName"]."</div>\n"
							."\t\t</div>\n"
							."\t\t<hr class=\"custom-hr\">\n";
			$html .=		"\t\t<div class=\"row\">\n"
								."\t\t\t<div class=\"col-xs-3\">Role</div>\n"
								."\t\t\t<div class=\"col-xs-6\">".$row["shipRole"]."</div>\n"
							."\t\t</div>\n"
							."\t\t<hr class=\"custom-hr\">\n"
							."\t\t<div class=\"row\">\n"
								."\t\t\t<div class=\"col-xs-3\">Crew</div>\n"
								."\t\t\t<div class=\"col-xs-6\">".$row["shipCrew"]."</div>\n"
							."\t\t</div>\n"
							."\t\t<hr class=\"custom-hr\">\n"
							."\t\t<div class=\"row\">\n"
								."\t\t\t<div class=\"col-xs-3\">Length</div>\n"
								."\t\t\t<div class=\"col-xs-6\">".$row["shipLength"]."</div>\n"
							."\t\t</div>\n"
							."\t\t<hr class=\"custom-hr\">\n"
							."\t\t<div class=\"row\">\n"
								."\t\t\t<div class=\"col-xs-3\">Mass</div>\n"
								."\t\t\t<div class=\"col-xs-6\">".$row["shipMass"]."</div>\n"
							."\t\t</div>\n"
							."\t\t<hr class=\"custom-hr\">\n"
							."\t\t<div class=\"row\">\n"
								."\t\t\t<div class=\"col-xs-3\">URL</div>\n"
								."\t\t\t<div class=\"col-xs-6\"><a href=\"http://robertsspaceindustries.com/pledge/ships/".$row["rsiUrl"]."\"><i class=\"fa fa-external-link\"></i></a></div>\n"
							."\t\t</div>\n"
							."\t\t<hr class=\"custom-hr\">\n"
							."\t\t<div class=\"row\">\n"
								."\t\t\t<div class=\"col-xs-3\">Price</div>\n"
								."\t\t\t<div class=\"col-xs-6\">".$row["shipPrice"]."</div>\n"
							."\t\t</div>\n"
							."\t\t<hr class=\"custom-hr\">\n"
							."\t\t<div class=\"button-container\">\n"
								."\t\t\t<button class=\"btn btn-warning bold lightbox-btn\" type=\"button\" href=\"#ship-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-edit\"><i class=\"fa fa-pencil-square-o fa-gl\"></i> Edit Ship</button>\n"
								."\t\t\t<button class=\"btn btn-danger bold lightbox-btn\" type=\"button\" href=\"#ship-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-delete\"><i class=\"fa fa-times fa-gl\"></i> Delete Ship</button>\n"
							."\t\t</div>\n"
						."\t</div>\n"
						."\t<div class=\"displayNone\">\n"
							."\t\t<div id=\"ship-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-edit\">\n"
								."\t\t\t<div class=\"edit-ship\">\n"
									."\t\t\t\t<h4 class=\"text-left\"><span class=\"warning-color\"><i class=\"fa fa-pencil-square-o fa-gl\"></i></span> Edit Ship</h4>\n"
									."\t\t\t\t<hr>\n"
									."\t\t\t\t<form action=\"\" id=\"edit-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-ship-form\" class=\"form-horizontal\" method=\"post\">\n"
										."\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"edit-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-ship-form\">\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"name\" class=\"col-sm-2 control-label\">Ship Name</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-space-shuttle fa-rotate-270 fa-gl\"></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"name\" placeholder=\"Carrack\" default=\"".$row["shipName"]."\" value=\"".$row["shipName"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"manufcature\" class=\"col-sm-2 control-label\">Manufacturer</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-copyright fa-gl\"></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"manufacturer\" placeholder=\"Anvil Aerospace\" default=\"".$row["manufac"]."\" value=\"".$row["manufac"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"acronym\" class=\"col-sm-2 control-label\">Acronym</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"checkbox\">\n"
													."\t\t\t\t\t\t\t\t<label>\n"
														."\t\t\t\t\t\t\t\t<input class=\"has-acronym\" id=\"has-acronym-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."_edit\" name=\"hasAcronym\" type=\"checkbox\"";
															if($row["manufacShortName"] && $row["manufacShortName"]!="NULL")
																$html .= " checked";
			$html .=									"> Manufacturer has acronym\n"
													."\t\t\t\t\t\t\t\t</label>\n"
												."\t\t\t\t\t\t\t</div>\n"
												."\t\t\t\t\t\t\t<div id=\"acronym-input-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."_edit-container\"";
													if(!$row["manufacShortName"] || $row["manufacShortName"]=="NULL")
														$html .= " class=\"displayNone\"";
			$html .=							">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group\">\n"
														."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-trademark fa-gl\"></i></i></div>\n"
														."\t\t\t\t\t\t\t\t<input id=\"acronym-input-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-edit\" type=\"text\" class=\"form-control\" name=\"acronym\"";
															if($row["manufacShortName"] && $row["manufacShortName"]!="NULL")
																$html .= " value=\"".$row["manufacShortName"]."\" default=\"".$row["manufacShortName"]."\"";
			$html .=									" placeholder=\"MISC\">\n"
													."\t\t\t\t\t\t\t\t</div>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"role\" class=\"col-sm-2 control-label\">Role</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-tag fa-gl\"></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"role\" placeholder=\"Exploration\" default=\"".$row["shipRole"]."\" value=\"".$row["shipRole"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"crew\" class=\"col-sm-2 control-label\">Crew</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-users fa-gl\"></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"crew\" placeholder=\"3\" default=\"".$row["shipCrew"]."\" value=\"".$row["shipCrew"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"length\" class=\"col-sm-2 control-label\">Length</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-arrows-h fa-gl\"></i></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"length\" placeholder=\"63\" default=\"".$row["shipLength"]."\" value=\"".$row["shipLength"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"mass\" class=\"col-sm-2 control-label\">Mass</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\"><i class=\"fa fa-cube fa-gl\"></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"mass\" placeholder=\"25000\" default=\"".$row["shipMass"]."\" value=\"".$row["shipMass"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"url\" class=\"col-sm-2 control-label\">URL</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-link fa-gl\"></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"url\" placeholder=\"".$row["rsiUrl"]."\" default=\"".$row["rsiUrl"]."\" value=\"".$row["rsiUrl"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<label for=\"price\" class=\"col-sm-2 control-label\">Price</label>\n"
											."\t\t\t\t\t\t<div class=\"col-sm-10\">\n"
												."\t\t\t\t\t\t\t<div class=\"input-group\">\n"
													."\t\t\t\t\t\t\t\t<div class=\"input-group-addon reset-input\" data-toggle=\"tool-tip\" title=\"Reset field\"><i class=\"fa fa-usd fa-gl\"></i></i></div>\n"
													."\t\t\t\t\t\t\t\t<input type=\"text\" class=\"form-control\" name=\"price\" placeholder=\"125\" default=\"".$row["shipPrice"]."\" value=\"".$row["shipPrice"]."\" required>\n"
												."\t\t\t\t\t\t\t</div>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											 ."\t\t\t\t\t\t<div class=\"col-sm-offset-2 col-sm-10\">\n"
												."\t\t\t\t\t\t\t<button form=\"edit-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-ship-form\" class=\"btn btn-warning\" type=\"submit\"><i class=\"fa fa-pencil-square-o fa-gl\"></i> Edit Ship</button>\n"
											."\t\t\t\t\t\t</div>\n"
										."\t\t\t\t\t</div>\n"
									."\t\t\t\t</form>\n"
								."\t\t\t</div>\n"
								."\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you click the edit. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
							."\t\t</div>\n"
							."\t\t<div id=\"ship-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-delete\">\n"
								."\t\t\t<div class=\"delete-ship\">\n"
									."\t\t\t\t<h4 class=\"text-left\"><span class=\"danger-color\"><i class=\"fa fa-times fa-gl\"></i></span> Delete Ship</h4>\n"
									."\t\t\t\t<hr>\n"
									."\t\t\t\t<form action=\"\" id=\"delete-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-ship-form\" method=\"post\">\n"
										."\t\t\t\t\t<input type=\"hidden\" name=\"formName\" value=\"delete-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-ship-form\">\n"
										."\t\t\t\t\t<div class=\"checkbox\">\n"
											."\t\t\t\t\t\t<label>\n"
												."\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"deleteShip\">\n"
												."Completely delete the ship <strong>".$row["shipName"]."</strong> from database.\n"
											."\t\t\t\t\t\t</label>\n"
										."\t\t\t\t\t</div>\n"
										."\t\t\t\t\t<div class=\"form-group\">\n"
											."\t\t\t\t\t\t<button form=\"delete-".str_replace("-","__",str_replace(" ","_",$row["shipName"]))."-ship-form\" class=\"btn btn-danger\" type=\"submit\"><i class=\"fa fa-times fa-gl\"></i> Delete Ship</button>\n"
										."\t\t\t\t\t</div>\n"
									."\t\t\t\t</form>\n"
								."\t\t\t</div>\n"
								."\t\t\t<p class=\"modify-disclaimer\">Note: Changes will only be made if you select the checkbox and click delete. Closing this page or clicking \"close\" will not submit the changes.</p>\n"
							."\t\t</div>\n"
						."\t</div>\n"
					."</div>\n";
				// var_dump($html);
		}
	} else {
		$html = "<p class=\"bold align-center\">No ships in database. Use the add feature above to add a new ship</p>";
	}
	
	
	//Add, Delete, and Edit functions for modifiing Ship informaiton
	function addShip($conn, $name, $manufacturer, $acronym, $role, $crew, $length, $mass, $url, $price){
		$query = "INSERT INTO fleet.shipsData (`shipName`, `manufac`, `manufacShortName`, `shipRole`, `shipCrew`, `shipLength`, `shipMass`, `rsiUrl`, `shipPrice`)"
					." VALUES ('$name', '$manufacturer', $acronym, '$role', '$crew', '$length', '$mass', '$url', '$price')";
		$result = $conn->query($query);
		return($result);
	}
	function deleteShip($conn, $name){
		$name = str_replace("_"," ",$name);
		$query = "DELETE FROM fleet.shipsData WHERE `shipName`='$name'";
		$result = $conn->query($query);
		return($result);
	}
	function editShip($conn, $existing, $name, $hasAcronym, $manufacturer, $acronym, $role, $crew, $length, $mass, $url, $price){
		if($hasAcronym){
			$query = "UPDATE fleet.shipsData"
						." SET `shipName`='$name', `manufac`='$manufacturer', `manufacShortName`=$acronym, `shipRole`='$role', `shipCrew`='$crew',"
						." `shipLength`='$length', `shipMass`='$mass', `rsiUrl`='$url', `shipPrice`='$price'"
						." WHERE `shipName`='$existing'";
		}
		else{
			$query = "UPDATE fleet.shipsData"
					." SET `shipName`='$name', `manufac`='$manufacturer', `manufacShortName`=NULL, `shipRole`='$role', `shipCrew`='$crew',"
					." `shipLength`='$length', `shipMass`='$mass', `rsiUrl`='$url', `shipPrice`='$price'"
					." WHERE `shipName`='$existing'";
		}
		$result = $conn->query($query);
		return($result);
	}
?>

			<div class="alert-section">
				<?php echo $alert ?>
				<?php if($queryResult) echo "<div class=\"alert alert-success\" role=\"alert\">Update successful!</div>"; ?>
			</div>
			<p>Dynamic Fleet is a ship inventory tracker. This module keeps track of all user ships in our organization.</p>
			<hr class="custom-hr increase-margins">
			<div id="add-ship">
				<h4 class="text-left"><span class="success-color"><i class="fa fa-plus fa-gl"></i></span> Add Ship</h4>
				<p class="text-justify">Adds a new ship type to the database.</p>
				<form action="" id="add-ship-form" class="form-horizontal" method="post">
					<input type="hidden" name="formName" value="add-ship-form">
					<div class="inline-form-column pull-left">
						<div class="form-group">
							<label for="name" class="col-sm-2 control-label">Ship Name</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-space-shuttle fa-rotate-270 fa-gl"></i></div>
									<input type="text" class="form-control" name="name" placeholder="Carrack" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="manufcature" class="col-sm-2 control-label">Manufacturer</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-copyright fa-gl"></i></div>
									<input type="text" class="form-control" name="manufacturer" placeholder="Anvil Aerospace" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="acronym" class="col-sm-2 control-label">Acronym</label>
							<div class="col-sm-10">
								<div class="checkbox">
									<label>
										<input class="has-acronym" id="has-acronym-add" name="hasAcronym" type="checkbox"> Manufacturer has acronym
									</label>
								</div>
								<div id="acronym-input-add-container" class="displayNone">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-trademark fa-gl"></i></i></div>
										<input id="acronym-input-add" type="text" class="form-control" name="acronym" placeholder="MISC">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="role" class="col-sm-2 control-label">Role</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-tag fa-gl"></i></div>
									<input type="text" class="form-control" name="role" placeholder="Exploration" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="crew" class="col-sm-2 control-label">Crew</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-users fa-gl"></i></div>
									<input type="text" class="form-control" name="crew" placeholder="3" required>
								</div>
							</div>
						</div>
					</div>
					<div class="inline-form-column pull-left">
						<div class="form-group">
							<label for="length" class="col-sm-2 control-label">Length</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-arrows-h fa-gl"></i></i></div>
									<input type="text" class="form-control" name="length" placeholder="63" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="mass" class="col-sm-2 control-label">Mass</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-cube fa-gl"></i></div>
									<input type="text" class="form-control" name="mass" placeholder="25000" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="url" class="col-sm-2 control-label">URL</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-link fa-gl"></i></div>
									<input type="text" class="form-control" name="url" placeholder="origin-300/300i" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="price" class="col-sm-2 control-label">Price</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-usd fa-gl"></i></i></div>
									<input type="text" class="form-control" name="price" placeholder="125" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							 <div class="col-sm-offset-2 col-sm-10">
								<button form="add-ship-form" class="btn btn-success" type="submit"><i class="fa fa-plus fa-gl"></i> Add Ship</button>
							</div>
						</div>
					</div>
				</form>
				<hr class="custom-hr increase-margins clear">
			</div>
			<div class="section-container">
				<div id="ship-data" class="more section-header">
					<h3>Ship Data</h3>
				</div>
				<div id="more-ship-data" class="section-body">
					<br>
					<form>
						<div class="form-group" style="margin-top:15px">
							<input type="text" class="form-control" id="search-ships" placeholder="Search...">
						</div>
					</form>
					<div id="ship-container">
						<?php echo $html; ?>
					</div>
				</div>
			</div>

<?php
	include $prefix.'footer.php';
?>