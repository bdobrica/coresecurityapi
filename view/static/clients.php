<?php
/*
App Title: Clienti
App Parent: crm
App Order: 1
App Description:
App Size: 2
App Style:
App Icon: users 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Client', array ('pid='.((int)$_GET['filter'])));
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
					'label' => 'Adauga',
					),
				'edit' => array (
					'label' => 'Modifica',
					),
				'view' => array (
					'label' => 'Vezi',
					),
				'invoice' => array (
					'label' => 'Factura',
					),
				'pay' => array (
					'label' => 'Plati',
					),
				)
			)
		));
unset ($view);
/*
		array (
			'type' => 'toolbar',
			'items' => array (
				'' => array (
					'label' => '',
					),
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'' => array (
					'label' => '',
					),
				)
			)
*/
?>
