<?php
/*
App Title: SecureBox
App Parent: dms
App Order: 1
App Description:
App Size: 1
App Style:
App Icon: folder
*/
$folder = new WP_CRM_Folder (1);
$view = new WP_CRM_View ($folder, array (
	'toolbar' => array (
		'add' => 'Creaza',
		'upload' => 'Incarca'
		),
	'item' => array (
		),
	));
unset ($view);
?>
