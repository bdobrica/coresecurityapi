<?php
/*
App Title: Angajati
App Description:
App Size: 1
App Style:
App Icon: briefcase 
*/
$list = new WP_CRM_List ('WP_CRM_Employee');
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'edit' => 'Modifica',
		));
unset ($view);
?>
