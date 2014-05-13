<?php
define ('WP_USE_THEMES', false);
define ('WP_CRM_DOCUMENT_COPIES', 2);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

if (!preg_match('/^[0-9,]+$/', urldecode($_GET['u']))) die ('ERROR');
$persons = explode(',', trim (urldecode($_GET['u']),','));
if (!is_array($persons)) die ('ERROR');
if (empty($persons)) die ('ERROR');

$tmp = array ();
foreach ($persons as $person) $tmp[] = new WP_CRM_Person ($person);
$persons = $tmp;

$product = new WP_CRM_Product (array (
		'series' => wp_crm_extract_series ($_GET['s']),
		'number' => wp_crm_extract_number ($_GET['s'])
		));

$cnfpa_begin = $product->get ('current cnfpa begin');
$time_begin = $product->get ('current begin');
$time_end = $product->get ('current end');
if (date('m', $time_begin) == date('m', $time_end))
        $date = date ('j - ', $time_begin) . date ('j F Y', $time_end);
else
        $date = date ('j F - ', $time_begin) . date ('j F Y', $time_end);
$date = wp_crm_date ($date);

if (date('m', $cnfpa_begin) == date('m', $time_end))
        $cnfpa_date = date ('j - ', $cnfpa_begin) . date ('j F Y', $time_end);
else
        $cnfpa_date = date ('j F - ', $cnfpa_begin) . date ('j F Y', $time_end);
$cnfpa_date = wp_crm_date ($cnfpa_date);

$trainer = new WP_CRM_Trainer ($product->get('trainer', $_GET['s']));
$location = new WP_CRM_Location ($product->get('location', $_GET['s']));
$company = new WP_CRM_Company($product->get('company', $_GET['s']));

$pdf = new PDF ();
$c = 1;
$h = 5;

$pdf->setY (20);
$pdf->style ('h3');
$pdf->Cell (0, $h, $product->get('short name') . ' (' . strtoupper($_GET['s']). ')', 0, 1, 'C');
$pdf->Cell (0, $h, 'Localitate: '.$location->get('city'), 0, 1, 'C');
$pdf->Cell (0, $h, 'Lector: '.$trainer->get('name'), 0, 1, 'C');
$pdf->Cell (0, $h, 'Data examen: '.wp_crm_date(date('j F Y', $time_end)), 0, 1, 'C');

$pdf->setY (45);
$pdf->Cell (0, $h, 'OPIS', 0, 1, 'C');

$pdf->setY (55);
$pdf->style ('opis:strong');
$pdf->Cell (0, $h, 'I. Documente generale curs', 0, 1);
$pdf->style ('opis');
if ($product->get('vat')) {
	$pdf->Cell (0, $h, ' 1) Lista prezenta', 0, 1);
	}
else {
	$pdf->Cell (0, $h, ' 1) Autorizatie CNFPA', 0, 1);
	$pdf->Cell (0, $h, ' 2) Cerere deschidere curs / Cerere organizare examen', 0, 1);
	$pdf->Cell (0, $h, ' 3) Variante test final proba teoretica (inchise in plicuri)', 0, 1);
	$pdf->Cell (0, $h, ' 4) Lista prezenta', 0, 1);
	$pdf->Cell (0, $h, ' 5) Tabel predare lucrari proba practica', 0, 1);
	$pdf->Cell (0, $h, ' 6) Tabel predare lucrari proba teoretica', 0, 1);
	$pdf->Cell (0, $h, ' 7) Foaie de notare lucrari proba practica', 0, 1);
	$pdf->Cell (0, $h, ' 8) Foaie de notare lucrari proba teoretica', 0, 1);
	$pdf->Cell (0, $h, ' 9) Catalog', 0, 1);
	$pdf->Cell (0, $h, '10) Proces verbal de examinare', 0, 1);
	}

$pdf->setY (120);
$pdf->style ('opis:strong');
$pdf->Cell (0, $h, 'II. Documente cursanti', 0, 1);
$pdf->style ('opis');

$rows = array ();
foreach ($persons as $person) {
	$rows[] = array (
		($c++).'.',
		$person->get('last_name'),
		$person->get('first_name'),
		'', '', '', '', '', '', '', '',
		);
	}

$x = $pdf->getX();

$cols = array (
	'Nr.' => 10,
	'Nume' => 40,
	'Prenume' => 40,
	'D1' => 8,
	'D2' => 8,
	'D3' => 8,
	'D4' => 8,
	'D5' => 8,
	'D6' => 8,
	'D7' => 8,
	'D8' => 8,
	);

$pdf->table ($cols, $rows, $h-1);

$h = 4;
$pdf->setY (240);
$pdf->style ('strong');
$pdf->Cell (0, $h, 'Legenda documente', 0, 1);
$pdf->style ();
$pdf->Cell (0, $h, 'D1. Carte de identitate (CI)', 0, 1);
$pdf->Cell (0, $h, 'D2. Certificat de casatorie (daca e cazul)', 0, 1);
$pdf->Cell (0, $h, 'D3. Diploma de studii', 0, 1);
$pdf->Cell (0, $h, 'D4. Cerere de inscriere curs/examen', 0, 1);
$pdf->Cell (0, $h, 'D5. Contract de formare profesiona', 0, 1);
$pdf->Cell (0, $h, 'D6. Trei teste intermediare completate', 0, 1);
$pdf->Cell (0, $h, 'D7. Proba teoretica - fisa de examnare si test final', 0, 1);
$pdf->Cell (0, $h, 'D8. Proba practica - lucrarea', 0, 1);

# page#2

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '1) Autorizatie CNFPA', 0, 1, 'C');
	$pdf->style ();

	

# page#3

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '2) Cerere deschidere curs / Cerere organizare examen', 0, 1, 'C');
	$pdf->style ();

# page#4

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '3) Variante test final proba teoretica (inchise in plicuri)', 0, 1, 'C');
	$pdf->style ();

# page#5

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '4) Lista prezenta', 0, 1, 'C');
	$pdf->style ();

	$pdf->AddPage ('L');
	$c = 1;
	$pdf->setY (25);
	$pdf->style ('h1');
	$pdf->Cell (0, 7, $product->get('nice name'), 0, 1, 'C');
	$pdf->style ();

	$pdf->style ('h2');
	$pdf->Cell (0, 6, $cnfpa_date, 0, 1, 'C');
	$pdf->style ();

	$pdf->style ();
	$pdf->Cell (0, 4, '- fisa de prezenta -', 0, 1, 'C');
	$pdf->style ();

	$y = $pdf->GetY();
	$pdf->SetY($y + 5);

	$pdf->Image(dirname(__FILE__).'/landscape.png', 0, 0, 297, 210);

	$cols = array (
		'h3;NR.' => 10,
		'h3;NUME' => 40,
		'h3;PRENUME' => 40,
		'h3;TELEFON' => 40,
		'h3;EMAIL' => 70,
		'h3;SEMNATURA' => 0
		);
	$rows = array();

	foreach ($persons as $person) {

		$rows[] = array (
			$c++,
			$person->get('last_name'),
			$person->get('first_name'),
			$person->get('phone'),
			$person->get('email'),
			''
			);
		}
	$pdf->table ($cols, $rows, 5);
	$pdf->setY (170);
	$pdf->style ('strong');
	$pdf->Cell (0, 5, 'Trainer', 0, 1);
	$pdf->style ();
	$pdf->Cell (0, 5, $trainer->get('name'), 0, 1);
	$pdf->Cell (100, 5, '', 'B', 1);


# page#6

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '5) Tabel predare lucrari proba practica', 0, 1, 'C'); # anexa 5
	$pdf->style ();

	for ($copies = 0; $copies < WP_CRM_DOCUMENT_COPIES; $copies++) {

		$pdf->AddPage ();
		$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);
		$c = 1;

		$pdf->setY (16);
		$pdf->style ('strong');
		$pdf->Cell (170, 5, 'Anexa nr. 5', 0, 0, 'R');

		$pdf->setY (30);
		$pdf->style ('h2');
		$pdf->Cell (0, 5, 'Tabel nominal de predare a lucrarilor practice', 0, 1, 'C');
		$pdf->style ('strong');
		$pdf->Cell (0, 4, 'la examenul de absolvire a programului de specializare pentru', 0, 1, 'C');
		$pdf->Cell (0, 4, 'ocupatia/competentele comune '.$product->get('current anc name').' organizat in data '.date('d/m/y', $time_end), 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY();
		$pdf->SetY ($y + 5);

		$rows = array ();
		foreach ($persons as $person) {
			$rows[] = array (
				($c++).'.',
				strtoupper($person->get('last_name').', '.$person->get('first_name')),
				'',
				'',
				''
				);
			}

		$cols = array (
			'strong;Nr.' => 8,
			'strong;Numele si prenumele' => 80,
			'strong;Nr. de pagini' => 20 ,
			'strong;Semnatura participantului' => 40,
			'strong;Observatii' => 0,
			);

		$pdf->setY (55);
		$pdf->table ($cols, $rows, 5);

		$pdf->SetY (230);
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Presedintele comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);

		$pdf->SetY (245);	
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Membrii comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->style ();
		}

# page#7

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '6) Tabel predare lucrari proba teoretica', 0, 1, 'C'); # anexa 4
	$pdf->style ();

	for ($copies = 0; $copies < WP_CRM_DOCUMENT_COPIES; $copies++) {

		$pdf->AddPage ();
		$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);
		$c = 1;

		$pdf->setY (16);
		$pdf->style ('strong');
		$pdf->Cell (170, 5, 'Anexa nr. 5', 0, 0, 'R');

		$pdf->setY (30);
		$pdf->style ('h2');
		$pdf->Cell (0, 5, 'Tabel nominal de predare a lucrarilor teoretice', 0, 1, 'C');
		$pdf->style ('strong');
		$pdf->Cell (0, 4, 'la examenul de absolvire a programului de specializare pentru', 0, 1, 'C');
		$pdf->Cell (0, 4, 'ocupatia/competentele comune '.$product->get('current anc name').' organizat in data '.date('d/m/y', $time_end), 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY();
		$pdf->SetY ($y + 5);

		$rows = array ();
		foreach ($persons as $person) {
			$rows[] = array (
				($c++).'.',
				strtoupper($person->get('last_name').', '.$person->get('first_name')),
				'',
				'',
				''
				);
			}

		$cols = array (
			'strong;Nr.' => 8,
			'strong;Numele si prenumele' => 80,
			'strong;Nr. de pagini' => 20 ,
			'strong;Semnatura participantului' => 40,
			'strong;Observatii' => 0,
			);

		$pdf->setY (55);
		$pdf->table ($cols, $rows, 5);

		$pdf->SetY (230);
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Presedintele comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);

		$pdf->SetY (245);	
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Membrii comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->style ();
		}

# page#8

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '7) Foaie de notare lucrari proba practica', 0, 1, 'C'); # anexa 3
	$pdf->style ();

	for ($copies = 0; $copies < WP_CRM_DOCUMENT_COPIES; $copies++) {

		$pdf->AddPage ();
		$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);
		$c = 1;

		$pdf->setY (16);
		$pdf->style ('strong');
		$pdf->Cell (170, 5, 'Anexa nr. 3', 0, 0, 'R');

		$pdf->setY (30);
		$pdf->style ('h2');
		$pdf->Cell (0, 5, 'Foaia de notare la proba practica', 0, 1, 'C');
		$pdf->style ('strong');
		$pdf->Cell (0, 4, 'la examenul de absolvire a programului de specializare pentru', 0, 1, 'C');
		$pdf->Cell (0, 4, 'ocupatia/competentele comune '.$product->get('current anc name').' organizat in data '.date('d/m/y', $time_end), 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY();
		$pdf->SetY ($y + 5);

		$rows = array ();
		foreach ($persons as $person) {
			$rows[] = array (
				($c++).'.',
				strtoupper($person->get('last_name').', '.$person->get('first_name')),
				'',
				'',
				'',
				'',
				'',
				);
			}

		$cols = array (
			'strong;Nr.' => 8,
			'strong;Numele si prenumele' => 70,
			'strong;Subiect / Lucrare' => 30,
			'strong;Nota 1' => 15,
			'strong;Nota 2' => 15,
			'strong;Media' => 15,
			'strong;Observatii' => 0,
			);

		$pdf->setY (55);
		$pdf->table ($cols, $rows, 5);

		$pdf->SetY (230);
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Presedintele comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);

		$pdf->SetY (245);	
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Membrii comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->style ();
		}

# page#9

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '8) Foaie de notare lucrari proba teoretica', 0, 1, 'C'); # anexa 3
	$pdf->style ();

	for ($copies = 0; $copies < WP_CRM_DOCUMENT_COPIES; $copies++) {

		$pdf->AddPage ();
		$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);
		$c = 1;

		$pdf->setY (16);
		$pdf->style ('strong');
		$pdf->Cell (170, 5, 'Anexa nr. 3', 0, 0, 'R');

		$pdf->setY (30);
		$pdf->style ('h2');
		$pdf->Cell (0, 5, 'Foaia de notare la proba teoretica', 0, 1, 'C');
		$pdf->style ('strong');
		$pdf->Cell (0, 4, 'la examenul de absolvire a programului de specializare pentru', 0, 1, 'C');
		$pdf->Cell (0, 4, 'ocupatia/competentele comune '.$product->get('current anc name').' organizat in data '.date('d/m/y', $time_end), 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY();
		$pdf->SetY ($y + 5);

		$rows = array ();
		foreach ($persons as $person) {
			$rows[] = array (
				($c++).'.',
				strtoupper($person->get('last_name').', '.$person->get('first_name')),
				'',
				'',
				'',
				'',
				'',
				);
			}

		$cols = array (
			'strong;Nr.' => 8,
			'strong;Numele si prenumele' => 70,
			'strong;Subiect / Lucrare' => 30,
			'strong;Nota 1' => 15,
			'strong;Nota 2' => 15,
			'strong;Media' => 15,
			'strong;Observatii' => 0,
			);

		$pdf->setY (55);
		$pdf->table ($cols, $rows, 5);

		$pdf->SetY (230);
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Presedintele comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);

		$pdf->SetY (245);	
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Membrii comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->style ();
		}

# page#10

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '9) Catalog', 0, 1, 'C');  # anexa 4
	$pdf->style ();

	for ($copies = 0; $copies < WP_CRM_DOCUMENT_COPIES; $copies++) {

		$pdf->AddPage ();
		$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);
		$c = 1;

		$pdf->setY (16);
		$pdf->style ('strong');
		$pdf->Cell (170, 5, 'Anexa nr. 4', 0, 0, 'R');

		$pdf->setY (30);
		$pdf->style ('h1');
		$pdf->Cell (0, 7, 'CATALOG', 0, 1, 'C');
		$pdf->style ('strong');
		$pdf->Cell (0, 4, 'cu rezultatele examenului de absolvire a programului de specializare', 0, 1, 'C');
		$pdf->Cell (0, 4, 'pentru ocupatia / competentele comune la cursul', 0, 1, 'C');
		$pdf->Cell (0, 4, $product->get('current anc name').' organizat in data '.date('d/m/y', $time_end), 0, 1, 'C');
		$pdf->style ();

		$y = $pdf->GetY();
		$pdf->SetY ($y + 5);

		$rows = array ();
		foreach ($persons as $person) {
			$rows[] = array (
				($c++).'.',
				strtoupper($person->get('last_name').', '.$person->get('first_name')),
				'',
				'',
				'',
				'',
				);
			}

		$cols = array (
			'strong;Nr.' => 8,
			'strong;Numele si prenumele' => 70,
			'strong;Medie proba practica' => 30,
			'strong;Medie proba teoretica' => 31,
			'strong;Medie generala' => 22,
			'strong;Observatii' => 0,
			);

		$pdf->setY (55);
		$pdf->table ($cols, $rows, 5);

		$pdf->SetY (230);
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Presedintele comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);

		$pdf->SetY (245);	
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Membrii comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->style ();
		}

# page#11

	$pdf->AddPage ();
	$pdf->style ('h1');
	$pdf->Cell (0, 10, '10) Proces verbal de examinare', 0, 1, 'C');
	$pdf->style ();

	for ($copies = 0; $copies < WP_CRM_DOCUMENT_COPIES; $copies++) {

		$pdf->AddPage ();
		$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);
		$c = 1;

		$pdf->setY (16);
		$pdf->style ('strong');
		$pdf->Cell (170, 5, 'Anexa nr. 1', 0, 0, 'R');

		$pdf->setY (30);
		$pdf->style ('h1');
		$pdf->Cell (0, 7, 'PROCES - VERBAL', 0, 1, 'C');

		$y = $pdf->GetY();
		$pdf->setY ($y+10);

		$pdf->style ('opis');
		$pdf->MultiCell (0, 5, 'Incheiat astazi, ' . wp_crm_date(date('j F Y', $time_end)) . ', cu ocazia sustinerii examenului de absolvire a programului de formare profesionala '.$product->get('current anc name').' organizat de '.$company->get('name').' in perioada '.wp_crm_date(date('j F Y', $cnfpa_begin)).' - '.wp_crm_date(date('j F Y', $time_end)).' la sediul '.$company->get('name').'.', 0, 'J');
		$y = $pdf->GetY();
		$pdf->setY ($y+5);
		$pdf->MultiCell (0, 5, 'Comisia de examinare s-a intrunit in data de ' . wp_crm_date(date('j F Y', $time_end - 172800)) . ' la ora ......... si a stabilit graficul de desfasurare al examenului de absolvire dupa cum urmeaza:', 0, 'J');
		$pdf->Cell (0, 5, '- proba teoretica, in data de ' . wp_crm_date(date('j F Y', $time_end)) . ', la ora .........', 0, 1);
		$pdf->Cell (0, 5, '- proba practica, in data de ' . wp_crm_date(date('j F Y', $time_end)) . ', la ora .........', 0, 1);
		$y = $pdf->GetY();
		$pdf->setY ($y+5);
		$pdf->MultiCell (0, 5, 'Graficul de desfasurare a fost afisat cu ......... de ore inainte de inceperea examenului de absolvire', 0, 'J');
		$y = $pdf->GetY();
		$pdf->setY ($y+5);
		$pdf->MultiCell (0, 5, 'Au fost verificate conditiile tehnice de desfasurare a examenului de absolvire care au fost gasite ......... corespunzatoare / ......... necorespunzatoare.', 0, 'J');
		$y = $pdf->GetY();
		$pdf->setY ($y+5);
		$pdf->MultiCell (0, 5, 'Au fost stabilite subiectele pentru probele examenului de absolvire, dupa cum urmeaza:', 0, 'J');
		$pdf->MultiCell (0, 5, '- la proba teoretica, din 5 variante de subiecte propuse a fost extrasa prin tragere la sorti varianta nr. .........', 0, 'J');
		$pdf->MultiCell (0, 5, '- la proba practica, ' . str_pad('', 130, '.'), 0, 'J');
		$y = $pdf->GetY();
		$pdf->setY ($y+5);
		$pdf->Cell (0, 5, 'Situatia statistica privind rezultatele examenului de absolvire:', 0, 1);
		$y = $pdf->GetY();
		$pdf->setY ($y+5);


		$pdf->style ('small');
		$y = $pdf->GetY();
		$x = $pdf->GetX();
		$pdf->MultiCell (27, 4, "\nNumar\npersoane\ninscrise la\nprogramul de\nformare\n\n", 1, 'C');
		$pdf->setXY ($x + 27, $y);
		$pdf->MultiCell (27, 4, "\nNumar\npersoane care\nau finalizat\nprogramul\nde formare\n\n", 1, 'C');
		$pdf->setXY ($x + 54, $y);
		$pdf->MultiCell (27, 4, "\nNumar\nparticipanti la\nexamenul de\nabsolvire\n\n\n", 1, 'C');
		$pdf->setXY ($x + 81, $y);
		$pdf->MultiCell (27, 4, "\nNumar\nabsolventi\n", 1, 'C');
		$pdf->setXY ($x + 81, $y + 12);
		$pdf->MultiCell (13, 4, "\ntotal\n\n\n", 1, 'C');
		$pdf->setXY ($x + 94, $y + 12);
		$pdf->MultiCell (14, 4, "din\ncare\nsub 25\nde ani", 1, 'C');
		$pdf->setXY ($x + 108, $y);
		$pdf->MultiCell (27, 4, "Numar\nfemei\nabsolvente", 1, 'C');
		$pdf->setXY ($x + 108, $y + 12);
		$pdf->MultiCell (13, 4, "\ntotal\n\n\n", 1, 'C');
		$pdf->setXY ($x + 121, $y + 12);
		$pdf->MultiCell (14, 4, "din\ncare\nsub 25\nde ani", 1, 'C');
		$pdf->setXY ($x + 135, $y);
		$pdf->MultiCell (27, 4, "\nRata de\nabandon\n(100-2/1*100)\n%\n\n\n", 1, 'C');
		$pdf->setXY ($x + 162, $y);
		$pdf->MultiCell (27, 4, "\nRata de\npromovabilitate\n(4/3*100)\n%\n\n\n", 1, 'C');

		$pdf->setXY ($x, $y + 28);
		$pdf->Cell (27, 4, '1', 1, 0, 'C');
		$pdf->Cell (27, 4, '2', 1, 0, 'C');
		$pdf->Cell (27, 4, '3', 1, 0, 'C');
		$pdf->Cell (13, 4, '4', 1, 0, 'C');
		$pdf->Cell (14, 4, '5', 1, 0, 'C');
		$pdf->Cell (13, 4, '6', 1, 0, 'C');
		$pdf->Cell (14, 4, '7', 1, 0, 'C');
		$pdf->Cell (27, 4, '8', 1, 0, 'C');
		$pdf->Cell (27, 4, '9', 1, 1, 'C');

		$pdf->Cell (27, 4, '', 1, 0, 'C');
		$pdf->Cell (27, 4, '', 1, 0, 'C');
		$pdf->Cell (27, 4, '', 1, 0, 'C');
		$pdf->Cell (13, 4, '', 1, 0, 'C');
		$pdf->Cell (14, 4, '', 1, 0, 'C');
		$pdf->Cell (13, 4, '', 1, 0, 'C');
		$pdf->Cell (14, 4, '', 1, 0, 'C');
		$pdf->Cell (27, 4, '', 1, 0, 'C');
		$pdf->Cell (27, 4, '', 1, 1, 'C');

		$y = $pdf->GetY();
		$pdf->setY ($y+5);
		$pdf->Cell (0, 5, 'Observatii / probleme:', 0, 1);
		$pdf->Cell (0, 5, '', 'B', 1);
		$pdf->Cell (0, 5, '', 'B', 1);
		$pdf->Cell (0, 5, '', 'B', 1);
		$pdf->Cell (0, 5, '', 'B', 1);
		$pdf->Cell (0, 5, '', 'B', 1);
		$pdf->Cell (0, 5, '', 'B', 1);

		$pdf->SetY (230);
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Presedintele comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);

		$pdf->SetY (245);	
		$pdf->style ('strong');
		$pdf->Cell (100, 5, 'Membrii comisiei de examinare', 0, 1);
		$pdf->style ('small');
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->Cell (100, 5, '', 'B', 0);
		$pdf->Cell (10, 5, '', 0, 0);
		$pdf->Cell (60, 5, '', 'B', 0);
		$pdf->Cell (0, 5, '(semnatura)', 0, 1);
		$pdf->style ();
		}

# page#12
	$c = 1;
	foreach ($persons as $person) {
		$pdf->AddPage ();
		$pdf->style ('h1');
		$pdf->Cell (0, 10, ($c++).'. '.$person->get('last_name').', '.$person->get('first_name'), 0, 1);
		$y = $pdf->GetY();
		$pdf->SetY ($y + 10);
		$pdf->style ('h3');
		$pdf->Cell (0, $h, 'Dosarul contine obligatoriu aceste documente in ordine:', 0, 1);
		$pdf->style ('large');
		$y = $pdf->GetY();
		$pdf->SetY ($y + 10);
		$pdf->Cell (0, $h, ' 1) Carte de identitate (CI)', 0, 1);
		$pdf->Cell (0, $h, ' 2) Certificat de casatorie (daca e cazul)', 0, 1);
		$pdf->Cell (0, $h, ' 3) Diploma de studii', 0, 1);
		$pdf->Cell (0, $h, ' 4) Cerere de inscriere curs/examen', 0, 1);
		$pdf->Cell (0, $h, ' 5) Contract de formare profesiona', 0, 1);
		$pdf->Cell (0, $h, ' 6) Trei teste intermediare completate', 0, 1);
		$pdf->Cell (0, $h, ' 7) Proba teoretica - fisa de examnare si test final', 0, 1);
		$pdf->Cell (0, $h, ' 8) Proba practica - lucrarea', 0, 1);
		$pdf->style ('h1');
		$y = $pdf->GetY();
		$pdf->SetY ($y + 10);
		$pdf->Cell (0, 10, 'ATENTIE!', 'TB', 1, 'C');
		$pdf->Cell (0, 10, 'DOCUMENTELE VOR FI INTRODUSE OBLIGATORIU', 'LR', 1, 'C');
		$pdf->Cell (0, 10, 'IN ACEASTA ORDINE, PRIN PERFORARE!', 'LR', 1, 'C');
		$pdf->Cell (0, 10, 'DOCUMENTELE NU VOR FI CAPSATE!', 'LR', 1, 'C');
		$pdf->Cell (0, 10, 'DOCUMENTELE NU VOR FI INTRODUSE IN FOLII!', 'LRB', 1, 'C');
		$pdf->style ();
		}


$pdf->out ();
?>
