<?php
/*
App Title: Rapoarte
App Parent: bi
App Order: 1
App Description:
App Size: 2
App Style:
App Icon: bar-chart-o 
*/

$list = new WP_CRM_List ('WP_CRM_Report', array ('uid='.$current_user->ID));

$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'select' => array (
					'label' => 'Selecteaza',
					'items' => array (
						'selall' => array (
							'label' => 'Tot'
							),
						'seldel' => array (
							'label' => 'Nimic'
							)
						)
					),
				'update' => array (
					'label' => 'Actualizeaza',
					),
				'add' => array (
					'label' => 'Raport Nou',
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
