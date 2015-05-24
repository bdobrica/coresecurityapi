<?php
/*
App Title: Coupons
App Parent: ecommerce
App Requires: wp_crm_admin
App Order: 5 
App Description:
App Size: 1
App Style:
App Icon: money
*/
$list = new WP_CRM_List ('WP_CRM_Coupon');
ini_set ('display_errors', 1);

$affiliates = array (array ('code' => 'Cod', 'url' => 'URL'));
if (!$list->is ('empty'))
foreach ($list->get() as $coupon) {
	$aliases = $coupon->get ('alias');
	$aliases = $aliases ? explode (',', $aliases) : array ();
	$code = $coupon->get ('code');
	$affiliates[] = array (
		'code' => $coupon->get ('code'),
		'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/affiliate/?class=' . $code . '&secret=' . $coupon->encode ($code)
		);

	if (!empty ($aliases))
		foreach ($aliases as $alias) {
			$affiliates[] = array (
				'code' => $alias,
				'url' => 'http://' . $_SERVER['HTTP_HOST'] . '/affiliate/?class=' . $code . '&secret=' . $coupon->encode ($alias)
				);
			}
	}

$view = new WP_CRM_View ($affiliates, array ());
unset ($view);
?>
