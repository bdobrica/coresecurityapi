<?php
/*
App Title: Persoane
App Parent: crm
App Order: 1
App Description:
App Size: 1
App Style: 
App Icon: male
*/
$list = new WP_CRM_List ('WP_CRM_Person');
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
						),
					),
				'contact' => array (
					'label' => 'Contacteaza',
					),
				'add' => array (
					'label' => 'Adauga',
					),
				'import' => array (
					'label' => 'Importa',
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
				'delete' => array (
					'label' => 'Sterge',
					),
				)
			)
		));
unset ($view);
?>
