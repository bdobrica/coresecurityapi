<?php
/*
App Title: Produsele mele
App Description:
App Size: 1
App Style:
App Icon: square 
*/
$list = new WP_CRM_List ('WP_CRM_Product', array ('uid='.$current_user->ID));
$view = new WP_CRM_View ($list, array (
		'add' => 'Adauga',
		'price' => 'Preturi',
		'edit' => 'Modifica',
		'delete' => 'Sterge'
		));
unset ($view);
?>
