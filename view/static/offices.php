<?php
/*
App Title: Birouri
App Description:
App Size: 1
App Style:
App Icon: suitcase 
*/
//$list = new WP_CRM_List ('WP_CRM_Office', array ($wp_crm_office_query ? $wp_crm_office_query : 'uid='.$current_user->ID));
$list = new WP_CRM_List ('WP_CRM_Office', array ('uid='.$current_user->ID));//, 'id in (select iid from `wp_basket` where pid=4)'));
$view = new WP_CRM_View ($list, array (
	'add' => 'Birou nou',
	'view' => 'Vezi',
	'memo' => 'Memo',
	'delete' => 'Sterge'
	));
unset ($view);
?>
