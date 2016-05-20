<?php
	$prefix = "";
	$pageName = "Profile";
	$customHeaderIcon = "<i class=\"fa fa-user\"></i>";
	
	include 'header.php';
	if(count($_POST)>0){
		setcookie( "theme", $_POST['theme'], strtotime( '+30 days' ),'/dashboard/');
		header("Refresh:0");
	}
?>
<p>User settings and information</p>
<hr class="custom-hr increase-margins">
<h4><i class="fa fa-cog"></i> Settings</h4>
<form id="user-settings-form" class="form-horizontal" action="" method="post">
	<div class="form-group">
		<label for="inputEmail3" class="col-sm-2 control-label">Theme</label>
		<div class="col-sm-10">
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-paint-brush"></i></div>
				<select class="form-control" name="theme">
					<option value="dark">Dark</option>
					<option value="light">Light</option>
				</select>
			</div>
		</div>
	</div>
	<!--<div class="form-group">-->
	<!--	<label for="inputPassword3" class="col-sm-2 control-label">Password</label>-->
	<!--	<div class="col-sm-10">-->
	<!--		<input type="password" class="form-control" id="inputPassword3" placeholder="Password">-->
	<!--	</div>-->
	<!--</div>-->
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
			<button type="submit" form="user-settings-form" class="btn btn-default"><i class="fa fa-floppy-o"></i> Save Settings</button>
		</div>
	</div>
</form>
<!--<hr class="custom-hr increase-margins">-->

<?php
	include $prefix.'footer.php';
?>