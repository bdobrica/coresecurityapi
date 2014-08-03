<?php
/*
App Title: Clienti
App Description:
App Size: 2
App Style:
App Icon: users 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Client', array ('pid='.((int)$_GET['filter'])));
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'edit' => 'Modifica',
		'view' => 'Vezi',
		'invoice' => 'Factura',
		'pay' => 'Plati',
		));
unset ($view);
?>
