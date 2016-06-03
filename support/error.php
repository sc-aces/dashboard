<?php
	$allowedTags=array("3");
	$prefix = "";
	$pageName = "Error";
	$customHeaderIcon = "<i class=\"fa fa-exclamation-triangle\"></i>";
	$supportPrefix = "../../../support/";
	$dashboardSupportPrefix = $prefix."../support/";
	$dashboardRoot = "../";
	include $dashboardSupportPrefix.'siteheader.php';
	
	$alert .=createAlert("danger","You do not have sufficient permissions to access this page!");
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
<?php
	include '../admin/footer.php';
?>