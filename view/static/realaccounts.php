<?php
/*
App Title: Conturi Comerciale
App Parent: finance
App Order: 1
App Description:
App Size: 2
App Style:
App Icon: money
*/
$list = new WP_CRM_List ('WP_CRM_Account', array ('type=\'R\''));

$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Factura Noua',
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
				'edit' => array (
					'label' => 'Modifica',
					),
				'pay' => array (
					'label' => 'Plateste',
					),
				'people' => array (
					'label' => 'Persoane',
					),
				'contact' => array (
					'label' => 'Contact',
					),
				'memo' => array (
					'label' => 'Memo',
					),
				'delete' => array (
					'label' => 'Sterge',
					),
				)
			)
	));
unset ($view);
?>
