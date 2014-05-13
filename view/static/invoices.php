<?php
/*
App Title: Facturi
App Description:
App Size: 2
App Style: 
*/
$list = new WP_CRM_List ('WP_CRM_Invoice', current_user_can ('add_users') ? null : array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));
$actions = array (
	'add' => 'Factura Noua',
	'view' => 'Vezi',
	'edit' => 'Modifica',
	'pay' => 'Plateste',
	'people' => 'Participanti',
	'contact' => 'Contact',
	'memo' => 'Memo',
	'delete' => 'Sterge'
	);

if (!current_user_can ('wp_crm_pay') && !current_user_can ('add_users')) unset ($actions['pay']);

$view = new WP_CRM_View ($list, $actions);
unset ($view);
?>
