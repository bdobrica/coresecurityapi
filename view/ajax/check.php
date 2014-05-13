<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

$file = file_get_contents (dirname (__FILE__) . '/e1st.csv');
$rows = explode ("\n", $file);

function fixcell ($cell) {
	return trim(str_replace ('-', ' ', trim($cell, '"')));
	}

foreach ($rows as $row) {
	$cells = explode (';', $row);
	$card = (int) fixcell ($cells[0]);
	if ($card == 0) continue;

	$person = $wpdb->get_row ('select * from wp_persons where card=' . $card);

	if ((strtoupper (fixcell($person->first_name)) != strtoupper (fixcell ($cells[2])))) {
		echo '@'.strtoupper(fixcell($person->first_name)) . "@\t@" . strtoupper(fixcell ($cells[2])) . "@\n";
		$wpdb->query ('update wp_persons set fix=1 where card=' . $card);
		}
	}

$file = file_get_contents (dirname (__FILE__) . '/e2nd.csv');
$rows = explode ("\n", $file);

foreach ($rows as $row) {
	$cells = explode (';', $row);
	$card = (int) fixcell ($cells[0]);
	if ($card == 0) continue;

	$person = $wpdb->get_row ('select * from wp_persons where card=' . $card);

	if ((strtoupper (fixcell($person->first_name)) != strtoupper (fixcell ($cells[2])))) {
		echo '@'.strtoupper(fixcell($person->first_name)) . "@\t@" . strtoupper(fixcell ($cells[2])) . "@\n";
		$wpdb->query ('update wp_persons set fix=2 where card=' . $card);
		}
	}
?>
