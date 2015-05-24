<?php
/*
App Title: Clienti
App Description:
App Size: 1
App Order: 2
App Style:
App Icon: gear 
*/
$list = new WP_CRM_List ('WP_CRM_Company');//, array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));
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
				'add' => array (
					'label' => 'Adauga',
					),
				'info' => array (
					'label' => 'Informatii',
					'items' => array (
						'financial' => array (
							'label' => 'Situatie Financiara',
							),
						'invoices' => array (
							'label' => 'Facturi',
							),
						'products' => array (
							'label' => 'Produse / Servicii',
							),
						'documents' => array (
							'label' => 'Documente',
							),
						'history' => array (
							'label' => 'Istoric Client',
							),
						)
					),
				'delete' => array (
					'label' => 'Sterge',
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
				'financial' => array (
					'label' => 'Situatie Financiara',
					),
				'invoices' => array (
					'label' => 'Facturi',
					),
				'products' => array (
					'label' => 'Produse / Servicii',
					),
				'documents' => array (
					'label' => 'Documente'
					),
				'history' => array (
					'label' => 'Istoric Client',
					),
				'memo' => array (
					'label' => 'Memo',
					),
				)
			)
		));
unset ($view);
?>
