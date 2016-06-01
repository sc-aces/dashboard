<?php
	$requiresLogin = true;
	$returnUrl = "admin/dynamicfleet/";
	$prefix = "../";
	$pageName = "Dynamic Fleet";
	$customHeaderIcon = "<i class=\"fa fa-space-shuttle\"></i>";
	include $prefix.'header.php';
	
	if($debug){
		echo "<pre>";
		var_dump(mysqli_stat($conn));
		var_dump($_POST);
		var_dump($_FILES);
		var_dump($_SERVER);
		echo "</pre>";
	}
	
	if($_POST['formName']=="add-ship-form"){
		if(uploadFile("$prefix../support/images/dynamicfleet/shipImages/", $_FILES, "jpg", $_POST['name'])){
			//File upload successful
			$alert .= createAlert("success","The file <em>". basename( $_FILES['fileToUpload']['name']). "</em> has been uploaded to <em>$prefix../support/images/dynamicfleet/shipImages/".$_POST['name'].".jpg</em>");
			//Add SQL data to database
			if(!$_POST['acronym'])
				$acronym = "NULL";
			else
				$acronym = "'".$_POST['acronym']."'";
			$queryResult = addShip($_POST['name'], $_POST['manufacturer'], $acronym, $_POST['role'], $_POST['crew'], $_POST['length'], $_POST['mass'], $_POST['url'], $_POST['price']);
		}
	
		if($queryResult)
			$alert .= createAlert("success","Database update successful!");
		else if(!$queryResult && isset($_POST['formName'])){
			$alert .= createAlert("danger","Database update failed!");
			$existing = explode("-",$_POST['formName']);
			$existing = str_replace("__","-",$existing[1]);
			$existing = str_replace("_"," ",$existing);
			if(!unlink("$prefix../support/images/dynamicfleet/shipImages/$existing.jpg"))
				$alert .= createAlert("danger","Failed to remove image: <em>$prefix../support/images/dynamicfleet/shipImages/$existing.jpg</em>");
		}
	}
	elseif($_POST['formName']=="add-manufac-icon-form"){
		if(uploadFile("$prefix../support/images/dynamicfleet/manufacIcons/",$_FILES, "png", $_POST['name'])){
			//File upload successful
			$alert .= createAlert("success","The file <em>". basename( $_FILES['fileToUpload']['name']). "</em> has been uploaded to <em>$prefix../support/images/dynamicfleet/manufacIcons/".$_POST['name'].".png</em>");
		}
	}
	elseif($_POST['formName']=="delete-manufac-icon-form"){
		if(unlink("$prefix../support/images/dynamicfleet/manufacIcons/".$_POST['name'].".png"))
			$alert .= createAlert("success","Successfully removed image: <em>$prefix../support/images/dynamicfleet/manufacIcons/".$_POST['name'].".png</em>");
		else
			$alert .= createAlert("danger","Failed to remove image: <em>$prefix../support/images/dynamicfleet/manufacIcons/".$_POST['name'].".png</em>");
	}
	elseif(substr($_POST['formName'],0,7) == "delete-" && $_POST['deleteShip'] == "on"){
		$existing = explode("-",$_POST['formName']);
		$existing = str_replace("__","-",$existing[1]);
		$existing = str_replace("_"," ",$existing);
		$queryResult = deleteShip($existing);
		if(!unlink("$prefix../support/images/dynamicfleet/shipImages/$existing.jpg"))
			$alert .= createAlert("danger","Failed to remove image: <em>$prefix../support/images/dynamicfleet/shipImages/$existing.jpg</em>");
		
		if($queryResult)
			$alert .= createAlert("success","Database update successful!");
		else if(!$queryResult && isset($_POST['formName']))
			$alert .= createAlert("danger","Database update failed!");
	}
	elseif(substr($_POST['formName'],0,5) == "edit-"){
		$existing = explode("-",$_POST['formName']);
		$existing = str_replace("__","-",$existing[1]);
		$existing = str_replace("_"," ",$existing);
		$queryResult = editShip($existing, $_POST['name'], $_POST['hasAcronym'], $_POST['manufacturer'], $_POST['acronym'], $_POST['role'], $_POST['crew'], $_POST['length'], $_POST['mass'], $_POST['url'], $_POST['price']);
		
		if($_FILES['fileToUpload']['name'] != ""){
			if(!unlink("$prefix../support/images/dynamicfleet/shipImages/".$_POST['name'].".jpg"))
				$alert .= createAlert("danger","Failed to remove existing image: <em>$prefix../support/images/dynamicfleet/shipImages/".$_POST['name'].".jpg</em>");
			else{
				if(!uploadFile("$prefix../support/images/dynamicfleet/shipImages/", $_FILES, "jpg", $_POST['name']))
					$alert .= createAlert("danger","Failed to update image: <em>$prefix../support/images/dynamicfleet/shipImages/".$_POST['name'].".jpg</em>");
			}
		}
		
		if($queryResult)
			$alert .= createAlert("success","Database update successful!");
		else if(!$queryResult && isset($_POST['formName']))
			$alert .= createAlert("danger","Database update failed!");
	}
	
	//Add, Delete, and Edit functions for modifiing Ship informaiton
	function addShip($name, $manufacturer, $acronym, $role, $crew, $length, $mass, $url, $price){
		global $conn;
		$query = "INSERT INTO fleet.shipsData (`shipName`, `manufac`, `manufacShortName`, `shipRole`, `shipCrew`, `shipLength`, `shipMass`, `rsiUrl`, `shipPrice`)"
					." VALUES ('$name', '$manufacturer', $acronym, '$role', '$crew', '$length', '$mass', '$url', '$price')";
		$result = $conn->query($query);
		return($result);
	}
	function deleteShip($name){
		global $conn;
		$name = str_replace("_"," ",$name);
		$query = "DELETE FROM fleet.shipsData WHERE `shipName`='$name'";
		$result = $conn->query($query);
		return($result);
	}
	function editShip($existing, $name, $hasAcronym, $manufacturer, $acronym, $role, $crew, $length, $mass, $url, $price){
		global $conn;
		if($hasAcronym){
			$query = "UPDATE fleet.shipsData"
						." SET `shipName`='$name', `manufac`='$manufacturer', `manufacShortName`='$acronym', `shipRole`='$role', `shipCrew`='$crew',"
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
			</div>
			<p>Dynamic Fleet is a ship inventory tracker. This module keeps track of all user ships in our organization.</p>
			<hr class="custom-hr increase-margins">
			<section class="form-container">
				<h4 class="text-left more-static" id="add-ship"><span class="success-color"><i class="fa fa-plus fa-gl"></i></span> Add Ship</h4>
				<div id="more-static-add-ship">
					<p class="text-justify">Adds a new ship type to the database.</p>
					<strong>Usage information:</strong>
					<ol>
						<li>Uploaded image should have the size of 409px x 144px and must be a .jpg</li>
						<li>Ensure that the image name is displayed before submitting the change</li>
					</ol>
					<br>
					<form action="" id="add-ship-form" class="form-horizontal" method="post" enctype="multipart/form-data">
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
								<label for="price" class="col-sm-2 control-label">Image</label>
								<div class="col-sm-10">
									<input class="upload" type="file" name="fileToUpload" id="fileToUpload" required>
								</div>
							</div>
							<div class="form-group">
								 <div class="col-sm-offset-2 col-sm-10">
									<button form="add-ship-form" class="btn btn-success" type="submit"><i class="fa fa-plus fa-gl"></i> Add Ship</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<hr class="custom-hr increase-margins clear">
			</section>
			<section class="form-container">
				<h4 class="text-left more-static" id="add-manufac-icon"><span class="success-color"><i class="fa fa-plus fa-gl"></i></span> Add Manufacturer Icon</h4>
				<div id="more-static-add-manufac-icon">
					<p class="text-justify">Adds a new manufacturer icon to the server.</p>
					<strong>Usage information:</strong>
					<ol>
						<li>Uploaded image should have the size of 45px x 45px, must be a .png, and have a transparent background</li>
						<li>Ensure that the image name is displayed before submitting the change</li>
					</ol>
					<br>
					<form action="" id="add-manufac-icon-form" class="form-horizontal" method="post" enctype="multipart/form-data">
						<input type="hidden" name="formName" value="add-manufac-icon-form">
						<div class="inline-form-column pull-left">	
							<div class="form-group">
								<label for="name" class="col-sm-2 control-label">Manufacturer</label>
								<div class="col-sm-10">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-copyright fa-gl"></i></div>
										<input type="text" class="form-control" name="name" placeholder="Roberts Space Industry" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="fileToUpload" class="col-sm-2 control-label">Image</label>
								<div class="col-sm-10">
									<input class="upload" type="file" name="fileToUpload" id="fileToUpload" required>
								</div>
							</div>
							<div class="form-group">
								 <div class="col-sm-offset-2 col-sm-10">
									<button form="add-manufac-icon-form" class="btn btn-success" type="submit"><i class="fa fa-plus fa-gl"></i> Add Image</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<hr class="custom-hr increase-margins clear">
			</section>
			<section class="form-container">
				<h4 class="text-left more-static" id="delete-manufac-icon"><span class="danger-color"><i class="fa fa-trash-o fa-gl"></i></span> Delete Manufacturer Icon</h4>
				<div id="more-static-delete-manufac-icon">	
					<p class="text-justify">Delete an existing manufacturer icon from the server.</p>
					<!--<strong>Usage information:</strong>-->
					<!--<ol>-->
					<!--	<li>Uploaded image should have the size of 45px x 45px, must be a .png, and have a transparent background</li>-->
					<!--	<li>Ensure that the image name is displayed before submitting the change</li>-->
					<!--</ol>-->
					<!--<br>-->
					<form action="" id="delete-manufac-icon-form" class="form-horizontal" method="post" enctype="multipart/form-data">
						<input type="hidden" name="formName" value="delete-manufac-icon-form">
						<div class="inline-form-column pull-left">	
							<div class="form-group">
								<label for="name" class="col-sm-2 control-label">Manufacturer</label>
								<div class="col-sm-10">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-copyright fa-gl"></i></div>
										<select class="form-control" name="name" id="name">
											<?php
												$imageNames = scandir("$prefix../support/images/dynamicfleet/manufacIcons/");
												for($i=2; $i<count($imageNames); $i++)
													echo "\t<option>".substr($imageNames[$i],0,-4)."</option>\n";
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								 <div class="col-sm-offset-2 col-sm-10">
									<button form="delete-manufac-icon-form" class="btn btn-danger" type="submit"><i class="fa fa-trash-o fa-gl"></i> Delete Image</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<hr class="custom-hr increase-margins clear">
			</section>
			<h2>Ship Data</h2>
			<div id="ship-data">
				<form class="col-md-8">
					<div class="form-group">
						<input type="text" class="form-control" id="search-ships" placeholder="Search...">
					</div>
				</form>
				<ul id="ship-pagination" class="pagination">
					
				</ul>
				<br class="clear">
				<div id="ship-container">
					
				</div>
			</div>

<?php
	include $prefix.'footer.php';
?>