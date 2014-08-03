<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$invoice = new WP_CRM_Invoice ($_GET['inv']);

$mentions = '';
foreach ($_POST as $key => $val)
	$mentions .= $key.' = '.$val."\n";

$invoice->set ('mentions', $mentions);
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC 
"-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Extreme Training - Eroare Plata</title>
	</head>
	<body>
		Au aparut erori in procesarea platii dumneavoastra. Apasati <a href="/ro/">aici</a> pentru a reveni pe site-ul Extreme Training.
	</body>
</html>
