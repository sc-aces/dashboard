<?php
	$supportPrefix = "../../../support/";
    $dashboardSupportPrefix = $prefix."../support/";
    $dashboardRoot = "../";
	include $dashboardSupportPrefix.'siteheader.php';
?>

		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-3 col-md-2 sidebar">
					<h2>Admin</h2>
					<ul class="nav nav-sidebar">
						<li <?php if($pageName == "Overview") echo "class=\"active\"";?>>
								<a href="<?php echo $prefix;?>"><i class="fa fa-server"></i> Overview</a>
						</li>
						<!--li><a href="#">Reports</a></li>
						<li><a href="#">Analytics</a></li>
						<li><a href="#">Export</a></li-->
					</ul>
					<ul class="nav nav-sidebar">
						<li <?php if($pageName == "Squadrons") echo "class=\"active\"";?>>
								<a href="<?php echo $prefix;?>squadrons/"><i class="fa fa-rocket"></i> Squadrons</a>
						</li>
						<li <?php if($pageName == "Ballot Box") echo "class=\"active\"";?>>
								<a href="<?php echo $prefix;?>balletbox/"><i class="fa fa-archive"></i> Ballot Box</a>
						</li>
						<li <?php if($pageName == "Dynamic Fleet") echo "class=\"active\"";?>>
							<a href="<?php echo $prefix;?>dynamicfleet/"><i class="fa fa-space-shuttle"></i> Dynamic Fleet</a>
						</li>
						<li <?php if($pageName == "Council Voting") echo "class=\"active\"";?>>
							<a href="<?php echo $prefix;?>councilvoting/"><i class="fa fa-check-square-o"></i> Council Voting</a>
						</li>
						<li <?php if($pageName == "Applications") echo "class=\"active\"";?>>
							<a href="<?php echo $prefix;?>applications/"><i class="fa fa-file-text"></i> Applications</a>
						</li>
						<li <?php if($pageName == "Bug Reporting") echo "class=\"active\"";?>>
							<a href="<?php echo $prefix;?>bugreporting/"><i class="fa fa-bug"></i> Bug Reporting</a>
						</li>
						<li <?php if($pageName == "Files") echo "class=\"active\"";?>>
							<a href="<?php echo $prefix;?>files/"><i class="fa fa-files-o"></i> Files</a>
						</li>
					</ul>
					<?php include $dashboardSupportPrefix.'sitefooter.php'; ?>
				</div>
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
					<h1 class="page-header"><?php if(customHeaderIcon) echo $customHeaderIcon; echo ' '; echo $pageName; ?></h1>