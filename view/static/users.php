<?php
/*
App Title: Users
App Parent: system
App Order: 2
App Description:
App Size: 1
App Style:
App Icon: users 
*/
$list = new WP_CRM_List ('WP_CRM_User');
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
				'orders' => array (
					'label' => 'Status Comenzi',
					),
				'assignment' => array (
					'label' => 'Stabileste Tema',
					),
				'viewassigments' => array (
					'label' => 'Vizualizeaza Teme',
					),
				'contact' => array (
					'label' => 'Contacteaza',
					),
				'add' => array (
					'label' => 'Adauga',
					),
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'edit' => array (
					'label' => 'Modifica',
					),
				'assignment' => array (
					'label' => 'Stabileste Tema',
					),
				'viewassignment' => array (
					'label' => 'Vizualizeaza Teme',
					),
				'orders' => array (
					'label' => 'Status Comenzi',
					),
				'contact' => array (
					'label' => 'Contacteaza',
					),
				'delete' => array (
					'label' => 'Sterge',
					),
				)
			)
		));
unset ($view);
?>
