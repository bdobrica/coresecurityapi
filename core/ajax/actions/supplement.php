<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');


if (!preg_match('/^[0-9,]+$/', urldecode($_GET['u']))) die ('ERROR');
$diplomas = explode(',', trim (urldecode($_GET['u']),','));
if (!is_array($diplomas)) die ('ERROR');
if (empty($diplomas)) die ('ERROR');

$product = new WP_CRM_Product (array (
	'series' => wp_crm_extract_series ($_GET['s']),
	'number' => wp_crm_extract_number ($_GET['s']),
	));

$trainer = new WP_CRM_Trainer ($product->get('trainer', $_GET['s']));
$training = $product->get('short name', $_GET['s']);
$location = new WP_CRM_Location ($product->get('location', $_GET['s']));
$company = new WP_CRM_Company($product->get('company', $_GET['s']));
$director = $company->get ('director');
$ancrep = $product->get ('current ancrep');
$secretary_user = get_userdata($product->get('responsible'));
$secretary = $secretary_user->user_lastname . ' ' . $secretary_user->user_firstname;
$competences = explode ("\n", $product->get('current competences'));

$c = 0;

$ox = 0;
$oy = -8;
$h = 6;

$pdf = new PDF ();
$pdf->SetAutoPageBreak(FALSE);
$pdf->style ('cnfpa');


foreach ($diplomas as $diploma) {
	if ($c) $pdf->AddPage();

	$person = new WP_CRM_Person ($diploma);

	$pdf->setXY (34 + $ox, 86 + $oy);
	$pdf->cell (90, $h, 'CERTIFICAT DE ABSOLVIRE', 0, 0, 'C');
	$pdf->setX (133 + $ox);
	$cnfpa = $person->get ('cnfpa', $product->get('current code'));
	$diploma = $person->get ('diploma', $product->get('current code'));
	$pdf->cell (35, $h, strtoupper(trim(wp_crm_extract_series($cnfpa))), 0, 0, 'C');
	$pdf->setX (177 + $ox);
	$pdf->cell (18, $h, str_pad(wp_crm_extract_number($cnfpa), 8, '0', STR_PAD_LEFT), 0, 0, 'C');
	$pdf->setXY (60 + $ox, 91 + $oy);
	$pdf->cell (70, $h, $product->get('current anc name'), 0, 0, 'C');
	$pdf->setX (155 + $ox);
	$pdf->cell (40, $h, $product->get('current corno'), 0, 0, 'C');
//	$pdf->setXY (150 + $ox, 96 + $oy);
//	$pdf->cell (45, $h, $product->get('current rnffpa'), 0, 0, 'C');

	$pdf->setXY (0, 153 + $oy);
	$pdf->cell (0, $h, $pdf->fix(strtoupper($company->get('name'))), 0, 0, 'C');
	$pdf->setXY (160 + $ox, 159 + $oy);
	$pdf->cell (35, $h, $product->get('current rnffpa'), 0, 0, 'C');
	$pdf->setXY (0, 179 + $oy);
	$pdf->cell (0, $h, 'SPECIALIZARE', 0, 0, 'C');
	$pdf->setXY (32 + $ox, 202 + $oy);
	$pdf->cell (65, $h, 'X', 0, 0, 'C');
	$pdf->setX (135 + $ox);
	$pdf->cell (65, $h, '', 0, 0, 'C');
	$pdf->setXY (0, 223 + $oy);
	$pdf->cell (0, $h, 'O.G. 129/2000 REPUBLICATA', 0, 0, 'C');
	$pdf->setXY (0, 244 + $oy);
	$pdf->cell (0, $h, $product->get('current studies'), 0, 0, 'C');
	$pdf->setXY (0, 265 + $oy);
	$pdf->cell (0, $h, 'SPECIALIZARE', 0, 0, 'C');
	$pdf->setXY (150 + $ox, 269 + $oy);
	$hours = $product->get('current hours');
	$theory = $product->get('current theory');
	$pdf->cell (30, $h, $hours, 0, 0, 'C');
	$pdf->setXY (150 + $ox, 273 + $oy);
	$pdf->cell (30, $h, $theory, 0, 0, 'C');
	$pdf->setXY (150 + $ox, 278 + $oy);
	$pdf->cell (30, $h, $hours - $theory, 0, 0, 'C');

	$ox = 0;
	$oy = -8;
	$h = 6;

	$pdf->AddPage ();


	$d = 0;
	foreach ($competences as $competence) {
		$competence = trim($competence);
		if (empty($competence)) continue;
		$pdf->setXY (20 + $ox, $d*10 + 34 + $oy);
		$pdf->cell (170, $h, $pdf->fix($competence), 0, 0, 'L');
		$d++;
		}

	$pdf->setXY (25 + $ox, 195 + $oy);
	$pdf->cell (60, $h, $pdf->fix($director->get('name')), 0, 0, 'C');
	$pdf->setXY (125 + $ox, 195 + $oy);
	$pdf->cell (60, $h, $pdf->fix($ancrep), 0, 0, 'C');
	$pdf->setXY (80 + $ox, 215 + $oy);
	$pdf->cell (60, $h, $pdf->fix($secretary), 0, 0, 'C');

	$c++;
	}

$pdf->out ();
?>
