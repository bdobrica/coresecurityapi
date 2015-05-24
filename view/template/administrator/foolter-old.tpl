	<?php wp_footer();
	$URL = get_bloginfo('stylesheet_directory'); ?>
	
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->

			<script src="<?php echo $URL; ?>/assets/js/jquery-2.1.0.min.js"></script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script src="assets/js/jquery-1.11.0.min.js"></script>
	
	<![endif]-->

	<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='<?php echo $URL; ?>/assets/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
		</script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script type="text/javascript">
	 	window.jQuery || document.write("<script src='<?php echo $URL; ?>/assets/js/jquery-1.11.0.min.js'>"+"<"+"/script>");
		</script>
		
	<![endif]-->
	<script src="<?php echo $URL; ?>/assets/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/bootstrap.min.js"></script>

	<!-- page scripts -->
	<script src="<?php echo $URL; ?>/assets/js/jquery.icheck.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.ui.touch-punch.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.sparkline.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/fullcalendar.min.js"></script>
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo $URL; ?>/assets/js/excanvas.min.js"></script><![endif]-->
	<script src="<?php echo $URL; ?>/assets/js/jquery.flot.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.flot.pie.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.flot.stack.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.flot.resize.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.flot.time.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.autosize.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.placeholder.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/moment.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/daterangepicker.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.easy-pie-chart.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/dataTables.bootstrap.min.js"></script>
	
	<!-- theme scripts -->
	<script src="<?php echo $URL; ?>/assets/js/custom.min.js"></script>
	<script src="<?php echo $URL; ?>/assets/js/core.min.js"></script>
	
	<!-- inline scripts related to this page -->
	<script src="<?php echo $URL; ?>/assets/js/pages/index.js"></script>
	
	<!-- end: JavaScript-->
</body>
</html>
