<?php
/*
App Title: Orders
App Parent: ecommerce
App Requires: wp_crm_work
App Order: 4
App Description:
App Size: 1
App Style:
App Icon: files-o 
*/
$role = $wp_crm_user->get ('role');
if (in_array ($role, array ('wp_crm_subscriber'))) {
$list = new WP_CRM_List ('WP_CRM_Invoice', array (sprintf('bid=%d', $wp_crm_user->get('person')->get()), 'buyer=\'person\''));

$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'column',
			'label' => 'Actiuni',
			'items' => array (
				'view' => array (
					'label' => 'Vezi',
					),
				'pay' => array (
					'label' => 'Plateste',
					),
				'people' => array (
					'label' => 'Persoane',
					),
				)
			)
	));
unset ($view);
	}
else {
$list = new WP_CRM_List ('WP_CRM_Invoice', current_user_can ('add_users') ? null : array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));
$actions = array (
	'add' => 'Factura Noua',
	'view' => 'Vezi',
	'edit' => 'Modifica',
	'pay' => 'Plateste',
	'people' => 'Participanti',
	'contact' => 'Contact',
	'memo' => 'Memo',
	'delete' => 'Sterge'
	);

if (!current_user_can ('wp_crm_pay') && !current_user_can ('add_users')) unset ($actions['pay']);

$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Factura Noua',
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
				'pay' => array (
					'label' => 'Plateste',
					),
				'people' => array (
					'label' => 'Persoane',
					),
				'contact' => array (
					'label' => 'Contact',
					),
				'memo' => array (
					'label' => 'Memo',
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
