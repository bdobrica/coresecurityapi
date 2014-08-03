<?php
/*
App Title: Site-uri
App Description:
App Size: 1
App Style:
App Icon: code-fork 
*/
$list = new WP_CRM_List ('WP_CRM_Site');
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'view' => 'Vezi',
		'edit' => 'Modifica',
		'delete' => 'Sterge'
		));
unset ($view);
?>
