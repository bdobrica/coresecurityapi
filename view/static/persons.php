<?php
/*
App Title: Persoane
App Description:
App Size: 1
App Style: 
App Icon: male
*/
$list = new WP_CRM_List ('WP_CRM_Person', array ('first_name<>\'\' or last_name<>\'\' or email<>\'\''));
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'view' => 'Vezi',
		'edit' => 'Modifica',
		'delete' => 'Sterge'
		));
unset ($view);
?>
