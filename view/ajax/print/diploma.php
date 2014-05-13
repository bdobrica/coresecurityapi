<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

if (!preg_match('/^[0-9,]+$/', urldecode($_GET['d']))) die ('ERROR');
$diplomas = explode(',', trim (urldecode($_GET['d']),','));
if (!is_array($diplomas)) die ('ERROR');
if (empty($diplomas)) die ('ERROR');

$product = new WP_CRM_Product (array (
	'series' => wp_crm_extract_series ($_GET['s']),
	'number' => wp_crm_extract_number ($_GET['s']),
	));

$time_begin = $product->get ('current begin');
$time_end = $product->get ('current end');

$trainer = new WP_CRM_Trainer ($product->get('trainer', $_GET['s']));
$training = $product->get('short name', $_GET['s']);
$location = new WP_CRM_Location ($product->get('location', $_GET['s']));

if (date('d-m', $time_begin) == date('d-m', $time_end))
	$date = date ('j F Y', $time_end);
else
if (date('m', $time_begin) == date('m', $time_end))
	$date = date ('j - ', $time_begin) . date ('j F Y', $time_end);
else
	$date = date ('j F - ', $time_begin) . date ('j F Y', $time_end);
$date = wp_crm_date ($date);

$c = 0;

$pdf = new PDF ('L');
$pdf->AddFont ('copperplate-light');
$pdf->AddFont ('broadway');

while ($c < count($diplomas)) {
	$person = new WP_CRM_Person ($diplomas[$c]);

	$pdf->style ('badge');

	$x = $pdf->GetX();
	$y = $pdf->GetY();

	$pdf->Image(dirname(__FILE__).'/diploma.png', 0, 0, 297, 210);


	$y = 50;
	$h = 10;

	$pdf->setY (50);
	$pdf->style ('diploma:h1');
	$pdf->cell (0, $h, 'CERTIFICAT', 0, 1, 'C');
	$pdf->setY (65);
	$pdf->style ('diploma:h2');
	$pdf->cell (0, $h, 'DE PARTICIPARE', 0, 1, 'C');
	$pdf->style ();
	$pdf->setY (85);
	$pdf->cell (0, $h, 'Prin prezentul se adevereste ca', 0, 1, 'C');
	$pdf->style ('diploma:em');
	$pdf->setY (95);
	$pdf->cell (0, $h, $pdf->fix($_GET['empty'] ? '' : $person->get('name')), 0, 1, 'C');
	$pdf->style ();
	$pdf->setY (120);
	$pdf->cell (0, $h, 'a participat la cursul', 0, 1, 'C');
	#$pdf->cell (0, $h, 'a participat la', 0, 1, 'C');
	$pdf->style ('diploma:em');
	$pdf->setY (130);
	$pdf->cell (0, $h, '"'.$training.'"', 0, 1, 'C');

	$pdf->style ('diploma:h3');
	$pdf->setY (155);
	$pdf->cell (148, $h, 'Trainer', 0, 0, 'C');
	#$pdf->cell (148, $h, 'Speakeri', 0, 0, 'C');
	$pdf->cell (0, $h, $location->get('city'), 0, 1, 'C');

	$pdf->style ('diploma:h4');
	$pdf->setY (165);
	$pdf->cell (148, $h, $trainer->get('name'), 0, 0, 'C');
#	$pdf->cell (148, $h, 'Mircea Chira', 0, 0, 'C');
#	$pdf->setY (171);
#	$pdf->cell (148, $h, 'Eliza Bercu', 0, 0, 'C');
#	$pdf->setY (177);
	#$pdf->cell (148, $h, 'Bruno Medicina', 0, 0, 'C');
	$pdf->cell (0, $h, $date, 0, 1, 'C');
	$pdf->style ();
	$c++;
	if ($c < count($diplomas)) $pdf->AddPage();
	}

$pdf->out ();
?>
