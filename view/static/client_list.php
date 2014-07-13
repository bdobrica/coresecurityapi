<?php
/*
App Title: Client List
App Description:
App Size: 1
App Style:
App Icon: check-square-o 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Client', array ('uid='.$current_user->ID));
$view = new WP_CRM_View ($list, array (
	'add' => 'Client nou',
	'view' => 'Vezi date client',
	'contact' => 'Modifica',
	'delete' => 'Sterge',
	'memo' => 'Incasari',
	'memo' => 'Restante',
	'memo' => 'Istoric(tickete)',			
	'memo' => 'Istoric(facturi)',			
	'memo' => 'Istoric(produse)',			
	'memo' => 'Mail',
	'memo' => 'Memo'			
					
	));
unset ($view);
?>
