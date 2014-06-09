<?php
/*
App Title: Utilizatori
App Description:
App Size: 1
App Style:
App Icon: users 
*/
$list = new WP_CRM_List ('WP_CRM_User');
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'edit' => 'Modifica',
		'delete' => 'Sterge'
		));
unset ($view);
?>
