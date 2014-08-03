<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$lin = intval(preg_replace('/[^0-9-]+/','',strtolower($_GET['l'])));

$location = $wpdb->get_row($wpdb->prepare('select * from `'.$wpdb->prefix.'product_locations` where id=%d;', $lin), ARRAY_A);
if (!empty($location))
	echo json_encode($location);
else
	json_encode(array());
?>
