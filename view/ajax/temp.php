<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

$list = new WP_CRM_List ('WP_CRM_Invoice', array (
	'uid=1'
	));

$invoices = $list->get ();

function input () {
	return trim(fgets(STDIN));
	}

function package ($code) {
	$code = strtoupper(trim(preg_replace ('/[0-9]+/', '', $code)));
	switch ($code) {
		case 'DGBSLV':
		case 'BGDSLV':
		case 'DGBPTS':
		case 'DGBEDS':
			return 'SILVER';
			break;
		case 'DGBGLD':
		case 'DGBPTG':
		case 'DGBEDG':
			return 'GOLD';
			break;
		case 'DGBVIP':
		case 'DGBPTV':
		case 'DGBEDV':
			return 'VIP';
			break;
		case 'DGBPLT':
		case 'DGBPTP':
			return 'PLATINUM';
			break;
		case 'DGBORG':
			return 'ORG';
			break;
		case 'DGBTMP':
			return 'TMP';
			break;
		}
	return '';
	}

function guess ($string) {
	if (strpos (strtoupper($string), 'PLATINUM') !== FALSE) return 'PLATINUM';
	if (strpos (strtoupper($string), 'VIP') !== FALSE) return 'VIP';
	if (strpos (strtoupper($string), 'GOLD') !== FALSE) return 'GOLD';
	if (strpos (strtoupper($string), 'SILVER') !== FALSE) return 'SILVER';
	return '';
	}

$out = array ();

foreach ($invoices as $invoice) {
	$clients = $invoice->get ('clients');
	$series = $invoice->get ('series');
	if (!empty ($clients)) {
	foreach ($clients as $code => $data) {
		$out[] = implode (',',array(
			$invoice->get ('series'),
			'"' . $invoice->buyer->get ('name') . '"',
			'"' . $invoice->get ('value') . '"'
			));
		if (!empty ($data['clients']))
		foreach ($data['clients'] as $key => $client) {
			$out[] = implode (',', array (
				'""',
				'""',
				'""',
				$client->get ('box'),
				$client->get ('card'),
				'"' . $client->get ('first_name') . '"',
				'"' . $client->get ('last_name') . '"',
				'"' . $client->get ('company') . '"'
				));
			}
		}
		/*
		$package = package ($code);
		if (!$package) $package = guess ($data['product']);

		echo "PRODUCT CODE: " . $code . "\n";
		echo "PRODUCT NAME: " . $data['product'] . "\n";
		echo "INVOICE: " . $series . "\n";
		if (!empty ($data['clients']))
		foreach ($data['clients'] as $key => $client) {
			echo $key . ". " . $client->get ('last_name') . ",\t" . $client->get ('first_name') . "\n";
			}

		echo "DETECTED: " . $package . " ?\n";
		$input = input ();
		$package = $input ? $input : $package;
		echo "SELECTED: " . $package . "\n";

		if (!empty ($data['clients']))
		foreach ($data['clients'] as $key => $client) {
			$client->set ('package', strtoupper($package));
			}
		*/
		}
	}

echo implode ("\n", $out);
?>
