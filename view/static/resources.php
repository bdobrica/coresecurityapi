<?php
/*
App Title: Resurse
App Description:
App Size: 1
App Style:
App Icon: suitcase
*/
$list = new WP_CRM_List ('WP_CRM_Resource');
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'purchase' => 'Cumpara',
		'edit' => 'Modifica',
		'delete' => 'Sterge'
		));
unset ($view);
?>
