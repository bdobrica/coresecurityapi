<?php
/*
App Title: Comenzi
App Parent: crm
App Order: 1
App Description:
App Size: 1
App Style:
App Icon: check-square-o 
*/
ini_set ('display_errors', 1);
$list = new WP_CRM_List ('WP_CRM_Order', array ('uid='.$current_user->ID));//, 'id in (select iid from `wp_basket` where pid=4)'));
$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
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
					),
				'contact' => array (
					'label' => 'Contact',
					),
				'memo' => array (
					'label' => 'Memo',
					),
				'pay' => array (
					'label' => 'Plateste',
					),
				'delete' => array (
					'label' => 'Sterge',
					),
				)
			)
	));
unset ($view);
?>
