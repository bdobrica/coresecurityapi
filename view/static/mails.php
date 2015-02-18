<?php
/*
App Title: Conturi Email
App Parent: erp
App Order: 1
App Description:
App Size: 1
App Style:
App Icon: envelope-o 
*/
$mails = new WP_CRM_List ('WP_CRM_Mail');
$view = new WP_CRM_View ($mails, array (
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
