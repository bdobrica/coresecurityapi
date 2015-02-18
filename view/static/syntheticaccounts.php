<?php
/*
App Title: Conturi Sintetice
App Parent: finance
App Order: 1
App Description:
App Size: 2
App Style:
App Icon: money
*/
$list = new WP_CRM_List ('WP_CRM_Account', array ('type=\'S\''));

$view = new WP_CRM_View ($list, array (
		array (
			'type' => 'toolbar',
			'items' => array (
				'add' => array (
					'label' => 'Inregistrare Noua',
					),
				'close' => array (
					'label' => 'Inchidere luna',
					),
				'utility' => array (
					'label' => 'Utilitare',
					'items' => array (
						'd100' => array (
							'label' => 'Declaratia 100',
							),
						'd300' => array (
							'label' => 'Declaratia 300',
							),
						'd394' => array (
							'label' => 'Declaratia 394',
							),
						'balance' => array (
							'label' => 'Bilant',
							),
						)
					),
				'reports' => array (
					'label' => 'Rapoarte',
					'items' => array (
						'rjournal' => array (
							'label' => 'Registru Jurnal',
							),
						'rbigbook' => array (
							'label' => 'Carte mare debit/credit',
							),
						'raccountfile' => array (
							'label' => 'Fisa de cont',
							),
						'rcash' => array (
							'label' => 'Registru de casa',
							),
						'rbank' => array (
							'label' => 'Registru de banca',
							),
						'rsales' => array (
							'label' => 'Jurnal de vanzari',
							),
						'rbuy' => array (
							'label' => 'Registru de cumparari',
							),
						'rlease' => array (
							'label' => 'Leasing',
							),
						)
					)
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
?>
