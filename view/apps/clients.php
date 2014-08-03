<?php
global
	$current_user,
	$wpdb;
get_currentuserinfo();

$out = '';

$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . WP_CRM_Company::$T . '` where uid=%d;', $current_user->ID);
$companies = $wpdb->get_col ($sql);

$list = new WP_CRM_List ('WP_CRM_Product', (current_user_can ('manage_options') || empty ($companies)) ? null : array ('cid in (' . implode (',', $companies) . ')'));
if (!$list->is ('empty')) {
	$out .= '<div class="app-slide-wrapper">';
	$out .= '<div class="app-slide-container">';
	foreach ($list->get() as $product)
		$out .= '<a class="app-slide" href="' . $this->get('slug') . '/' . $product->get() . '"><span class="app-slide-title">' . $product->get('short name') . '</span><span class="app-slide-info"><span class="app-slide-info-highlight">' . $product->get('confirmed clients') . '</span>/' . $product->get('clients') . '</span></a>';
	$out .= '</div>';
	$out .= '<div class="app-slide-up ui-icon ui-icon-circle-arrow-n"></div>';
	$out .= '<div class="app-slide-down ui-icon ui-icon-circle-arrow-s"></div>';
	$out .= '</div>';
	}
?>
