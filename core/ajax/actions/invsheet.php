<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

if (!preg_match('/^[0-9,]+$/', urldecode($_GET['u']))) die ('ERROR');
$persons = explode(',', trim (urldecode($_GET['u']),','));
if (!is_array($persons)) die ('ERROR');
if (empty($persons)) die ('ERROR');

$product = new WP_CRM_Product (array (
		'series' => wp_crm_extract_series ($_GET['s']),
		'number' => wp_crm_extract_number ($_GET['s'])
		));

$time_begin = $product->get ('current begin');
$time_end = $product->get ('current end');
if (date('m', $time_begin) == date('m', $time_end))
        $date = date ('j - ', $time_begin) . date ('j F Y', $time_end);
else
        $date = date ('j F - ', $time_begin) . date ('j F Y', $time_end);
$date = wp_crm_date ($date);

$pdf = new PDF ('L');
$c = 1;

$cols = array (
	'h3;NR.' => 10,
	'h3;NUME SI PRENUME' => 70,
	'h3;TELEFON' => 40,
	'h3;EMAIL' => 70,
	'h3;FACTURA' => 40,
	'h3;OBS.' => 0
	);

foreach ($persons as $person) {
	if (($c%20) == 1) {
		if ($c > 1) $pdf->AddPage ();

		$pdf->style ('h1');
		$pdf->Cell (0, 7, $product->get('nice name'), 0, 1, 'C');
		$pdf->style ();

		$pdf->style ('h2');
		$pdf->Cell (0, 7, $date, 0, 1, 'C');
		$pdf->style ();

		$pdf->style ('h3');
		$pdf->Cell (0, 6, '- fisa de prezenta -', 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY();
		$pdf->SetY($y + 10);

		$pdf->Image(dirname(__FILE__).'/landscape.png', 0, 0, 297, 210);

		$rows = array();
		}

	$participant = new WP_CRM_Person ($person);
	$invoice = $product->get('invoice', $participant);
	if ($invoice)
		$invoice = new WP_CRM_Invoice ($invoice);

	$rows[] = array (
		$c++,
		$participant->get('name'),
		$participant->get('phone'),
		$participant->get('email'),
		$invoice ? ($invoice->get('invoice_series') . $invoice->get('invoice_number')) : ' - ',
		''
		);

	if (($c%20) == 1) {
		$pdf->table ($cols, $rows, 7);
		}
	}

if (($c%20) != 1) {
	$pdf->table ($cols, $rows, 7);
	$pdf->out ();
	}
else
	$pdf->out ();
?>
