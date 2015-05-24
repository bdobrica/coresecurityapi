<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-blog-header.php');
include (dirname(dirname(__FILE__)) . '/common.php');

try {
	$product = new WP_CRM_Product ($_GET['p']);
	}
catch (WP_CRM_Exception $wp_crm_exception) {
	$product = null;
	}

if (is_null ($product)) die ();

$price = $product->get ('price', array (
		'quantity'	=> 1,
		'date'		=> time ()
		));
$value = $price->get ('full price');
$currency = 'lei';
echo 'document.write(\'' . $value . ' ' . $currency . '\');';
?>
