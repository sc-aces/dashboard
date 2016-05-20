<?php
	$prefix = "";
	$pageName = "Copyrights and Licenses";
	$customHeaderIcon = "<i class=\"fa fa-copyright\"></i>";
	$supportPrefix = "../../support/";
    $dashboardSupportPrefix = $prefix."";
    $dashboardRoot = "../";
	include $dashboardSupportPrefix.'siteheader.php';
?>

		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-3 col-md-2 sidebar">
					<?php include 'sitefooter.php'; ?>
				</div>
				<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
					<h1 class="page-header"><?php if(customHeaderIcon) echo $customHeaderIcon; echo ' '; echo $pageName; ?></h1>
					<?php if(count($_POST)>0)	var_dump($_POST);?>
					
					<h3>Association of Capitalists, Soliders, and Explorers (ACES) <a href="http://www.starcitizenaces.org/"><i class="fa fa-external-link"></i></a></h3>
					<p class="indent">This application was created for ACES as part of the ACES 2.0 project. For more information about ACES visit <a href="http://www.starcitizenaces.org/">http://www.starcitizenaces.org/</a></p>
					<hr class="custom-hr increase-margins">
					
					<h3>Cloud Imperium Games <a href="https://cloudimperiumgames.com/"><i class="fa fa-external-link"></i></a></h3>
					<p class="indent">This is a <a href="https://robertsspaceindustries.com/">Star Citizen</a> fan site. Content not original to this site is © 2015 Cloud Imperium Games Corporation and Roberts Space Industries Corp. Star Citizen™, Squadron 42™, Roberts Space Industries™, and Cloud Imperium Games™ are trademarks of Cloud Imperium Games Corporation. All rights reserved.</p>
					<hr class="custom-hr increase-margins">
					
					<h3>Bootstrap <a href="http://getbootstrap.com/"><i class="fa fa-external-link"></i></a></h3>
					<p class="indent"><a href="https://github.com/twbs/bootstrap/blob/master/LICENSE">View Bootstrap's license</a>.</p>
					<hr class="custom-hr increase-margins">
					
					<h3>Font Awesome <a href="http://fontawesome.io/"><i class="fa fa-external-link"></i></a></h3>
					<p class="indent">by Dave Gandy</p>
					<hr class="custom-hr increase-margins">
					
					<h3>Colorbox <a href="http://www.jacklmoore.com/colorbox/"><i class="fa fa-external-link"></i></a></h3>
					<p class="indent">By Jack Moore. <a href="https://github.com/jackmoore/colorbox/blob/master/LICENSE.md">View Colorbox's license</a>.</p>
					<hr class="custom-hr increase-margins">
					
					<h3>Lightbox <a href="http://lokeshdhakar.com/projects/lightbox2/"><i class="fa fa-external-link"></i></a></h3>
					<p class="indent">By Lokesh Dhakar. <a href="https://raw.githubusercontent.com/lokesh/lightbox2/master/LICENSE">View Lightbox's license</a>.</p>
					<hr class="custom-hr increase-margins">
            	</div>
        	</div>
      	</div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script src="<?php echo $supportPrefix;?>bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo $supportPrefix;?>flipclock/compiled/flipclock.min.js"></script>
    <script src="<?php echo $dashboardSupportPrefix;?>js/main.js"></script>
    <?php if($customJs) echo $customJs; ?>
  </body>
</html>