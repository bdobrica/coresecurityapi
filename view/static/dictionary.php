<?php
/*
App Title: Dictionare
App Parent: elearning
App Requires: wp_crm_add_dictionary
App Permissions: system
App Description:
App Size: 1
App Style:
App Order: 4
App Icon: book
*/

list ($id, ) = explode (':', $_GET['filter'], 2);
if (is_numeric ($id)) {
	$dictionary = new WP_CRM_Dictionary ((int) $id);
	$view = new WP_CRM_View ($dictionary->get ('entries'), array (
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
	}
else {
	$list = new WP_CRM_List ('WP_CRM_Dictionary');
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
	}
?>
