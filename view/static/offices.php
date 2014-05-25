<?php
/*
App Title: Organizatii
App Description:
App Size: 1
App Style:
App Icon: suitcase 
*/
$list = new WP_CRM_List ('WP_CRM_Office', array ($wp_crm_office_query ? $wp_crm_office_query : 1));//, 'id in (select iid from `wp_basket` where pid=4)'));
$view = new WP_CRM_View ($list, array (
	'add' => 'Organizatie Noua',
	'view' => 'Vezi',
	'memo' => 'Memo',
	'delete' => 'Sterge'
	));
unset ($view);
?>
