<?php
/*
App Title: Resources
App Parent: elearning
App Requires: wp_crm_add_resource
App Order: 7
App Description:
App Size: 1
App Style:
App Icon: files-o
*/
$list = new WP_CRM_List ('WP_CRM_Support');
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
				)
			),
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
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
