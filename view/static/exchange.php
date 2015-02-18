<?php
/*
App Title: Curs Valutar
App Parent: finance
App Order: 1
App Description:
App Size: 2
App Style:
App Icon: exchange
*/

$stamp = strtotime (date('Y-m-d'));
$list = new WP_CRM_List ('WP_CRM_Currency', array ('stamp=' . $stamp));
$c = 0;
while ($list->is ('empty') && ($c++ < 10)) {
	unset ($list);
	$stamp -= 86400;
	$list = new WP_CRM_List ('WP_CRM_Currency', array ('stamp=' . $stamp));
	}


$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'convert' => array (
					'label' => 'Convertor',
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
		)
	);
unset ($view);
?>
