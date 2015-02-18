<?php
/*
App Title: Permisiuni
App Parent: system
App Order: 4
App Description:
App Size: 1
App Style:
App Icon: gears
*/
$list = new WP_CRM_List ('WP_CRM_ACL');
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
