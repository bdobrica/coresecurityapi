<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$rows = $wpdb->get_results ('select * from `'.$wpdb->prefix.'clients` where iid=1336 order by rand();');
foreach ($rows as $row) {
	$person = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where uin=%ld;', $row->uin));
	$baskets = $wpdb->get_results ($wpdb->prepare ('select * from `'.$wpdb->prefix.'new_basket` where code=%s;', $row->series.str_pad($row->number, 3, '0', STR_PAD_LEFT)));

	echo 'PERSON:'.$person->first_name."\t".$person->last_name."\t".$person->name."\t".$person->email."\n";
	echo 'CURS:'.$row->series.' '.$row->number."\n";
	foreach ($baskets as $basket) {
		$count = $wpdb->get_var($wpdb->prepare ('select count(1) from `'.$wpdb->prefix.'clients` where iid=%d and series=%s and number=%d;', array (
			$basket->iid,
			$row->series,
			$row->number
			)));
		//if ($count == $basket->quantity) continue;
		$invoice = $wpdb->get_row($wpdb->prepare ('select * from `'.$wpdb->prefix.'new_invoices` where id=%d;', $basket->iid));
		if ($invoice->buyer == 'person') {
			$delegate = $wpdb->get_row($wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where id=%d;', $invoice->bid));
			$suf = ' (person: '.$delegate->name.' '.$delegate->first_name.' '.$delegate->last_name.' '.$delegate->email.')';
			}
		else {
			$company = $wpdb->get_row($wpdb->prepare ('select * from `'.$wpdb->prefix.'companies` where id=%d;', $invoice->bid));
			$suf = ' (company: '.$company->name.' '.$company->email.')';
			}

		echo 'PERSON:'.$person->uin."\tINVOICE:".$basket->iid.$suf."\n";
		echo 'SQL: update wp_clients set iid='.$basket->iid.' where id='.$row->id.";\n";
		}
	die ();
	}
?>
