<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-blog-header.php');
include (dirname(dirname(__FILE__)) . '/common.php');

$current_user = wp_get_current_user ();
if ($current_user->ID) {
	$wp_crm_offices = get_user_meta ($current_user->ID, '_wp_crm_offices', TRUE);
	$wp_crm_office_query = is_numeric ($wp_crm_offices) ? sprintf ('oid=%d', $wp_crm_offices) : !empty($wp_crm_offices) ? sprintf ('oid in (%s)', implode (',', $wp_crm_offices)) : '';
	}

$list = new WP_CRM_List ('WP_CRM_Company', current_user_can ('add_users') ? null : array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));

echo $list->get ('json');
?>
