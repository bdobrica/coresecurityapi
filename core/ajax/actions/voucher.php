<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

$voucher = dirname(dirname(dirname(__FILE__))).'/cache/template/voucher.jpg';

$vids = explode(',', rtrim($_GET['v'],','));
if (empty($vids)) die ('Selecteaza participanti!');

$products = array ();
$participants = array();
foreach ($vids as $vid) {
	$client = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'clients` where id=%d;', wp_crm_extract_number(trim($vid))));
	$products[] = $client->series.str_pad($client->number, 5, 0, STR_PAD_LEFT);
	$participants[] = new WP_CRM_Person ($client->uin);
	}

$pdf = new PDF ();

$count = 0;

foreach ($participants as $participant) {
	if ($count) $pdf->AddPage();
	for ($count = 0; $count < 3; $count ++) {
		$pdf->image ($voucher, 0, ($count%3)*99, 210, 99);

		$pdf->SetXY (50,($count%3)*99 + 25);
		$pdf->cell (0, 5, $participant->get('voucher', $products[$count]).'-'.$count);
		$pdf->SetXY (80,($count%3)*99 + 45);
		$pdf->cell (0, 5, $participant->get('name'));
		}
	}

$pdf->out ();
?>
