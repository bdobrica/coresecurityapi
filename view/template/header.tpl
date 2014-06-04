<?php $URL = get_bloginfo ('stylesheet_directory'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	
	<!-- start: Meta -->
	<meta charset="utf-8">
	<title><?php bloginfo('name'); ?></title>
	<meta name="description" content="<?php bloginfo('name'); echo ' - '; bloginfo('description'); ?>">
	<meta name="author" content="Bogdan Dobrica / Core Security Advisers">
	<meta name="keyword" content="Complete E-Commerce Solution for SMEs">
	<!-- end: Meta -->
	
	<!-- start: Mobile Specific -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- end: Mobile Specific -->
	
	<!-- start: CSS -->
	<link href="<?php echo $URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo $URL; ?>/assets/css/style.min.css" rel="stylesheet">
	<link href="<?php echo $URL; ?>/assets/css/retina.min.css" rel="stylesheet">
	<link href="<?php echo $URL; ?>/assets/css/print.css" rel="stylesheet" type="text/css" media="print"/>
	<link href="<?php echo $URL; ?>/style.css?v=0.1" rel="stylesheet">
	<!-- end: CSS -->
	

	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		
	  	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script src="<?php echo $URL; ?>/assets/js/respond.min.js"></script>
		
	<![endif]-->
	
	<!-- start: Favicon and Touch Icons -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $URL; ?>/assets/ico/apple-touch-icon-144-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $URL; ?>/assets/ico/apple-touch-icon-114-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $URL; ?>/assets/ico/apple-touch-icon-72-precomposed.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo $URL; ?>/assets/ico/apple-touch-icon-57-precomposed.png">
	<link rel="shortcut icon" href="<?php echo $URL; ?>/assets/ico/favicon.png">
	<!-- end: Favicon and Touch Icons -->
	<?php wp_head(); ?>
</head>

<body>
