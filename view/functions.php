<?php
ini_set ('display_errors', true);
global
	$wp_crm_ui,
	$wp_crm_buyer,
	$wp_crm_state;

#$wp_crm_ui = new WP_CRM_UI ();
$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();

add_filter('show_admin_bar', '__return_false');

add_action ('wp_head', array ('WP_CRM_Theme', 'head'));
add_action ('wp_enqueue_scripts', array ('WP_CRM_Theme', 'init'));
?>
