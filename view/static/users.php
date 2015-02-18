<?php
/*
App Title: Utilizatori
App Parent: system
App Order: 1
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
				'task' => array (
					'label' => 'Stabileste Task',
					),
				'viewtasks' => array (
					'label' => 'Task-uri Curente',
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
				'task' => array (
					'label' => 'Stabileste Task',
					),
				'viewtasks' => array (
					'label' => 'Task-uri Curente',
					),
				'delete' => array (
					'label' => 'Sterge',
					),
				)
			)
		));
unset ($view);
?>
