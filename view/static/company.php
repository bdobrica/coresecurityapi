<?php
/*
App Title: Companie
App Description:
App Size: 1
App Style:
App Icon: gear 
*/
$list = new WP_CRM_List ('WP_CRM_Company', array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));
$view = new WP_CRM_View ($list, $list->get ('size') ? array (
			'item' => array (
				'edit' => 'Modifica'
				)
			):
		array (
			'toolbar' => array (
				'add' => 'Adauga',
				),
			'item' => array (
				'edit' => 'Modifica'
				)
			));
unset ($view);
?>
