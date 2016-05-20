<?php
	$requiresLogin = true;
	$returnUrl = "admin/balletbox/";
	$customHeaderIcon = "<i class=\"fa fa-archive\"></i>";
	$prefix = "../";
	$pageName = "Ballot Box";
	include $prefix.'header.php';
	if(count($_POST)>0){
		var_dump($_POST);
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
						$conn->query("DELETE FROM `fleet`.`elections` WHERE `id`='$electionId'");
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
				$query = "SELECT id FROM $db.elections WHERE id=(SELECT max(id) from fleet.elections)";
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
						$conn->query("DELETE FROM `fleet`.`elections` WHERE `id`='$electionId'");
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
				$query = "SELECT id FROM $db.elections WHERE id=(SELECT max(id) from fleet.elections)";
				$result = $conn->query($query);
				if (mysqli_num_rows($result) > 0) {
					$electionId = mysqli_fetch_assoc($result);
					$electionId = $electionId['id'];
					
					$squadronGen= createNewElection_Generic($electionId, $_POST['squadron-id']." Vote");
					if(!$squadronGen){
						$conn->query("DELETE FROM `fleet`.`elections` WHERE `id`='$electionId'");
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
	}
	
	function createNewElection_Generic($id, $name){
		global $conn;
		global $db;
		$query = "INSERT INTO $db.election_generic (`id`, `election_id`, `name`, `candidates`, `voters`)"
					." VALUES (NULL, $id, '$name', '{\"candidates\": [null]}', '{\"voters\": [null]}')";
		$result = $conn->query($query);
		return $result;
	}
	
	function createNewElection($type, $tags){
		global $conn;
		global $db;
		if(strlen($_POST['election-description']) == 0)
			$description = "NULL";
		else
			$description = "'".$_POST['election-description']."'"; 
		$query = "INSERT INTO $db.elections (`id`, `type`, `name`, `status`, `description`, `admin_notes`, `access_tags`, `start`, `end`)"
				." VALUES (NULL, '$type', '".$_POST['election-name']."', '".strtolower($_POST['election-status'])."', $description, NULL, '$tags', '".$_POST['election-start']."', '".$_POST['election-end']."')";
		$result = $conn->query($query);
		return $result;
	}
	
	//Fetch and build all HTML for all elections
	$query = "SELECT * FROM $db.elections";
	$elections = $conn->query($query);
	if(!$elections)
		$alert .= createAlert("danger","Unable to fetch existing elections");
	else{
		$electionHtml .= "pull from db";
	}
	
// 	$timers = "<script>"
// 				."var deadline = new Date(Date.parse(new Date()) + 12 * 24 * 60 * 60 * 1000);"
// ."initializeClock('clockdiv', deadline);"
// ."var deadline = new Date(Date.parse(new Date()) + 6 * 24 * 60 * 60 * 1000);"
// ."initializeClock('clockdiv1', deadline);"
// 				."</script>";
// 				echo $timers;
?>
			<div class="alert-section">
				<?php echo $alert; ?>
			</div>
			<p>Ballot Box is a voting module designed for government elections in our organization. Voting times are based off of UTC+0.</p>
			<hr class="custom-hr increase-margins">
			<div id="create-election">
				<h4 class="text-left"><span class="success-color"><i class="fa fa-plus"></i></span> Create Election</h4>
				<p class="text-justify">Creates a new election. <del>The <em><strong>Election ID</strong></em> is the used to generate the name of the election 
										(ie an ID of "<em><strong>ge</strong></em>" will create an election titled "<em><strong>General Election - <code>lastElectionId++</code></strong></em>").</del>
										The <em><strong>Start Date</strong></em>	can not be farther in the future than the <em><strong>End Date</strong></em>.
										The <em><strong>End Date</strong></em> can not be more than a year in the future.</p>
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
							<label for="election-status" class="col-sm-2 control-label">Status</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-heartbeat"></i></div>
									<select class="form-control" name="election-status" id="election-status">
										<option>Upcoming</option>
										<option>Active</option>
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
							<label for="election-start" class="col-sm-2 control-label">Start Date</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-calendar-plus-o"></i></div>
									<input type="date" class="form-control" name="election-start" id="election-start" min="2016-04-02" value="2016-04-02" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="election-end" class="col-sm-2 control-label">End Date</label>
							<div class="col-sm-10">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-calendar-times-o"></i></div>
									<input type="date" class="form-control" name="election-end" id="election-end" min="2016-04-03" max="2017-04-03" value="2016-04-03" required>
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
				<hr class="custom-hr increase-margins">
			</div>
			<h2>Recent Elections</h2>
			<div id="elections-container">
				<?php echo $electionHtml; ?>
			</div>
			<div  id="election-1005" class="section-container">
				<div class="election-status section-header info-background more-parent">
					<div class="pull-left">
						<h4 class="bold">General Election - 1005</h4>
						<h5>Mar 17 - Mar 23, 2016</h5>
					</div>
					<div class="pull-right">
						<h4><i class="fa fa-info"></i> Upcoming</h4>
						<h5><div class="text-clock" id="clockdiv">
							<i class="fa fa-clock-o"></i> 
							<span class="days"></span>d
							<span class="hours"></span>h
							<span class="minutes"></span>m
							<span class="seconds"></span>s
					</div></h5>
					</div>
				</div>
				<div id="more-election-1005" class="section-body clear">
					<h4>Notes</h4>
					<p class="election-notes">Optional description of the events surrounding the election (ie reason for pause, longer than one week for Gen Elections, reason for special election, etc)</p>
					<h4 class="clear">Election Controls</h5>
					<hr class="custom-hr">
					<div class="election-controls">
						<button id="deleteElection-button" class="btn btn-danger bold lightbox-btn" type="button" href="#election-ge1005_delete"><i class="fa fa-times"></i> Delete Election</button>
						<br><br>
						<button id="startElection-button" class="btn btn-success bold lightbox-btn" type="button" href="#election-ge1005_start"><i class="fa fa-play"></i> Manually Start Election</button>
					</div>
				</div>
				<div class="displayNone">
					<div id="election-ge1005_delete">
						<div class="delete-election">
							<h4 class="text-left"><span class="danger-color"><i class="fa fa-times fa-gl"></i></span> Delete Election</h4>
							<hr>
							<form action="" id="delete_ge1005-election-form" method="post">
								<input type="hidden" name="formName" value="delete_ge1005-election-form">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="deleteElection">
										Delete the election <strong>General Election - 1005 (ge1005)</strong> from database.
									</label>
								</div>
								<div class="form-group">
									<button form="delete_ge1005-election-form" class="btn btn-danger" type="submit"><i class="fa fa-times fa-gl"></i> Delete Election</button>
								</div>
							</form>
						</div>
						<p class="modify-disclaimer">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking "close" will not submit the changes.</p class="modify-disclaimer">
					</div>
					<div id="election-ge1005_start">
						<div class="delete-election">
							<h4 class="text-left"><span class="success-color"><i class="fa fa-play fa-gl"></i></span> Start Election</h4>
							<hr>
							<form action="" id="start_ge1005-election-form" method="post">
								<input type="hidden" name="formName" value="start_ge1005-election-form">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="startElection">
										The election <strong>General Election - 1005 (ge1005)</strong> is set to start at <strong>countdown til start</strong>.
									</label>
								</div>
								<div class="form-group">
									<button form="start_ge1005-election-form" class="btn btn-success" type="submit"><i class="fa fa-play fa-gl"></i> Start Election</button>
								</div>
							</form>
						</div>
						<p class="modify-disclaimer">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking "close" will not submit the changes.</p class="modify-disclaimer">
					</div>
				</div>
			</div>
			<div  id="election-1000" class="section-container">
				<div class="election-status section-header success-background more-parent">
					<div class="pull-left">
						<h4 class="bold">General Election - 1000</h4>
						<h5>Mar 17 - Mar 23, 2016</h5>
					</div>
					<div class="pull-right">
						<h4><i class="fa fa-circle-o-notch fa-spin"></i> Active</h4>
						<h5></h5> <div class="text-clock" id="clockdiv1">
							<i class="fa fa-clock-o"></i>
							<span class="days"></span>d
							<span class="hours"></span>h
							<span class="minutes"></span>m
							<span class="seconds"></span>s
					</div>
					</h5>
					</div>
				</div>
				<div id="more-election-1000" class="section-body clear">
					<h4>Notes</h4>
					<p class="election-notes">Optional description for future notes on events surrounding the election (ie reason for pause, longer than one week for Gen Elections, reason for special election, etc)</p>
					<h4 class="clear">Results</h4>
					<hr class="custom-hr increase-margins clear">
					<div class="election-result-container">
					<div id="executive-council" class="election-result">
						<h5 class="text-center">Executive Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="capitalist-council" class="election-result">
						<h5 class="text-center">Capitalist Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="explorer-council" class="election-result">
						<h5 class="text-center">Explorer Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="solider-council" class="election-result">
						<h5 class="text-center">Solider Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
				</div>
					<h4>Election Controls</h5>
					<hr class="custom-hr">
					<div class="election-controls">
						<button id="editElection-button" class="btn btn-warning bold lightbox-btn" type="button" href="#election-ge1000_edit"><i class="fa fa-pencil-square-o"></i> Edit Election</button>
						<button id="deleteElection-button" class="btn btn-danger bold lightbox-btn" type="button" href="#election-ge1000_delete"><i class="fa fa-times"></i> Delete Election</button>
						<br><br>
						<button id="pauseElection-button" class="btn btn-warning bold lightbox-btn" type="button" href="#election-ge1000_pause"><i class="fa fa-pause"></i> Manually Pause Election</button>
						<button id="stopElection-button" class="btn btn-danger bold lightbox-btn" type="button" href="#election-ge1000_stop"><i class="fa fa-stop"></i> Manually Stop Election</button>
					</div>
				</div>
				<div class="displayNone">
					<div id="election-ge1000_edit">
						<div class="edit-election">
							<h4 class="text-left"><span class="warning-color"><i class="fa fa-pencil-square-o fa-gl"></i></span> Edit Election</h4>
							<hr>
							<form action="" id="edit_ge1000-election-form" method="post">
								<input type="hidden" name="formName" value="edit_ge1000-election-form">
								<hr>
								<strong>Remove candidates</strong>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remove-candidate1" value="Candidate1">
										Candidate 1
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remove-candidate2" value="Candidate2">
										Candidate 2
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remove-candidate3" value="Candidate3">
										Candidate 3
									</label>
								</div>
								<hr>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="resetcandidates">
										Reset all of the candidates' votes to 0
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="deletecandidates">
										Remove all candidates from current election
									</label>
								</div>
								<div class="form-group">
									<button form="edit_ge1000-election-form" class="btn btn-warning" type="submit"><i class="fa fa-pencil-square-o fa-gl"></i> Edit Election</button>
								</div>
							</form>
						</div>
						<p class="modify-disclaimer">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking "close" will not submit the changes.</p class="modify-disclaimer">
					</div>
					<div id="election-ge1000_delete">
						<div class="delete-election">
							<h4 class="text-left"><span class="danger-color"><i class="fa fa-times fa-gl"></i></span> Delete Election</h4>
							<hr>
							<form action="" id="delete_ge1000-election-form" method="post">
								<input type="hidden" name="formName" value="delete_ge1000-election-form">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="deleteElection">
										Delete the election <strong>General Election - 1000 (ge1000)</strong>?
									</label>
								</div>
								<div class="form-group">
									<button form="delete_ge1000-election-form" class="btn btn-danger" type="submit"><i class="fa fa-times fa-gl"></i> Delete Election</button>
								</div>
							</form>
						</div>
						<p class="modify-disclaimer">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking "close" will not submit the changes.</p class="modify-disclaimer">
					</div>
					<div id="election-ge1000_pause">
						<div class="pause-election">
							<h4 class="text-left"><span class="warning-color"><i class="fa fa-pause fa-gl"></i></span> Pause Election</h4>
							<hr>
							<form action="" id="pause_ge1000-election-form" method="post">
								<input type="hidden" name="formName" value="pause_ge1000-election-form">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="pauseElection">
										The election <strong>General Election - 1000 (ge1000)</strong> is set to finish at <strong>countdown til finish</strong>. Pause election now?
									</label>
								</div>
								<div class="form-group">
									<button form="pause_ge1000-election-form" class="btn btn-warning" type="submit"><i class="fa fa-pause fa-gl"></i> Pause Election</button>
								</div>
							</form>
						</div>
						<p class="modify-disclaimer">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking "close" will not submit the changes.</p class="modify-disclaimer">
					</div>
					<div id="election-ge1000_stop">
						<div class="stop-election">
							<h4 class="text-left"><span class="danger-color"><i class="fa fa-stop fa-gl"></i></span> Stop Election</h4>
							<hr>
							<form action="" id="stop_ge1000-election-form" method="post">
								<input type="hidden" name="formName" value="stop_ge1000-election-form">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="stopElection">
										The election <strong>General Election - 1000 (ge1000)</strong> is set to finish at <strong>countdown til finish</strong>. Stop election indefinitely?
									</label>
								</div>
								<div class="form-group">
									<button form="stop_ge1000-election-form" class="btn btn-danger" type="submit"><i class="fa fa-stop fa-gl"></i> Stop Election</button>
								</div>
							</form>
						</div>
						<p class="modify-disclaimer">Note: Changes will only be made if you select the checkbox and click the button. Closing this page or clicking "close" will not submit the changes.</p class="modify-disclaimer">
					</div>
				</div>
			</div>
			<div  id="election-1001" class="section-container">
				<div class="election-status section-header warning-background more-parent">
					<div class="pull-left">
						<h4 class="bold">General Election - 1001</h4>
						<h5>Sept 20 - Mar 26, 2015</h5>
					</div>
					<div class="pull-right">
						<h4><i class="fa fa-pause"></i> Paused</h4>
						<h5><i class="fa fa-clock-o"></i> time remaining</h5>
					</div>
				</div>
				<div id="more-election-1001" class="section-body clear">
					<div class="election-result-container">
						<div id="executive-council" class="election-result">
							<h5 class="text-center">Executive Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
						<div id="capitalist-council" class="election-result">
							<h5 class="text-center">Capitalist Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
						<div id="explorer-council" class="election-result">
							<h5 class="text-center">Explorer Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
						<div id="solider-council" class="election-result">
							<h5 class="text-center">Solider Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<h4>Election Controls</h5>
					<hr class="custom-hr">
					<div class="election-controls">
						<button id="editElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pencil-square-o"></i> Edit Election</button>
						
						<button id="deleteElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-times"></i> Delete Election</button>
						<br><br>
						<button id="startElection-button" class="btn btn-success bold" type="button"><i class="fa fa-play"></i> Manually Start Election</button>
						<!--<button id="pauseElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pause"></i> Manually Pause Election</button>-->
						<button id="stopElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-stop"></i> Manually Stop Election</button>
					</div>
				</div>
			</div>
			<div  id="election-1002" class="section-container election-done">
				<div class="election-status section-header done-background more-parent">
					<div class="pull-left">
						<h4 class="bold">General Election - 1002</h4>
						<h5>Sept 20 - Mar 26, 2015</h5>
					</div>
					<div class="pull-right">
						<h4><i class="fa fa-check"></i> Done</h4>
						<h5><i class="fa fa-clock-o"></i> time finished</h5>
					</div>
				</div>
				<div id="more-election-1002" class="section-body clear">
					<div class="election-result-container">
					<div id="executive-council" class="election-result">
						<h5 class="text-center">Executive Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="capitalist-council" class="election-result">
						<h5 class="text-center">Capitalist Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="explorer-council" class="election-result">
						<h5 class="text-center">Explorer Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="solider-council" class="election-result">
						<h5 class="text-center">Solider Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
				</div>
					<h4>Election Controls</h5>
					<hr class="custom-hr">
					<div class="election-controls">
						<!--<button id="editElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pencil-square-o"></i> Edit Election</button>-->
						<!---->
						<button id="deleteElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-times"></i> Delete Election</button>
						<!--<br><br>-->
						<!--<button id="startElection-button" class="btn btn-success bold" type="button"><i class="fa fa-play"></i> Manually Start Election</button>-->
						<!--<button id="pauseElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pause"></i> Manually Pause Election</button>-->
						<!--<button id="stopElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-stop"></i> Manually Stop Election</button>-->
					</div>
				</div>
			</div>
			<div  id="election-1003" class="section-container election-deleted">
				<div class="election-status section-header danger-background more-parent">
					<div class="pull-left">
						<h4 class="bold">General Election - 1003</h4>
						<h5>Sept 20 - Mar 26, 2015</h5>
					</div>
					<div class="pull-right">
						<h4><i class="fa fa-times"></i> Deleted</h4>
						<h5><i class="fa fa-calendar-o"></i> date/time deleted</h5>
					</div>
				</div>
				<div id="more-election-1003" class="section-body clear">
					<div class="election-result-container">
					<div id="executive-council" class="election-result">
						<h5 class="text-center">Executive Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="capitalist-council" class="election-result">
						<h5 class="text-center">Capitalist Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="explorer-council" class="election-result">
						<h5 class="text-center">Explorer Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
					<div id="solider-council" class="election-result">
						<h5 class="text-center">Solider Council</h5>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 1</h6>
							<h6 class="small pull-right">18 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
									<span class="sr-only">31.58% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 2</h6>
							<h6 class="small pull-right">12 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
									<span class="sr-only">21.05% Votes</span>
								</div>
							</div>
						</div>
						<div class="candidate">
							<h6 class="small pull-left">Candidate 3</h6>
							<h6 class="small pull-right">27 votes</h6>
							<div class="progress clear">
								<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
									<span class="sr-only">47.37% Votes</span>
								</div>
							</div>
						</div>
					</div>
				</div>
					<h4>Election Controls</h5>
					<hr class="custom-hr">
					<div class="election-controls">
						<button id="restoreElection-button" class="btn btn-success bold" type="button"><i class="fa fa-arrow-up"></i> Restore Election</button>
						<!--<button id="editElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pencil-square-o"></i> Edit Election</button>-->
						<!---->
						<!--<button id="deleteElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-times"></i> Delete Election</button>-->
						<!--<br><br>-->
						<!--<button id="startElection-button" class="btn btn-success bold" type="button"><i class="fa fa-play"></i> Manually Start Election</button>-->
						<!--<button id="pauseElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pause"></i> Manually Pause Election</button>-->
						<!--<button id="stopElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-stop"></i> Manually Stop Election</button>-->
					</div>
				</div>
			</div>
			<div  id="election-1004" class="section-container">
				<div class="election-status section-header danger-background more-parent">
					<div class="pull-left">
						<h4 class="bold">General Election - 1004</h4>
						<h5>Sept 20 - Mar 26, 2015</h5>
					</div>
					<div class="pull-right">
						<h4><i class="fa fa-stop"></i> Stopped</h4>
						<h5><i class="fa fa-clock-o"></i> time stopped</h5>
					</div>
				</div>
				<div id="more-election-1004" class="section-body clear">
					<div class="election-result-container">
						<div id="executive-council" class="election-result">
							<h5 class="text-center">Executive Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
						<div id="capitalist-council" class="election-result">
							<h5 class="text-center">Capitalist Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
						<div id="explorer-council" class="election-result">
							<h5 class="text-center">Explorer Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
						<div id="solider-council" class="election-result">
							<h5 class="text-center">Solider Council</h5>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 1</h6>
								<h6 class="small pull-right">18 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="31.58" aria-valuemin="0" aria-valuemax="100" style="width:31.58%">
										<span class="sr-only">31.58% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 2</h6>
								<h6 class="small pull-right">12 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="21.05" aria-valuemin="0" aria-valuemax="100" style="width:21.05%">
										<span class="sr-only">21.05% Votes</span>
									</div>
								</div>
							</div>
							<div class="candidate">
								<h6 class="small pull-left">Candidate 3</h6>
								<h6 class="small pull-right">27 votes</h6>
								<div class="progress clear">
									<div class="progress-bar" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width:47.37%">
										<span class="sr-only">47.37% Votes</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<h4>Election Controls</h5>
					<hr class="custom-hr">
					<div class="election-controls">
						<!--<button id="editElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pencil-square-o"></i> Edit Election</button>-->
						<!---->
						<button id="deleteElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-times"></i> Delete Election</button>
						<br><br>
						<!--<button id="startElection-button" class="btn btn-success bold" type="button"><i class="fa fa-play"></i> Manually Start Election</button>-->
						<!--<button id="pauseElection-button" class="btn btn-warning bold" type="button"><i class="fa fa-pause"></i> Manually Pause Election</button>-->
						<!--<button id="stopElection-button" class="btn btn-danger bold" type="button"><i class="fa fa-stop"></i> Manually Stop Election</button>-->
					</div>
				</div>
			</div>
			
			
<?php
	include $prefix.'footer.php';
?>