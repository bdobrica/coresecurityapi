<?php
/*
App Title: Companii
App Description:
App Size: 1
App Style:
App Icon: gears 
*/
$list = new WP_CRM_List ('WP_CRM_Company');
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'edit' => 'Modifica'
		));
unset ($view);
?>
