<?php
	$prefix = "../";
	$pageName = "Squadrons";
	$customHeaderIcon = "<i class=\"fa fa-rocket\"></i>";
	include $prefix.'header.php';
?>

<h1 class="text-center">COMING SOON!</h1>
			<hr class="custom-hr increase-margins clear">
			<div id="add-ship">
				<h4 class="text-left"><span class="success-color"><i class="fa fa-plus fa-gl"></i></span> Add Squadron</h4>
				<p class="text-justify">Adds a new suqadron to the database.</p>
				<form action="" id="add-squadron-form" class="form-horizontal" method="post">
					<input type="hidden" name="formName" value="add-squadron-form">
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label">Squadron Name</label>
						<div class="col-sm-10">
							<div class="input-group">
								<div class="input-group-addon"><i class="fa fa-space-shuttle fa-rotate-270 fa-gl"></i></div>
								<input type="text" class="form-control" name="name" placeholder="Squadron" required>
							</div>
						</div>
					</div>
					<div class="form-group">
						 <div class="col-sm-offset-2 col-sm-10">
							<button form="add-squadron-form" class="btn btn-success" type="submit"><i class="fa fa-plus fa-gl"></i> Add Squadron</button>
						</div>
					</div>
				</form>
				<hr class="custom-hr increase-margins clear">
			</div>
			<div class="section-container">
				<div id="squadron-data" class="more section-header">
					<h3>Squadron Data</h3>
				</div>
				<div id="more-squadron-data" class="section-body">
					Squadron data
				</div>
			</div>

<?php
	include $prefix.'footer.php';
?>