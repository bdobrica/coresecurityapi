<?php
/*
App Title: Istoric
App Parent: crm
App Order: 1
App Description:
App Size: 1
App Style:
App Icon: check-square-o 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Task', array ('uid='.$current_user->ID));
$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Adauga',
					),
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'view' => array (
					'label' => 'Vezi',
					),
				'memo' => array (
					'label' => 'Memo',
					),
				)
			)
	));
unset ($view);
?>
