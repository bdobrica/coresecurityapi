<?php
/*
App Title: Comenzi
App Description:
App Size: 1
App Style:
App Icon: check-square-o 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Order', array ('uid='.$current_user->ID));//, 'id in (select iid from `wp_basket` where pid=4)'));
$view = new WP_CRM_View ($list, array (
	'add' => 'Cerere Plata',
	'view' => 'Vezi',
	'contact' => 'Contact',
	'memo' => 'Memo',
	'pay' => 'Plateste',
	'delete' => 'Sterge'
	));
unset ($view);
?>
