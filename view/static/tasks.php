<?php
/*
App Title: Sarcini
App Description:
App Size: 1
App Style:
App Icon: tasks 
*/
$list = new WP_CRM_List ('WP_CRM_Task', array ('uid='.$current_user->ID));//, 'id in (select iid from `wp_basket` where pid=4)'));
$view = new WP_CRM_View ($list, array (
	'add' => 'Sarcina Noua',
	'view' => 'Vezi',
	'memo' => 'Memo',
	'do' => 'Fa-o!',
	'delete' => 'Sterge'
	));
unset ($view);
?>
