<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

$c = 0;

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
$secretary = $current_user->user_lastname . ' ' . $current_user->user_firstname;

$cnfpa_begin = $product->get('current cnfpa begin');
$cnfpa_end = $product->get('current cnfpa end');
$months = date('n', $cnfpa_end) - date('n', $cnfpa_begin);
if ($months > 0) {
	$cnfpa_interval = date('j F', $cnfpa_begin).' - '.date('j F Y', $cnfpa_end);
	}
else
	$cnfpa_interval = date('j', $cnfpa_begin).' - '.date('j F Y', $cnfpa_end);
$months += 1;



$h = 5;
$c = 0;

#pagina 1 = contract
$pdf = new PDF ();
$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);

foreach ($diplomas as $diploma) {
	if ($c) $pdf->AddPage();
	$c++;

	$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);
	$person = new WP_CRM_Person ($diploma);


	$pdf->SetY (40);
	$pdf->Cell (0, $h, $pdf->fix('ADEVERINȚĂ'), 0, 1, 'C');
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->MultiCell (0, $h, $pdf->fix('Prin prezenta se adeverește că ' . ($person->get('gender') == 'M' ? 'domnul' : 'doamna') . ' ' . $person->get('last_name') . ' ' . $person->get('first_name').' având CNP '.$person->get('uin').', a absolvit cursul de '.$product->get('current anc name').', cod COR '.$product->get('current corno').', în perioada '.wp_crm_date($cnfpa_interval).', urmând a i se elibera un certificat de absolvire.'), 0, 1);
	$pdf->SetY ($pdf->GetY() + $h);
	$pdf->MultiCell (0, $h, $pdf->fix('Se eliberează prezenta pentru a-i servi la nevoie și este valabilă până la eliberarea certificatului.'), 0, 1);
	$pdf->SetY ($pdf->GetY() + $h);
	$pdf->MultiCell (0, $h, $pdf->fix($company->get('name').' este companie autorizată de Ministerul Muncii, Familiei și Egalității de Șanse și Ministerul Educației, Cercetării, Tineretului și Sportului să organizeze programul de formare profesională pentru ocupația de '.strtolower($product->get('current anc name')).' prin autorizația '.$product->get('current rnffpa').'.'));
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->Cell (0, $h, $pdf->fix('Director'), 0, 1, 'R');
	$pdf->Cell (0, $h, $director->get('name'), 0, 1, 'R');
	}

$pdf->out ();
?>
