<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$pin = trim(preg_replace('/[^a-z0-9]+/',' ',strtolower($_GET['p'])));

$product_series = preg_replace('/[0-9]+/', '', $pin);
$product_number = intval(preg_replace('/[^0-9]+/', '', $pin));

$product = $wpdb->get_var ($wpdb->prepare("select pid from `wp_products` where series=%s and number=%d;", $product_series, $product_number));
if (!$product) die(json_encode(array()));

$product = array (
	'code' => $pin,
	'name' => get_the_title($product),
	'price' => get_post_meta($product, 'shop_price'),
	'vat' => get_post_meta($product, 'shop_vat')
	);

echo json_encode($product);
?>
