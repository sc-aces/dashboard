					</div>
				</div>
			</div>
		</div>
		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script src="<?php echo $supportPrefix;?>bootstrap/js/bootstrap.min.js"></script>
		<!--<script src="<?php echo $supportPrefix;?>lightbox/dist/js/lightbox.min.js"></script>-->
		<script src="<?php echo $supportPrefix;?>colorbox/jquery.colorbox-min.js"></script>
		<script src="<?php echo $supportPrefix;?>flipclock/compiled/flipclock.min.js"></script>
		<script src="<?php echo $dashboardSupportPrefix;?>js/main.js"></script>
		<?php if(file_exists("local.js")) echo "<script src=\"local.js\"></script>";?>
		<?php if($customJs) echo $customJs; ?>
		<?php $conn->close(); ?>
	</body>
</html>