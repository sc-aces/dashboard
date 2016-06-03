<?php
	$prefix = "";
	$pageName = "Create Account";
	$customHeaderIcon = "<i class=\"fa fa-user-plus\"></i>";
	$supportPrefix = "../../../../support/";
	$dashboardSupportPrefix = $prefix."../../support/";
	$dashboardRoot = "../../";
	include $dashboardSupportPrefix.'siteheader.php';
	
	$currentUser = "";
	$currentPass = "";
	
	if(!$_SESSION['loggedIn']){
		header("Location: ");
	}
	
	// if(isset($_SESSION['loggedIn']) && !isset($_GET['returnUrl']))
	// 	header('Location: ../user/');
	
	if(count($_POST)>0){
		if($_POST['password'] != $_POST['re-password'])
			$alert .= createAlert("danger","Passwords don't match!");
		else{
			$result = $conn->query("SELECT * FROM $db.users WHERE `username`='".$_POST['username']."'");
			if(mysqli_num_rows($result) > 0)
				$alert .= createAlert("danger","Username already exists. Please contact an admin.");
			else{
				$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
				$result = $conn->query("INSERT INTO $db.users (`username`, `email`, `password`, `verify`, `tags`, `primary_squadron`) VALUES ('".$_POST['username']."', '".$_POST['email']."', '$password', 'false', '".$_POST['tags']."', 'Freelancer')");
				if($result)
					$alert .= createAlert("success","Account successfully created!");
				else
					$alert .= createAlert("danger","Account creation failed!");
			}
		}
	}
?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-3 col-md-2 sidebar">
					<?php include $dashboardSupportPrefix.'sitefooter.php'?>
				</div>
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
					<h1 class="page-header"><?php if(customHeaderIcon) echo $customHeaderIcon; echo ' '; echo $pageName; ?></h1>
					<div class="alert-section">
						<?php echo $alert ?>
					</div>
					<div class="create-account">
						<div id="q-1" class="question">
							<h4>Do you have an ACES Enjin account?</h4>
							<button id="a-2" class="btn btn-default correct-answer">Yes!</button>
							<a href="http://www.starcitizenaces.org/join"><button class="btn btn-default">Enjin? What's that?</button></a>
						</div>
						<div id="q-2" class="question displayNone">
							<h4>Welcome! We'll need some information to get started.<br>Do you know your Enjin ID?</h4>
							<button id="a-3" class="btn btn-default correct-answer">Got it right here!</button>
							<button class="btn btn-default lightbox-btn cboxElement" type="button" href="#help-enjin-id">Enjin ID? What's that?</button>
							<div class="displayNone">
								<div id="help-enjin-id">
									<div class="edit-election">
										<h4 class="text-left"><i class="fa fa-question-circle"></i> Enjin ID Help</h4>
										<hr>
										<p>Your Enjin ID is the last part of the URL of your Enjin profile. Typically it is a number consisting of 5 to 8 digits.</p>
										<p>For example, your url could look like <strong>http://www.starcitizenaces.org/profile/2266156</strong>. Your ID would be <strong>2266156</strong>.</p>
									</div>
								</div>
							</div>
						</div>
						<div id="q-3" class="question displayNone">
							<h4>Perfect!<br>Do you know your Voting Token?</h4>
							<button id="a-4" class="btn btn-default correct-answer">Yep, ready to roll!</button>
							<button class="btn btn-default lightbox-btn cboxElement" type="button" href="#help-voting-token">What's this token thingy you speak of?</button>
							<div class="displayNone">
								<div id="help-voting-token">
									<div class="edit-election">
										<h4 class="text-left"><i class="fa fa-question-circle"></i> Voting Token Help</h4>
										<hr>
										<p>Your Voting Token is like your password to be able to vote in <a href="http://www.starcitizenaces.org/profile/5014575">TheDevideo</a>'s Election Module.</p>
										<p>If you don't have one or forgot yours, please contact <a href="http://www.starcitizenaces.org/profile/5014575">TheDevideo</a> or 
											<a href="http://www.starcitizenaces.org/profile/2266156">MobiusKiller</a> via an Enjin PM.</p>
									</div>
								</div>
							</div>
						</div>
						<div id="q-4" class="question form-container displayNone">
							<h4>Awesome! Fill out the form below so we know who you are.</h4>
							<input type="hidden" name="formName" value="validate-user">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-user"></i></div>
									<input type="text" class="form-control" id="enjin-id" placeholder="Enjin ID" name="enjin-id" autofocus required>
								</div>
							</div>
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-key"></i></div>
									<input type="text" class="form-control" id="voting-token" placeholder="Voting Token" name="voting-token" required>
								</div>
							</div>
							<button id="id-token-submit" type="submit" value="ture" class="btn btn-default"><i class="fa fa-sign-in"></i> Submit</button>
							<br><br>
						<div id="validate-alert"></div>
						</div>
						<div id="q-5" class="question form-container displayNone">
							<h4>Hello, <span id="enjin-username-header"></span>! Please create your account below.</h4>
							<form id="create-account-form" action="" method="post">
								<input type="hidden" name="formName" value="create-account">
								<input id="tags" type="hidden" name="tags" value="3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-envelope"></i></div>
										<input type="email" class="form-control" id="email" placeholder="example@somedomain.com" name="email" autofocus required autocomplete="off">
									</div>
								</div>
								<div id="email-alert">
									
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-user"></i></div>
										<input type="text" class="form-control" id="enjin-username" placeholder="Username" name="username" required readonly>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-lock"></i></div>
										<input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
									</div>
								</div>
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><i class="fa fa-lock"></i></div>
										<input type="password" class="form-control" id="re-password" placeholder="Confirm Password" name="re-password" required>
									</div>
								</div>
								<div id="password-alert">
								
								</div>
								<button id="create-account-button" form="create-account-form" type="submit" value="ture" class="btn btn-default" disabled><i class="fa fa-user-plus"></i> Create Account</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script src="<?php echo $supportPrefix;?>bootstrap/js/bootstrap.min.js"></script>
		<script src="<?php echo $supportPrefix;?>lightbox/dist/js/lightbox.min.js"></script>
		<script src="<?php echo $supportPrefix;?>colorbox/jquery.colorbox-min.js"></script>
		<script src="<?php echo $supportPrefix;?>flipclock/compiled/flipclock.min.js"></script>
		<script src="<?php echo $dashboardSupportPrefix;?>js/main.js"></script>
		<?php if(file_exists("local.js")) echo "<script src=\"local.js\"></script>";?>
		<?php if($customJs) echo $customJs; ?>
		<?php $conn->close(); ?>
  </body>
</html>