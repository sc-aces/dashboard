<?php
	$requiresLogin = true;
	$returnUrl = "admin/balletbox/";
	$customHeaderIcon = "<i class=\"fa fa-archive\"></i>";
	$prefix = "../";
	$pageName = "Ballot Box";
	include $prefix.'header.php';
	if(count($_POST)>0 && $debug){
		echo "<pre>";
		var_dump($_POST);
		echo "</pre>";
	}
	
	if($_POST['formName'] == "create-election-form"){
		if($_POST['election-type'] == "General Election"){
			$create = createNewElection("gen","1,3");
			if($create){
				$query = "SELECT id FROM $db.elections WHERE id=(SELECT max(id) from fleet.elections)";
				$result = $conn->query($query);
				if (mysqli_num_rows($result) > 0) {
					$electionId = mysqli_fetch_assoc($result);
					$electionId = $electionId['id'];
					$executive = createNewElection_Generic($electionId, "Executive Council");
					$capitalist = createNewElection_Generic($electionId, "Capitalist Council");
					$explorer = createNewElection_Generic($electionId, "Explorer Council");
					$soldier = createNewElection_Generic($electionId, "Soldier Council");
					if($executive && $capitalist && $explorer && $soldier)
						$alert .= createAlert("success","Successfully created election.");
					else{
						$conn->query("DELETE FROM $db.elections WHERE `id`='$electionId'");
						$alert .= createAlert("danger","Failed to create generics.");
					}
				}else
					$alert .= createAlert("danger","FATAL: Failed to create election");
			}
			else{
				$alert .= createAlert("danger","Failed to create election");
			}
		}else if($_POST['election-type'] == "Special Election"){
			$create = createNewElection("spe","1,3");
			if($create){
				$query = "SELECT id FROM $db.elections WHERE id=(SELECT max(id) from $db.elections)";
				$result = $conn->query($query);
				if (mysqli_num_rows($result) > 0) {
					$electionId = mysqli_fetch_assoc($result);
					$electionId = $electionId['id'];
					
					$error = false;
					if($_POST['executiveCouncil'] == "true"){
						$executive = createNewElection_Generic($electionId, "Executive Council");
						if(!$executive)
							$error = true;
					}
					if($_POST['capitalistCouncil'] == "true"){
						$capitalist = createNewElection_Generic($electionId, "Capitalist Council");
						if(!$capitalist)
							$error = true;
					}
					if($_POST['explorerCouncil'] == "true"){
						$explorer = createNewElection_Generic($electionId, "Explorer Council");
						if(!$explorer)
							$error = true;
					}
					if($_POST['soliderCouncil'] == "true"){
						$soldier = createNewElection_Generic($electionId, "Soldier Council");
						if(!$soldier)
							$error = true;
					}
					
					if($error){
						$conn->query("DELETE FROM $db.elections WHERE `id`='$electionId'");
						$alert .= createAlert("danger","Failed to create generics.");
					}else
						$alert .= createAlert("success","Successfully created election.");
				}else
					$alert .= createAlert("danger","FATAL: Failed to create election");
			}
			else{
				$alert .= createAlert("danger","Failed to create election");
			}
		}else if($_POST['election-type'] == "Squadron Vote"){
			$tags = str_replace(" ","",$_POST['tags-description']);
			$create = createNewElection("squ","2,$tags");
			if($create){
				$query = "SELECT id FROM $db.elections WHERE id=(SELECT max(id) from $db.elections)";
				$result = $conn->query($query);
				if (mysqli_num_rows($result) > 0) {
					$electionId = mysqli_fetch_assoc($result);
					$electionId = $electionId['id'];
					
					$squadronGen= createNewElection_Generic($electionId, $_POST['squadron-id']." Vote");
					if(!$squadronGen){
						$conn->query("DELETE FROM $db.elections WHERE `id`='$electionId'");
						$alert .= createAlert("danger","Failed to create generics.");
					}else
						$alert .= createAlert("success","Successfully created election.");
				}else
					$alert .= createAlert("danger","FATAL: Failed to create election");
			}
			else{
				$alert .= createAlert("danger","Failed to create election");
			}
		}else if($_POST['election-type'] == "Generic Election"){
			$tags = str_replace(" ","",$_POST['tags-description']);
			$create = createNewElection("bge","1,2,$tags");
			if($create){
				$query = "SELECT id FROM $db.elections WHERE id=(SELECT max(id) from fleet.elections)";
				$result = $conn->query($query);
				if (mysqli_num_rows($result) > 0) {
					$electionId = mysqli_fetch_assoc($result);
					$electionId = $electionId['id'];
					
					$squadronGen= createNewElection_Generic($electionId,$_POST['election-name']);
					if(!$squadronGen){
						$conn->query("DELETE FROM $db.elections WHERE `id`='$electionId'");
						$alert .= createAlert("danger","Failed to create generics.");
					}else
						$alert .= createAlert("success","Successfully created election.");
				}else
					$alert .= createAlert("danger","FATAL: Failed to create election");
			}
			else
				$alert .= createAlert("danger","Failed to create election");
		}else
			$alert .= createAlert("danger","Invalid Election Type: <code>".$_POST['election-type']."</code>");
	}else if(explode('_',$_POST['formName'])[0] == "edit"){
		$electionId = explode('-',$_POST['formName'])[1];
		$result = $conn->query("UPDATE $db.elections SET `name`='".$_POST['name']."', `description`='".$_POST['description']."', `start`='".$_POST['start']." 00:00:00', `end`='".$_POST['end']." 23:59:00', `access_tags`='".$_POST['tags']."' WHERE `id`=$electionId");
		if($result)
			$alert .= createAlert("success","Election successfully modified!");
		else
			$alert .= createAlert("danger","Database update failed!");
	}else if(explode('_',$_POST['formName'])[0] == "delete" && isset($_POST['commitChange'])){
		$electionId = explode('-',$_POST['formName'])[1];
		$result = $conn->query("UPDATE $db.elections SET `status`='deleted', `date_deleted`='$currentTime' WHERE `id`=$electionId");
		if($result)
			$alert .= createAlert("success","Election successfully marked as deleted!");
		else
			$alert .= createAlert("danger","Database update failed!");
	}else if(explode('_',$_POST['formName'])[0] == "start" && isset($_POST['commitChange'])){
		$electionId = explode('-',$_POST['formName'])[1];
		$result = $conn->query("UPDATE $db.elections SET `start`='$currentDate 00:00:00' WHERE `id`=$electionId");
		if($result)
			$alert .= createAlert("success","Election successfully started!");
		else
			$alert .= createAlert("danger","Database updated failed!");
	}else if(explode('_',$_POST['formName'])[0] == "stop" && isset($_POST['commitChange'])){
		$electionId = explode('-',$_POST['formName'])[1];
		$result = $conn->query("UPDATE $db.elections SET `status`='stopped', `last_status`='stopped', `last_status_date`='$currentTime' WHERE `id`=$electionId");
		if($result)
			$alert .= createAlert("success","Election successfully stopped!");
		else
			$alert .= createAlert("danger","Database update failed!");
	}else if(explode('_',$_POST['formName'])[0] == "restore" && isset($_POST['commitChange'])){
		$electionId = explode('-',$_POST['formName'])[1];
		$status = "unknown";
		$result = $conn->query("SELECT * FROM $db.elections WHERE `id`=$electionId");
		if($result){
			$row = mysqli_fetch_assoc($result);
			$status = $row['last_status'];
			$result = $conn->query("UPDATE $db.elections SET `status`='$status', `date_deleted`=NULL WHERE `id`=$electionId");
		}
			
		if($result)
			$alert .= createAlert("success","Database successfully updated!");
		else
			$alert .= createAlert("danger","Database updated failed!");
	}
	
	function createNewElection_Generic($id, $name){
		global $conn;
		global $db;
		$query = "INSERT INTO $db.election_generic (`id`, `election_id`, `name`, `candidates`, `voters`)"
					." VALUES (NULL, $id, '$name', '{\"candidates\": [null], \"totalVotes\":null}', '{\"voters\": [null], \"totalVoters\":null}')";
		$result = $conn->query($query);
		return $result;
	}
	
	function createNewElection($type, $tags){
		global $conn;
		global $db;
		// if(strlen($_POST['election-description']) == 0)
		// 	$description = "NULL";
		// else
		// 	$description = "'".$_POST['election-description']."'"; 
		$query = "INSERT INTO $db.elections (`id`, `type`, `name`, `status`, `description`, `admin_notes`, `access_tags`, `start`, `end`, `last_status`, `last_status_date`, `date_deleted`)"
				." VALUES (NULL, '$type', '".$_POST['election-name']."', 'unknown', '$description', NULL, '$tags', '".$_POST['start']." 00:00:00', '".$_POST['end']." 23:59:00', NULL, NULL, NULL)";
		$result = $conn->query($query);
		return $result;
	}
	
	//Fetch and build all HTML for all elections
	$query = "SELECT * FROM $db.elections";
	$elections = $conn->query($query);
	if(!$elections)
		$alert .= createAlert("danger","Unable to fetch existing elections");
?>
			<div class="alert-section">
				<?php echo $alert; ?>
			</div>
			<p>Ballot Box is a voting module designed for government elections in our organization. Voting times are based off of UTC+0.</p>
			<hr class="custom-hr increase-margins">
			<section class="form-container">
				<h4 class="text-left more-static" id="create-election"><span class="success-color"><i class="fa fa-plus"></i></span> Create Election</h4>
				<div id="more-static-create-election">
					<p class="text-justify">Creates a new election.</p>
					<strong>Usage information:</strong>
					<ol>
						<li>The <em><strong>Start Date</strong></em> can not be farther in the future than the <em><strong>End Date</strong></em></li>
						<li>The <em><strong>End Date</strong></em> can not be more than <em><strong>one year</strong></em> in the future</li>
						<li>The <em><strong>Start Date</strong></em> has a time stamp of <em><strong>00:00 UTC</strong></em> of the selected date</li>
						<li>The <em><strong>End Date</strong></em> has a time stamp of <em><strong>23:59 UTC</strong></em> of the selected date</li>
						<li>The date format is <em><strong>YYYY/MM/DD</strong></em></li>
					</ol>
					<br>
					<form action="" id="create-election-form" class="form-horizontal" method="post">
						<input type="hidden" name="formName" value="create-election-form">
						<div class="inline-form-column">
							<div class="form-group">
								<label for="election-type" class="col-sm-2 control-label">Election Type</label>
								<div class="col-sm-10">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-barcode"></i></div>
										<select class="form-control" name="election-type" id="election-type">
											<option>General Election</option>
											<option>Special Election</option>
											<option>Squadron Vote</option>
											<option>Generic Election</option>
											<option>Error</option>
										</select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="election-name" class="col-sm-2 control-label">Name</label>
								<div class="col-sm-10">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-pencil"></i></div>
										<input type="text" class="form-control" id="election-name" placeholder="Eelection Name" name="election-name" required>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="election-description" class="col-sm-2 control-label">Description</label>
								<div class="col-sm-10">
									<textarea class="form-control" id="election-description" form="create-election-form" name="election-description" placeholder="Description of election..."></textarea>
								</div>
							</div>
							<div id="squadron-vote-inputs" class="displayNone">
								<div class="form-group">
									<label for="squadron-id" class="col-sm-2 control-label">Squadron Name</label>
									<div class="col-sm-10">
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-rocket"></i></div>
											<select class="form-control" name="squadron-id" id="squadron-id">
												<option>Squadron 1</option>
												<option>Squadron 2</option>
												<option>Squadron 3</option>
												<option>Squadron 4</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div id="election-tags-input" class="displayNone">
								<div class="form-group">
									<label for="tags-description" class="col-sm-2 control-label">Tags</label>
									<div class="col-sm-10">
										<textarea class="form-control" id="tags-description" form="create-election-form" name="tags-description" placeholder="Enter the user tags that can vote and view this election (CSV)..."></textarea>
									</div>
								</div>
							</div>
							<div id="special-election-inputs" class="displayNone">
								<div class="form-group">
									<label for="electioni-dd" class="col-sm-2 control-label">Councils</label>
									<div class="col-sm-10">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="executiveCouncil" value="true">Executive Council
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" name="capitalistCouncil" value="true">Capitalist Council
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" name="explorerCouncil" value="true">Explorer Council
											</label>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" name="soliderCouncil" value="true">Solider Council
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Date</label>
								<div class="col-sm-10">
									<div class="input-daterange input-group" id="election-datepicker">
										<input type="text" class="input-sm form-control" name="start" />
										<span class="input-group-addon">to</span>
										<input type="text" class="input-sm form-control" name="end" />
									</div>
								</div>
							</div>
							<div class="form-group">
								 <div class="col-sm-offset-2 col-sm-10">
									<button form="create-election-form" class="btn btn-success" type="submit"><i class="fa fa-plus"></i> Create Election</button>
								</div>
							</div>
						</div>
					</form>
				</div>
				<hr class="custom-hr increase-margins">
			</section>
			<h2>Recent Elections</h2>
			<div id="elections-data">
				<form class="col-md-8">
					<div class="form-group">
						<input type="text" class="form-control" id="search-elections" placeholder="Search...">
					</div>
				</form>
				<ul id="ship-pagination" class="pagination">
					
				</ul>
				<br class="clear">
				<div id="elections-container">
					<?php echo $electionHtml; ?>
				</div>
			</div>
<?php
	include $prefix.'footer.php';
?>