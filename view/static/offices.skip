<?php
/*
App Title: Birouri
App Parent: erp
App Order: 1
App Description:
App Size: 1
App Style:
App Icon: suitcase 
*/
$list = new WP_CRM_List ('WP_CRM_Office', array ($wp_crm_office_query ? : 'uid=' . $current_user->ID));
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
			'add' => array (
				'label' => 'Birou nou'
				),
			'view' => array (
				'label' => 'Vezi'
				),
			'memo' => array (
				'label' => 'Memo'
				),
			'edit' => array (
				'label' => 'Editeaza'
				),
			'delete' => array (
				'label' => 'Sterge'
				),
			'companii' => array (
				'label' => 'Companii'
				),
			'produse' => array (
				'label' => 'Produse'
				)
			)
		)
	));
unset ($view);
?>
