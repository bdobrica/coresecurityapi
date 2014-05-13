<?php
session_start ();
$cart = unserialize($_SESSION['WP_CRM_SHOP']);
$c = 0;
if (!empty($cart['p']))
foreach ($cart['p'] as $key => $val)
	$c += $val[1];
echo $c;
?>
