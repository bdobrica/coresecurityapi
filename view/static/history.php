<?php
/*
App Title: History
App Description:
App Size: 1
App Style:
App Icon: check-square-o 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Task', array ('uid='.$current_user->ID));
$view = new WP_CRM_View ($list, array (
	'add' => 'Adauga tichet',
	'view' => 'Vezi',
	'memo' => 'Memo',
	));
unset ($view);
?>
