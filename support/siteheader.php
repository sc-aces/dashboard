<?php
	include 'control.php';
	if(!$_COOKIE['theme'])
		$theme = $defaultTheme;
	else
		$theme = $_COOKIE['theme'];
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<meta name="theme-color" content="#dcbc00">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="<?php echo $supportPrefix; ?>/images/favicon.ico">
		<title>Dashboard - ACES</title>

		<!-- Bootstrap core CSS -->
		<link href="<?php echo $supportPrefix;?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo $supportPrefix;?>flipclock/compiled/flipclock.css" rel="stylesheet">
		<link href="<?php echo $supportPrefix;?>lightbox/dist/css/lightbox.min.css" rel="stylesheet">
		<link href="<?php echo $supportPrefix;?>colorbox/colorbox.css" rel="stylesheet"></link>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

		<!-- Custom styles for this template -->
		<link href="<?php echo $dashboardSupportPrefix;?>css/dashboard.css" rel="stylesheet">
		<link href="<?php echo $dashboardSupportPrefix;?>css/css.css" rel="stylesheet">
		<link href="<?php echo $dashboardSupportPrefix;?>css/themes/<?php echo $theme;?>.css" rel="stylesheet">
		<?php if($customCss) echo $customCss; ?>
	</head>

	<body>
		<?php echo "<input id=\"server-time\" type=\"hidden\" value=\"".$serverTime."\">";?>
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">ACES</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="<?php echo $dashboardRoot.$prefix;?>admin/">Admin</a></li>
						<!--li><s><a href="">Settings</a></s></li-->
						<li><a href="<?php echo $dashboardRoot.$prefix;?>user/">Profile</a></li>
					<?php if(!isset($_SESSION['loggedIn'])): ?>
						<li><a href="<?php echo $dashboardRoot.$prefix;?>login/">Login</a></li>
					<?php else: ?>
						<li><a href="<?php echo $dashboardRoot.$prefix;?>login/?logout=true">Logout</a></li>
					<?php endif; ?>
							
					</ul>
				</div>
			</div>
		</nav>