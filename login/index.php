<?php
	$prefix = "";
	$pageName = "Login";
	$customHeaderIcon = "<i class=\"fa fa-lock\"></i>";
	$supportPrefix = "../../../support/";
	$dashboardSupportPrefix = $prefix."../support/";
	$dashboardRoot = "../";
	include $dashboardSupportPrefix.'siteheader.php';
	
	if($_SESSION['loggedIn'] && $_GET['logout'] && count($_POST) == 0){
		//perform logout and return to homepage
		$_SESSION = array();
		session_destroy();
		header("Location: ");
		$alert .= createAlert("info","You have been successfully logged out.");
	}
	
	if(isset($_SESSION['loggedIn']) && !isset($_GET['returnUrl']))
		header('Location: ../user/');
	
	if(count($_POST)>0){
		$_POST['user'] = strtolower($_POST['user']);
		if (strpos($_POST['user'], '@') !== false)
			$query = "SELECT password FROM users WHERE email='".$_POST['user']."'";
		else
			$query = "SELECT password FROM users WHERE username='".$_POST['user']."'";
			
		$passwordHash = $conn->query($query);
		if (mysqli_num_rows($passwordHash) > 0) {
			while($row = mysqli_fetch_assoc($passwordHash)) {
				if(password_verify($_POST['password'],$row['password'])){
					$_SESSION['loggedIn'] = true;
					if(isset($_GET['returnUrl']) && $_SESSION['loggedIn'])
						header('Location: ../'.$_GET['returnUrl']);
					else if(isset($_SESSION['loggedIn']))
						header('Location: ../user/');
					$alert .= createAlert("info","Successfully logged in!");
					
					// *Fetch tags and store in session 
					// *(MAY NOT NEED; PENDING phpBB)
					// *
					// $query = "SELECT tags FROM users WHERE email='".$_POST['email']."'";
					// $tags = $conn->query($query);
					// if(mysqli_num_rows($tags)>0){
					// 	while($tagsRow = mysqli_fetch_assoc($tags)){
					// 		$_SESSION['tags'] = $row['tags'];
					// 	}
					// }
				}else{
					$alert .= createAlert("danger","Incorrect username or password. Complex password? Try clicking the lock next to the password field to see your password.");
				}
			}
		}else{
			$alert .= createAlert("danger","Unknown username.");
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
					<div id="login">
						<form id="login-form" action="" class="form-horizontal" method="post">
							<input type="hidden" name="formName" value="login-form">
							<input type="hidden" name="loggedIn" value="false">
							<div class="inline-form-column">
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-user"></i></div>
											<input type="text" class="form-control" id="user" placeholder="Username or Email" name="user" autofocus required>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
										<div class="input-group">
											<div id="display-password" class="input-group-addon"><i class="fa fa-lock"></i></div>
											<input type="password" class="form-control" id="password" placeholder="Password" name="password" required>
										</div>
									</div>
								</div>
								<!--<div class="form-group">-->
								<!--	<div class="col-sm-offset-2 col-sm-10">-->
								<!--		<div class="checkbox">-->
								<!--			<label>-->
								<!--				<input type="checkbox"> Remember me-->
								<!--			</label>-->
								<!--		</div>-->
								<!--	</div>-->
								<!--</div>-->
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
										<button form="login-form" type="submit" value="ture" class="btn btn-default"><i class="fa fa-sign-in"></i> Sign in</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<!--<div id="create-account">-->
						
					<!--</div>-->
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