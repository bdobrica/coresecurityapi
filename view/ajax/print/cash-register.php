<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

$pdf = new PDF ('L');
$h = 5;

/* header */
$pdf->style('strong');
$x = $pdf->GetX();
$y = $pdf->GetY()+$h;
$pdf->Cell (89, $h, 'UNITATEA: EXTREME TRAINING', 1);
$pdf->style ('h1');
$pdf->Cell (89, 4*$h, 'REGISTRU DE CASA', 1, 0, 'C');
$pdf->style ('strong');
$pdf->Cell (30, $h, 'DATA', 1, 0, 'C');
$pdf->Cell (40, 2*$h, 'CONT CASA', 1, 0, 'C');
$pdf->Cell ( 0, 2*$h, 'NR. POZ', 1, 0, 'C');

$pdf->SetY ($y);
$pdf->Cell (30, $h, 'OP.PAD.', 1, 0, 'C');
$pdf->Cell (59, $h, '', 1, 0, 'C');
$pdf->SetX ($pdf->GetX()+89);
$pdf->Cell ( 7, $h, 'ZI', 1, 0, 'C');
$pdf->Cell (13, $h, 'LUNA', 1, 0, 'C');
$pdf->Cell (10, $h, 'AN', 1, 0, 'C');
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->SetY ($y);
$pdf->Cell (30, $h, '1', 1, 0, 'C');
$pdf->Cell (30, $h, '', 1);
$pdf->Cell (29, $h, '2', 1, 0, 'C');
$pdf->SetX ($pdf->GetX()+89);
$pdf->Cell ( 7, $h, '3', 1, 0, 'C');
$pdf->Cell (13, $h, '4', 1, 0, 'C');
$pdf->Cell (10, $h, '5', 1, 0, 'C');
$pdf->Cell (40, $h, '6', 1, 0, 'C');
$pdf->Cell ( 0, $h, '7', 1, 0, 'C');
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->SetY ($y);
$pdf->Cell (30, $h, '', 1);
$pdf->Cell (30, $h, '', 1);
$pdf->Cell (29, $h, '', 1);
$pdf->SetX ($pdf->GetX()+89);
$pdf->style ('color: red');
$pdf->Cell ( 7, $h, date('d'), 1, 0, 'C');
$pdf->Cell (13, $h, date('m'), 1, 0, 'C');
$pdf->Cell (10, $h, date('Y'), 1, 0, 'C');
$pdf->style ('strong');
$pdf->Cell (40, $h, '5311', 1, 0, 'C');
$pdf->Cell ( 0, $h, '', 1);
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell ( 8, $h, 'NR.', 'TLR', 0, 'C');
$pdf->Cell (22, $h, 'NR.', 'TLR', 0, 'C');
$pdf->Cell (13, $h, 'NR.', 'TLR', 0, 'C');
$pdf->Cell (82, $h, 'EXPLICATIA', 'TLR', 0, 'C');
$pdf->Cell (30, $h, 'INCASARI', 'TLR', 0, 'C');
$pdf->Cell (30, $h, 'PLATI', 'TLR', 0, 'C');
$pdf->Cell ( 0, $h, 'SIMBOL CONT CORESPONDENT', 'TLR', 0, 'C');
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell ( 8, $h, 'CRT', 'BLR', 0, 'C');
$pdf->Cell (22, $h, 'ACT CASA', 'BLR', 0, 'C');
$pdf->Cell (13, $h, 'ANEXA', 'BLR', 0, 'C');
$pdf->Cell (82, $h, '', 'BLR');
$pdf->Cell (30, $h, '', 'BLR');
$pdf->Cell (30, $h, '', 'BLR');
$pdf->Cell ( 0, $h, '', 'BLR');
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell ( 8, $h, '8', 1, 0, 'C');
$pdf->Cell (22, $h, '9', 1, 0, 'C');
$pdf->Cell (13, $h, '10', 1, 0, 'C');
$pdf->Cell (82, $h, '', 1);
$pdf->Cell (30, $h, '11', 1, 0, 'C');
$pdf->Cell (30, $h, '12', 1, 0, 'C');
$pdf->Cell ( 6, $h, '13', 1, 0, 'C');
$pdf->Cell ( 6, $h, '14', 1, 0, 'C');
$pdf->Cell ( 6, $h, '15', 1, 0, 'C');
$pdf->Cell ( 6, $h, '16', 1, 0, 'C');
$pdf->Cell ( 6, $h, '17', 1, 0, 'C');
$pdf->Cell (40, $h, '18', 1, 0, 'C');
$pdf->Cell ( 0, $h, '19', 1, 0, 'C');
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell (125, $h, 'SOLD ZIUA PRECEDENTA', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1);
$pdf->Cell (30, $h, '', 1);
$pdf->Cell ( 0, $h, '', 1);
$y += $h;

for ($c = 0; $c < 20; $c++) {
	$pdf->SetXY ($x, $y);
	$pdf->Cell ( 8, $h, '8', 1, 0, 'C');
	$pdf->Cell (22, $h, '9', 1, 0, 'R');
	$pdf->Cell (13, $h, '10', 1, 0, 'R');
	$pdf->Cell (82, $h, '', 1);
	$pdf->Cell (30, $h, '11', 1, 0, 'R');
	$pdf->Cell (30, $h, '12', 1, 0, 'R');
	$pdf->Cell ( 6, $h, '', 1);
	$pdf->Cell ( 6, $h, '', 1);
	$pdf->Cell ( 6, $h, '', 1);
	$pdf->Cell ( 6, $h, '', 1);
	$pdf->Cell ( 6, $h, '', 1);
	$pdf->Cell (40, $h, '', 1);
	$pdf->Cell ( 0, $h, '', 1);
	$y += $h;
	}

$pdf->SetXY ($x, $y);
$pdf->Cell ( 8, $h, '', 1);
$pdf->Cell (22, $h, '', 1);
$pdf->Cell (13, $h, '', 1);
$pdf->Cell (82, $h, 'TOTAL', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1, 0, 'R');
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell (40, $h, '', 1);
$pdf->Cell ( 0, $h, '', 1);
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell ( 8, $h, '', 1);
$pdf->Cell (22, $h, '', 1);
$pdf->Cell (13, $h, '', 1);
$pdf->Cell (82, $h, 'SOLD', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1, 0, 'R');
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell (40, $h, '', 1);
$pdf->Cell ( 0, $h, '', 1);
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell ( 8, $h, '', 1);
$pdf->Cell (22, $h, '', 1);
$pdf->Cell (13, $h, '', 1);
$pdf->Cell (82, $h, 'RULAJ', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1, 0, 'R');
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell ( 6, $h, '', 1);
$pdf->Cell (40, $h, '', 1);
$pdf->Cell ( 0, $h, '', 1);
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell ( 8, $h, '', 1);
$pdf->Cell (22, $h, '', 1);
$pdf->Cell (13, $h, '', 1);
$pdf->Cell (82, $h, 'REPORT PAGINA/TOTAL', 1, 0, 'R');
$pdf->Cell (30, $h, '', 1);
$pdf->Cell (30, $h, '', 1);
$pdf->Cell ( 0, $h, '', 'R');
$y += $h;

$pdf->SetXY ($x, $y);
$pdf->Cell (125, $h, 'CASIER,', 'TLB');
$pdf->Cell ( 0, $h, 'COMPARTIMENT FINANCIAR CONTABIL,', 'RB');
$y += $h;


$pdf->out();

?>
