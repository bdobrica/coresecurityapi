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
if (date('m', $cnfpa_begin) != date('m', $cnfpa_end))
	$cnfpa_interval = date('d/m', $cnfpa_begin).' - '.date('d/m/Y', $cnfpa_end);
else
	$cnfpa_interval = date('d', $cnfpa_begin).' - '.date('d/m/Y', $cnfpa_end);


$h = 7;
$s = count($diplomas);
$n = 15;

$c = 0;

$pdf = new PDF ('L');

$cols = array (
	'h3;Nr.' => 10,
	'h3;Nume absolvent' => 34,
	'h3;I.T.' => 10,
	'h3;Prenumele' => 34,
	'h3;CNP' => 30,
	'h3;Serie si nr. certificat' => 40,
	'h3;Data absolvirii' => 30,
	'h3;Numar eliberare' => 32,
	'h3;Mentiuni' => 0
	);

foreach ($diplomas as $diploma) {
	if (($c%$n) == 0) {
		if ($c) {
			$pdf->table ($cols, $rows, $h);
			$pdf->AddPage();
			}
		$rows = array ();

		$pdf->style ('cnfpa');
		$pdf->Cell (149, $h, $pdf->fix(strtoupper($company->get('name'))), 0, 0);
		$pdf->Cell (0, $h, 'Pagina '.(1+floor($c/$n)).' din '.ceil($s/$n).'.', 0, 1, 'R');
		$pdf->Cell (0, $h, $pdf->fix(strtoupper($company->get('city'))).', '.$pdf->fix(strtoupper($company->get('county'))), 0, 1);
		$pdf->style ();

		$pdf->style ('h2');
		$pdf->Cell (0, $h, 'BORDEROU ELIBERARE CERTIFICATE DE ABSOLVIRE', 0, 1, 'C');
		$pdf->Cell (0, $h, 'Programul de formare profesionala: '.$product->get('current anc name'), 0, 1, 'C');
		$pdf->Cell (0, $h, 'pentru ocupatia/competentele comune '.$product->get('current anc name').' defasurat in perioada '.$cnfpa_interval, 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY ();
		$pdf->SetY ($y + $h);
		}
	$c++;
	$person = new WP_CRM_Person ($diploma);
	$rows[] = array (
		$c,
		$pdf->fix($person->get('last_name')),
		$pdf->fix($person->get('initial')),
		$pdf->fix($person->get('first_name')),
		$person->get('uin'),
		$person->get('cnfpa', $product->get('current code')),
		date('d/m/Y', $cnfpa_end),
		$person->get('diploma', $product->get('current code')),
		' ',
		);
	}

$pdf->table ($cols, $rows, $h);

$y = $pdf->GetY ();
$pdf->SetY ($y + $h);

$pdf->style ('h3');
$pdf->Cell (0, $h, 'S-a aplicat timbru sec pe un numar de '.$c.' exemplare.', 0, 1);
$pdf->SetX (149);
$pdf->Cell (0, $h, 'Intocmit,', 0, 1);
$pdf->SetX (149);
$pdf->Cell (0, $h, $secretary, 0, 1);

$pdf->AddPage();

$n = 8;
$c = 0;
$h = 6;

$cols = array (
		array (
			'h3; ' => 10,
			'h3;  ' => 34,
			'h3;Cod' => 26,
			'h3;   ' => 7,
			'h3;    ' => 32,
			'h3;Cod ' => 16,
			'h3;      ' => 19,
			'h3;       ' => 18,
			'h3;        ' => 20,
			'h3;         ' => 22,
			'h3;Numele si semnatura' => 41,
			'h3;          ' => 0
			),
		array (
			'Nr.',
			'Numele si',
			'numeric',
			'Tip',
			'Calificare',
			'nomen-',
			'Perioada',
			'Media /',
			'Data',
			'Semnatura',
			'persoanei care',
			'Mentiuni',
			),
		array (
			' ',
			'prenumele',
			'personal',
			' ',
			' ',
			'clator',
			' ',
			'calificativ',
			'eliberarii',
			'de primire',
			'elibereaza certificatul',
			' ',
			)
	);

foreach ($diplomas as $diploma) {
	if (($c%$n) == 0) {
		if ($c) {
			$pdf->table ($cols, $rows, $h - 1);
			$pdf->AddPage ();
			}

		$pdf->style('cnfpa');
		$pdf->Cell (149, $h, $company->get('name'));
		$pdf->Cell (0, $h, 'Anexa nr. 8', 0, 1, 'R');
		$pdf->Cell (149, $h, $company->get('city').', '.$company->get('county'));
		$pdf->Cell (0, $h, 'Pagina '.(1+floor($c/$n)).' din '.ceil($s/$n), 0, 1, 'R');

		$pdf->style ('h3');
		$pdf->Cell (0, $h, 'REGISTRUL DE EVIDENTA NOMINALA', 0, 1, 'C');
		$pdf->Cell (0, $h, 'A ELIBERARII CERTIFICATELOR DE ABSOLVIRE', 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY ();
		$pdf->SetY ($y + $h);

		$rows = array ();
		}
	$c++;
	$person = new WP_CRM_Person ($diploma);
	$rows[] = array (
		$c.'.',
		$pdf->fix($person->get('last_name').' '.$person->get('first_name')),
		$person->get('uin'),
		'S',
		$product->get('current anc name'),
		$product->get('current corno'),
		$cnfpa_interval,
		$person->get('grade', $product->get('current code')),
		$person->get('cnfpa', $product->get('current code')),
		'', '', '', ''
		);
	}

$pdf->table ($cols, $rows, $h - 1);


$pdf->out ();
?>
