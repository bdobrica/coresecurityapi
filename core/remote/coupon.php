<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
#include (dirname(__FILE__).'/card/mobilpay.php');

spl_autoload_register (function ($class) {
	include (dirname(dirname(__FILE__)) . '/class/' . strtolower($class) . '.php');
	});

ini_set ('display_errors', TRUE);

$coupon = null;
if ($_GET['coupon'])
	$_POST['coupon'] = $_GET['coupon'];

if ($_POST['coupon']) {
	try {
		$coupon = new WP_CRM_Coupon (strtoupper(trim($_POST['coupon'])));
		echo (string) $coupon;
		}
	catch (WP_CRM_Exception $wp_crm_exception) {
		}
	}
?>
