<?php
$list = new WP_CRM_List ('WP_CRM_Invoice', array (
		'uid='.$current_user->ID,
		'flags=' . WP_CRM_Invoice::Real_Invoice,
		'abs(paidvalue-value)>' . WP_CRM_Invoice::Epsilon));
$view = new WP_CRM_View ($list, array (
	'add' => 'Factura Noua',
	'view' => 'Vezi',
	'contact' => 'Contact',
	'memo' => 'Memo',
	'pay' => 'Plateste',
	'people' => 'Participanti',
	'delete' => 'Sterge'
	));
unset ($view);
?>
