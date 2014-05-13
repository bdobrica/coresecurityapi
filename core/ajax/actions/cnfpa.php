<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

$c = 0;

#$_GET['u'] = 1027;
#$_GET['s'] = 'IRUBUI011';

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

$c = 0;

$pdf = new PDF ('L');
//$pdf->Image(dirname(__FILE__).'/cnfpa.png', 0, 0, 297, 210);
$pdf->SetAutoPageBreak(FALSE);

$numbers = array ('zero', 'unu', 'doi', 'trei', 'patru', 'cinci', chr(186).'ase', chr(186).'apte', 'opt', 'nou'.chr(227), 'zece');

foreach ($diplomas as $diploma) {
	if ($c) $pdf->AddPage('L');
	$pdf->style();

	$person = new WP_CRM_Person ($diploma); //WP_CRM_Client(array ('person' => $diploma, 'product' => $product->get('current code')));
//	$person->set ('diploma', $product->get('current code'));

	$olx = 1;
	$oly = -1;


	$pdf->setXY (30 + $olx, 65 + $oly);
	$pdf->Cell (50, 5, $pdf->fix ($person->get('name')), 0, 0, 'C');

	$uin = str_split($person->get('uin'));
	foreach ($uin as $c => $num) {
		$pdf->setXY (29 + 4.15*$c + $olx, 72 + $oly);
		$pdf->Cell (4, 5, $num, 0, 0, 'C');
		}

	$birthday = $person->get('birthday');

	$pdf->setXY (43 + $olx, 77 + $oly);
	$pdf->Cell (10, 5, date('Y', $birthday), 0, 0, 'C');
	$pdf->setX (60 + $olx);
	$pdf->Cell (20, 5, date('m', $birthday), 0, 0, 'C');
	$pdf->setXY (20 + $olx, 81 + $oly);
	$pdf->Cell (10, 5, date('d', $birthday), 0, 0, 'C');
	$pdf->setX (48 + $olx);
	$pdf->Cell (32, 5, $pdf->fix($person->get('id_place')), 0, 0, 'C');
	$pdf->setXY (40 + $olx, 86 + $oly);
	$pdf->Cell (40, 5, $pdf->fix($person->get('county')), 0, 0, 'C');
	$pdf->setXY (33 + $olx, 91 + $oly);
	$pdf->Cell (47, 5, $pdf->fix($person->get('id_father')), 0, 0, 'C');
	$pdf->setXY (25 + $olx, 96 + $oly);
	$pdf->Cell (55, 5, $pdf->fix($person->get('id_mother')), 0, 0, 'C');

	$cnfpa_date_begin = $product->get('current cnfpa begin');
	$cnfpa_date_end = $product->get('current cnfpa end');
	$cnfpa_date = date('d/m', $cnfpa_date_begin).' - '.date('d/m/Y', $cnfpa_date_end);

	$pdf->SetXY (55 + $olx, 101 + $oly);
	$pdf->Cell (25, 5, $cnfpa_date, 0, 0, 'C');

	$pdf->SetXY (50 + $olx, 111 + $oly);
	$pdf->Cell (28, 5, $product->get('current hours'), 0, 0, 'C');

	$pdf->SetXY (15 + $olx, 120 + $oly);
	$pdf->MultiCell (68, 5, $pdf->fix($product->get('current ancname')), 0);

	$pdf->SetXY (30 + $olx, 129 + $oly);
	$pdf->Cell (30, 5, $product->get('current corno'), 0, 0, 'C');

	$pdf->SetXY (15 + $olx, 134 + $oly);
	$pdf->Cell (68, 5, $pdf->fix(strtoupper($company->get('name'))), 0, 0, 'C');
	$pdf->SetXY (50 + $olx, 139 + $oly);
	$pdf->Cell (32, 5, $pdf->fix(strtoupper($company->get('city'))), 0, 0, 'C');
	$pdf->SetXY (26 + $olx, 144 + $oly);
	$pdf->Cell (56, 5, $pdf->fix(strtoupper($company->get('county'))), 0, 0, 'C');

	$pdf->SetXY (15 + $olx, 153 + $oly);
	$pdf->Cell (68, 5, $product->get('current rnffpa'), 0, 0, 'C');

	$pdf->SetXY (22 + $olx, 163 + $oly);
	$pdf->Cell (10, 5, date('Y', $cnfpa_date_end), 0, 0, 'C');
	$pdf->SetXY (40 + $olx, 163 + $oly);
	$pdf->Cell (28, 5, strtoupper(wp_crm_date(date('F', $cnfpa_date_end))), 0, 0, 'C');
	$pdf->SetXY (75 + $olx, 163 + $oly);
	$pdf->Cell (8, 5, date('j', $cnfpa_date_end), 0, 0, 'C');

	$pdf->SetXY (15 + $olx, 175 + $oly);
	if (!is_null($director))
		$pdf->Cell (32, 5, $pdf->fix($director->get('name')), 0, 0, 'L');
	$pdf->SetX (47 + $olx);
	$pdf->Cell (32, 5, $pdf->fix(strtoupper($ancrep)), 0, 0, 'R');
	$pdf->SetXY (15 + $olx, 183 + $oly);
	$pdf->Cell (64, 5, $pdf->fix(strtoupper($secretary)), 0, 0, 'C');


	$diploma = str_pad ($person->get('diploma', $product->get('current code')), 5, '0', STR_PAD_LEFT);
	$pdf->SetXY (20 + $olx, 187 + $oly);
	$pdf->Cell (12, 5, $diploma, 0, 0, 'C');
	$pdf->SetXY (57 + $olx, 187 + $oly);
	$pdf->Cell (25, 5, date('d/m/Y'), 0, 0, 'C');

	$orx = 2;
	$ory = -2;

	$pdf->style ('cnfpa:name');
	$pdf->SetXY (128 + $orx, 88 + $ory);
	$pdf->Cell (132, 6, $pdf->fix($person->get('last_name').' '.$person->get('initial').' '.$person->get('first_name')), 0, 0, 'C');

	$pdf->style ('cnfpa');
	foreach ($uin as $c => $num) {
		$pdf->setXY (129 + 4.15*$c + $orx, 95 + $ory);
		$pdf->Cell (4, 6, $num, 0, 0, 'C');
		}

	$pdf->SetXY (217 + $orx, 95 + $ory);
	$pdf->Cell (11, 6, date('Y', $birthday), 0, 0, 'C');
	$pdf->SetXY (237 + $orx, 95 + $ory);
	$pdf->Cell (30, 6, strtoupper(wp_crm_date(date('F', $birthday))), 0, 0, 'C');
	$pdf->SetXY (120 + $orx, 101 + $ory);
	$pdf->Cell (10, 6, date('d', $birthday), 0, 0, 'C');
	$pdf->SetXY (161 + $orx, 101 + $ory);
	$pdf->Cell (48, 6, $pdf->fix($person->get('id_place')), 0, 0, 'C');
	$pdf->SetXY (239 + $orx, 101 + $ory);
	$pdf->Cell (30, 6, $pdf->fix($person->get('county')), 0, 0, 'C');
	$pdf->SetXY (140 + $orx, 107 + $ory);
	$pdf->Cell (53, 6, $pdf->fix($person->get('id_father')), 0, 0, 'C');
	$pdf->SetXY (206 + $orx, 107 + $ory);
	$pdf->Cell (58, 6, $pdf->fix($person->get('id_mother')), 0, 0, 'C');
	$pdf->SetXY (158 + $orx, 113 + $ory);
	$pdf->Cell (38, 6, $cnfpa_date, 0, 0, 'C');
	$pdf->SetXY (160 + $orx, 119 + $ory);
	$pdf->Cell (15, 6, $product->get('current hours'), 0, 0, 'C');
	$pdf->SetXY (120 + $orx, 125 + $ory);
	$pdf->Cell (100, 6, $pdf->fix($product->get('current ancname')), 0, 0, 'C');
	$pdf->SetXY (240 + $orx, 125 + $ory);
	$pdf->Cell (34, 6, $product->get('current corno'), 0, 0, 'C');
	$pdf->SetXY (140 + $orx, 131 + $ory);
	$pdf->Cell (50, 6, strtoupper($company->get('name')), 0, 0, 'C');
	$pdf->SetXY (240 + $orx, 131 + $ory);
	$pdf->Cell (25, 6, $pdf->fix(strtoupper($company->get('city'))), 0, 0, 'C');
	$pdf->SetXY (130 + $orx, 136 + $ory);
	$pdf->Cell (38, 6, $pdf->fix(strtoupper($company->get('county'))), 0, 0, 'C');
	$pdf->SetXY (170 + $orx, 142 + $ory);
	$pdf->Cell (50, 6, $product->get('current rnffpa'), 0, 0, 'C');
	$pdf->SetXY (146 + $orx, 148 + $ory);
	$pdf->Cell (11, 6, date('Y', $cnfpa_date_end), 0, 0, 'C');
	$pdf->SetXY (165 + $orx, 148 + $ory);
	$pdf->Cell (20, 6, date('m', $cnfpa_date_end), 0, 0, 'C');
	$pdf->SetXY (194 + $orx, 148 + $ory);
	$pdf->Cell (7, 6, date('d', $cnfpa_date_end), 0, 0, 'C');
	$pdf->SetXY (237 + $orx, 148 + $ory);
	$grade = $person->get ('grade', $product->get('current code'));
	$grade_int = floor($grade);
	$grade_dec = $grade - $grade_int;
	$pdf->Cell (32, 6, sprintf('%.2f', $grade) . ' ('.$numbers[$grade_int].($grade_dec ? sprintf(' %02d%%', round(100*$grade_dec)) : '').')' , 0, 0, 'C');

	$pdf->SetXY (126 + $orx, 176 + $ory);
	if (!is_null($director))
		$pdf->Cell (50, 6, $pdf->fix($director->get('name')), 0, 0, 'C');
	$pdf->SetX (214 + $orx);
	$pdf->Cell (50, 6, $pdf->fix(strtoupper($ancrep)), 0, 0, 'C');
	$pdf->SetXY (167 + $orx, 179 + $ory);
	$pdf->Cell (50, 6, $pdf->fix(strtoupper($secretary)), 0, 0, 'C');

	$pdf->SetXY (120 + $orx, 189 + $ory);
	$pdf->Cell (16, 6, $diploma, 0, 0, 'C');
	$pdf->SetXY (174 + $orx, 189 + $ory);
	$pdf->Cell (10, 6, date('Y'), 0, 0, 'C');
	$pdf->SetXY (194 + $orx, 189 + $ory);
	$pdf->Cell (27, 6, strtoupper(wp_crm_date(date('F'))), 0, 0, 'C');
	$pdf->SetXY (232 + $orx, 189 + $ory);
	$pdf->Cell (10, 6, date('j'), 0, 0, 'C');
	$c++;
	}

#contract
#$h = 4;
#$pdf->Cell (0, $h, 'CONTRACT DE FORMARE PROFESIONALA', 0, 0, 'C');


#	die();
$pdf->out ();
?>
