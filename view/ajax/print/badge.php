<?php
define ('WP_USE_THEMES', false);
define ('WP_CRM_BADGE_FILE', dirname(__FILE__).'/badge.png');
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');


$series = wp_crm_extract_series ($_GET['s']);
$badge_file = (file_exists (dirname(__FILE__) . '/badge-' . $series . '.png')) ? dirname(__FILE__) . '/badge-' . $series . '.png' : WP_CRM_BADGE_FILE;

if (!preg_match('/^[0-9,]+$/', urldecode($_GET['b']))) die ('ERROR');
$badges = explode(',', trim (urldecode($_GET['b']),','));
if (!is_array($badges)) die ('ERROR');
if (empty($badges)) die ('ERROR');

$c = 0;

$pdf = new PDF ('L');
$pdf->AddFont ('bookman-old-style');
$pdf->Cell (0, 5, '', 0, 1);

$pdf->style ('badge');

$x = $pdf->GetX();
$y = $pdf->GetY();


while ($c < count($badges)) {
	# row
	$pdf->Image($badge_file, $x		, $y, 89, 54);
	$pdf->Image($badge_file, $x + 91	, $y, 89, 54);
	$pdf->Image($badge_file, $x + 182	, $y, 89, 54);

	$h = 17;
	$pdf->Cell (89, $h, '', 'TLR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, '', 'TLR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, '', 'TLR', 0, 'C');
	$pdf->Cell (0, $h, '', 0, 1);

	$h = 10;
	$pa = null; $pb = null; $pc = null;
	if ($badges[$c  ]) $pa = new WP_CRM_Person($badges[$c  ]);
	if ($badges[$c+1]) $pb = new WP_CRM_Person($badges[$c+1]);
	if ($badges[$c+2]) $pc = new WP_CRM_Person($badges[$c+2]);

	if ($badge_file != WP_CRM_BADGE_FILE)
		$pdf->style ('badge:special');
	else
		$pdf->style ('badge');

	$pdf->Cell (89, $h, is_object($pa) ? $pa->get('first_name') : '', 'LR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, is_object($pb) ? $pb->get('first_name') : '', 'LR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, is_object($pc) ? $pc->get('first_name') : '', 'LR', 0, 'C');
	$pdf->Cell (0, $h, '', 0, 1);

	if ($badge_file != WP_CRM_BADGE_FILE)
		$pdf->style ('badge:special-small');
	else
		$pdf->style ('badge:small');

	$pdf->Cell (89, $h, is_object($pa) ? $pa->get('last_name') : '', 'LR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, is_object($pb) ? $pb->get('last_name') : '', 'LR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, is_object($pc) ? $pc->get('last_name') : '', 'LR', 0, 'C');
	$pdf->Cell (0, $h, '', 0, 1);

	$h = 17;
	$pdf->Cell (89, $h, '', 'BLR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, '', 'BLR', 0, 'C');
	$pdf->Cell (2, $h, '');
	$pdf->Cell (89, $h, '', 'BLR', 0, 'C');
	$pdf->Cell (0, $h, '', 0, 1);
	
	$c+=3;
	if ($c%9 == 0) {
		if ($c > count($badges)) {
			$pdf->AddPage();
			}
		}
	$pdf->Cell (0, 5, '', 0, 1);
	$y = $pdf->GetY();
	}

$pdf->out ();
?>
