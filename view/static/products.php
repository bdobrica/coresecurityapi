<?php
/*
App Title: Produse
App Description:
App Size: 1
App Style:
App Icon: square 
*/
$list = new WP_CRM_List ('WP_CRM_Product');
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'price' => 'Preturi',
		'edit' => 'Modifica',
		'order' => 'Comanda',
		'delete' => 'Sterge'
		));
unset ($view);
?>
