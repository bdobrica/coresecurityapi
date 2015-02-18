<?php
/*
App Title: Sarcini
App Parent: erp
App Order: 1
App Description:
App Size: 1
App Style:
App Icon: tasks 
*/
$list = new WP_CRM_List ('WP_CRM_Task', array ('uid='.$current_user->ID));//, 'id in (select iid from `wp_basket` where pid=4)'));
$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Sarcina Noua',
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
				'memo' => array (
					'label' => 'Memo',
					),
				'do' => array (
					'label' => 'Fa-o!',
					),
				'delete' => array (
					'label' => 'Sterge',
					),
				)
			)
	));
unset ($view);
?>
