<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$temp = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'companies` where uin=%s;', 'RO155256'), ARRAY_A);
print_r($temp);
?>
