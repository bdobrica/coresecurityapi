<?php
/*
App Title: Evaluation Forms
App Parent: elearning
App Requires: wp_crm_add_evaluation_form
App Description:
App Size: 1
App Style:
App Order: 1
App Icon: edit
*/
$list = new WP_CRM_List ('WP_CRM_Quiz', array ('uid='.$current_user->ID));
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
				'requirements' => array (
					'label' => 'Checklist',
					),
				'process' => array (
					'label' => 'Proces',
					),
				'delete' => array (
					'label' => 'Sterge',
					),
				)
			)
		));
unset ($view);
?>
