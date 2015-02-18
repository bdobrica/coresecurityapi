<?php
/*
App Title: Camere
App Description:
App Size: 1
App Style:
App Icon: building-o 
*/
$list = new WP_CRM_List ('WP_CRM_Room', current_user_can ('add_users') ? null : array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));
$actions = array (
	'add' => 'Camera Noua',
	'view' => 'Vezi'
	);

$view = new WP_CRM_View ($list, $actions);
unset ($view);
?>
