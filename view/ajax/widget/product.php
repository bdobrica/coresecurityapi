<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-blog-header.php');
include (dirname(dirname(__FILE__)) . '/common.php');

$current_user = wp_get_current_user ();

$list = new WP_CRM_List ('WP_CRM_Product', /* current_user_can ('edit_users') ? null : */ array (
	'uid=' . $current_user->ID
	));
echo $list->get ('json');
?>
