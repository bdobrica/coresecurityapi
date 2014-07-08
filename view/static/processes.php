<?php
/*
App Title: Procese
App Description:
App Size: 1
App Style:
App Icon: sitemap 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Process', array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));
$view = new WP_CRM_View ($list, array (
	'toolbar' => array (
		'add' => 'Adauga',
		),
	'item' => array (
		'edit' => 'Modifica',
		'memo' => 'Memo',
		'delete' => 'Sterge'
		)
	));
unset ($view);
?>
