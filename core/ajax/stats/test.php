<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$company = new WP_CRM_Company (1);

echo function_exists('wp_crm_crediteurope_payment') ? 'TRUE' : 'FALSE';
echo $company->get('crediteurope payment') ? 'TRUE' : 'FALSE';

/*
$rows = $wpdb->get_results ('select * from `'.$wpdb->prefix.'new_basket` order by id;');
foreach ($rows as $row) {
	$series = wp_crm_extract_series($row->code);
	$number = (int) wp_crm_extract_number($row->code);
	$stamp = (int) $row->stamp;

	echo "INVOICE:".$row->iid;

	$clients = $wpdb->get_results ($wpdb->prepare ('select * from `'.$wpdb->prefix.'clients` where series=%s and number=%d and stamp=%d;', array (
		$series,
		$number,
		$stamp
		)));

	if (!empty($clients)) {
		echo "\tCLIENTS:";
		foreach ($clients as $client) {
			$wpdb->query ($wpdb->prepare('update `'.$wpdb->prefix.'clients` set iid=%d where id=%d;', array (
				$row->iid,
				$client->id,
				)));
			echo "\t".$client->uin;
			}
		}
	echo "\n";
	}
*/
?>
