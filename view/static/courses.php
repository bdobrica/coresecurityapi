<?php
/*
App Title: Courses
App Parent: elearning
App Requires: wp_crm_add_course
App Description:
App Size: 1
App Style:
App Order: 6
App Icon: bookmark
*/
$role = $wp_crm_user->get ('role');
if (in_array ($role, array ('wp_crm_subscriber'))) {
$list = new WP_CRM_List ('WP_CRM_Course');
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
				'view' => array (
					'label' => 'Vezi',
					'type' => 'link',
					'url' => '/course/%d'
					),
				'order' => array (
					'label' => 'Inscrie-te',
					),
				)
			)
		));
unset ($view);
	}
else {
$list = new WP_CRM_List ('WP_CRM_Course');
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
				'view' => array (
					'label' => 'Vezi',
					'type' => 'link',
					'url' => '/course/%d'
					),
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
}
?>
