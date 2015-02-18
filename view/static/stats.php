<?php
/*
App Title: Statistici
App Parent: bi
App Order: 8
App Description:
App Size: 2
App Style:
App Icon: bar-chart-o 
*/
/*
$list = new WP_CRM_List ('WP_CRM_Invoice', array ('uid='.$current_user->ID));

$product_chart = new WP_CRM_Chart (array ('Produse', 'Inscrieri', 'Confirmari'));

foreach (($list->get()) as $invoice) {
	$products = $invoice->get ('products');
	if (!empty($products))
		foreach ($products as $key => $value) {
			$product_chart->push (array (
				$key,
				(int) $value,
				$invoice->is ('real') ? (int) $value : 0
				), 'add');
			}
	}
$product_chart->set ('id', 'wp-crm-view-product-chart');
$product_chart->set ('title', 'Situatie Cursuri');
$product_chart->set ('size', array ('x' => 600, 'y' => 400));
$view = new WP_CRM_View ($product_chart);
unset ($view);
*/
?>
