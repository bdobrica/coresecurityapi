<?php
/*
App Title: Modele
App Description:
App Size: 1
App Style:
App Icon: edit 
*/
$templates = new WP_CRM_List ('WP_CRM_Template');
$view = new WP_CRM_View ($templates, array (
		'add' => 'Adauga',
		'view' => 'Vezi',
		'edit' => 'Modifica',
		'delete' => 'Sterge'
		));
unset ($view);
?>
