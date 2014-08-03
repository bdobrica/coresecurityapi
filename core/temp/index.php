<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
#include (dirname(__FILE__).'/card/mobilpay.php');


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Bilete de Succes &raquo;</title>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="http://www.biletedesucces.ro/wp-content/plugins/wp-crm/remote/api.js"></script>

		<link rel="stylesheet" type="text/css" href="http://www.biletedesucces.ro/wp-content/plugins/wp-crm/remote/api.css" />
	</head>
	<body>
		<div class="wp-crm-form-body">
			<div>
				<form action="" method="post">
				<label>TID</label><input type="text" name="tid" value="" />
					<br />
				<label>MID</label><input type="text" name="mid" value="" />
					<br />
				<label>SUBJECT</label><input type="text" name="subject" value="" />
					<br />
				<label>CONTENT</label>
					<br />
				<textarea name="content"></textarea>
					<br />
				<input type="submit" name="q" value="SAVE" />
			</div>
		</div>
		<div class="wp-crm-form-shadow"></div>
	</body>
</html>
