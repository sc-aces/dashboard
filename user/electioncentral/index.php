<?php
	$allowedTags = array("3");
	$prefix = "../";
	$pageName = "Election Central";
	$customHeaderIcon = "<i class=\"fa fa-archive\"></i>";
	include $prefix.'header.php';
	if(count($_POST)>0){
		var_dump($_POST);
	}
?>
					
					<p>Description and instructions for voting system</p>
					<div class="clock2-container center-block"><div style="zoom:.55" class="clock2"></div></div>
					<hr class="custom-hr increase-margins">
						<div class="election-result-container">
							<div id="executive-council" class="election-result">
								<h5 class="text-center">Executive Council</h5>
								<form method="post" class="election-form" id="executive">
									<div class="radio">
										<label>
											<input type="radio" name="executive" id=abstain" value=abstain">
											Abstain
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="executive" id=option1" value=option1">
											Candidate 1
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="executive" id=option2" value=option2">
											Candidate 2
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="executive" id=option3" value=option3">
											Candidate 3
										</label>
									</div>
									<button form="executive" type="submit" class="center-block btn btn-default"><i class="fa fa-check-square-o "></i> Vote</button><br>
								</form>
							</div>
							<div id="capitalist-council" class="election-result">
								<h5 class="text-center">Capitalist Council</h5>
								<form method="post" class="election-form" id="capitalist">
									<div class="radio">
										<label>
											<input type="radio" name="capitalist" id="abstain" value="abstain">
											Abstain
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="capitalist" id="option1" value="option1">
											Candidate 1
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="capitalist" id="option2" value="option2">
											Candidate 2
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="capitalist" id="option3" value="option3">
											Candidate 3
										</label>
									</div>
									<button form="capitalist" type="submit" class="center-block btn btn-default"><i class="fa fa-check-square-o "></i> Vote</button><br>
								</form>
							</div>
							<div id="explorer-council" class="election-result">
								<h5 class="text-center">Explorer Council</h5>
								<form method="post" class="election-form" id="explorer">
									<div class="radio">
										<label>
											<input type="radio" name="explorer" id="abstain" value="abstain">
											Abstain
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="explorer" id="option1" value="option1">
											Candidate 1
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="explorer" id="option2" value="option2">
											Candidate 2
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="explorer" id="option3" value="option3">
											Candidate 3
										</label>
									</div>
									<button form="explorer" type="submit" class="center-block btn btn-default"><i class="fa fa-check-square-o "></i> Vote</button><br>
								</form>
							</div>
							<div id="solider-council" class="election-result">
								<h5 class="text-center">Solider Council</h5>
								<form method="post" class="election-form" id="solider">
									<div class="radio">
										<label>
											<input type="radio" name="solider" id="abstain" value="abstain">
											Abstain
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="solider" id="option1" value="option1">
											Candidate 1
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="solider" id="option2" value="option2">
											Candidate 2
										</label>
									</div>
									<div class="radio">
										<label>
											<input type="radio" name="solider" id="option3" value="option3">
											Candidate 3
										</label>
									</div>
									<button form="solider" type="submit" class="center-block btn btn-default"><i class="fa fa-check-square-o "></i> Vote</button><br>
								</form>
							</div>
						</div>
					<hr class="custom-hr increase-margins">
<?php
	include $prefix.'footer.php';
?>