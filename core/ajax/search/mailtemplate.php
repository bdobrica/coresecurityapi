<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$tin = intval(preg_replace('/[^0-9-]+/','',strtolower($_GET['t'])));

$mailtemplate = $wpdb->get_row($wpdb->prepare('select * from `'.$wpdb->prefix.'mailtemplate` where id=%d;', $tin), ARRAY_A);
if (!empty($mailtemplate))
	echo json_encode($mailtemplate);
else
	json_encode(array());
?>
