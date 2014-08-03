<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

function wp_crm_fill ($value, $len = 5) {
	if ($value) return $value;
	$out = '';
	for ($c = 0; $c<$len; $c++) $out .= '... ';
	return trim($out);
	}

$copies = 2;

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
	$cnfpa_interval = date('d/m', $cnfpa_begin).' - '.date('d/m/Y', $cnfpa_end);
	}
else
	$cnfpa_interval = date('d', $cnfpa_begin).' - '.date('d/m/Y', $cnfpa_end);
$months += 1;



$c = 0;

$pdf = new PDF ();

foreach ($diplomas as $diploma) {
	if ($c) $pdf->AddPage();
	$h = 6;

	$person = new WP_CRM_Person ($diploma);


#pagina 1 = contract
	for ($copy = 0; $copy < $copies; $copy++) {
		$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);

		$pdf->style ('h2');
		$pdf->Cell (0, $h, $pdf->fix('CONTRACT'), 0, 1, 'C');
		$pdf->Cell (0, $h, $pdf->fix('DE FORMARE PROFESIONALĂ'), 0, 1, 'C');
		$pdf->style ('h3');
		$pdf->Cell (0, $h, $pdf->fix('Nr. ...... din ..../..../.......'), 0, 1, 'C');
		$pdf->style ();

		$h = 5;
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('1. Părțile contractante:'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('A. '.$company->get('name').', în calitate de furnizor de formare profesională, denumit în continuare FURNIZOR, reprezentat prin '.$director->get('name').', având funcția de director, cu sediul în '.$company->get('address').', '.$company->get('county').', telefon '.$company->get('phone').', fax '.$company->get('fax').', e-mail '.$company->get('email').', cod fiscal/cod unic de înregistrare '.$company->get('uin').', cont bancar '.$company->get('account').' deschis la '.$company->get('bank').', posesor al autorizației de furnizor de formare profesională pentru ocupația '.$product->get('current anc name').', seria ' .wp_crm_extract_series($product->get('current anc auth')). ' numarul '.str_pad(wp_crm_extract_number($product->get('current anc auth')), 7, 0, STR_PAD_LEFT).', înmatriculat în Registrul National al Furnizorilor de Formare Profesională a adulților cu nr. '.$product->get('current rnffpa')), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('B. '.wp_crm_fill($person->get('last_name')).' '.wp_crm_fill($person->get('first_name')).', în calitate de beneficiar de formare profesională, denumit în continuare BENEFICIAR, cu domiciliul în '.wp_crm_fill($person->get('county')).', '.wp_crm_fill($person->get('address'),10).', telefon '.wp_crm_fill($person->get('phone')).'.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('2. Obiectul contractului:'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Obiectul contractului îl constituie prestarea de către furnizor a serviciului de formare profesională, pentru ocupația de '.$product->get('current anc name').'.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('3. Durata contractului:'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Durata contractului este de '.$months.' '.($months != 1 ? 'luni' : 'lună').', reprezentând '.$product->get('current theory').' ore de pregătire teoretica și '.($product->get('current hours') - $product->get('current theory')).' ore de pregătire practica; derularea contractului începe la data de '.date('d/m/Y', $cnfpa_begin).'.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('4. Obligațiile părților:'), 0, 1);
		$pdf->style ();
		$pdf->Cell (0, $h, $pdf->fix('A) Furnizorul se obliga:'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('a) să presteze serviciile de formare profesională, cu respectarea normelor legale și a metodologiilor în materie, punând accent pe calitatea formării profesionale;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('b) să asigure resursele umane, materiale, tehnice sau alte asemenenea, necesare desfășurării activității de formare profesională;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('c) să asigure finalizare procesului de formare profesională și susținerea examenelor de absolvire la terminarea stagiilor de pregătire teoretică și practică;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('d) să asigure instructajul privind protecția muncii;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('e) să nu impună beneficiarului să participe la alte activități decât cele prevăzute în programul de formare profesională.'), 0, 1);
		$pdf->Cell (0, $h, $pdf->fix('B) Beneficiarul se obligă:'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('a) să frecventeze programul de formare profesională pe întreaga perioadă. Înregistrarea a mai mult de 10% absențe nemotivate sau 25% absențe motivate din durata totală a programului conduce la pierderea dreptului beneficiarului de a susține examenul de absolvire;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('b) să utilizeze resursele materiale, tehnice și alte asemenea, potrivit scopului și destinației acestora și numai în cadrul procesului de formare profesională, evitând degradarea, deteriorarea sau distrugerea acestora;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('c) să păstreze ordinea, curățenia și disciplina pe parcursul frecventării cursurilor de formare profesională;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('d) să respecte normele privind protecția muncii;'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('e) să pună la dispoziția furnizorului toate documentele solicitate de acesta până la susținerea cursului, în caz contrar beneficiarul pierzând dreptul de a susține examenul de absolvire.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('5. Răspunderea contractuală:'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Pentru nerespectarea clauzelor prezentului contract, pentru neexecutarea sau executarea necorespunzătoare a contractului partea vinovată răspunde potrivit legii.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('6. Forța majoră'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Forța majoră exonerează părțile de răspundere în cazul în care aceasta este dovedită în condițiile legii.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('7. Soluționarea litigiilor'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Părțile contractante vor depune toate diligențele pentru rezolvare pe cale amiabilă a neînțelegerilor ce se pot ivi.'), 0, 1);
		$pdf->MultiCell (0, $h, $pdf->fix('Dacă rezolvarea pe cale amiabilă nu este posibilă, părțile se pot adresa instanței de judecată competentă, potrivit legii.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('8. Modificarea, suspedarea și încetarea contractului'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Contractul poate fi modificat numai prin acordul de voință al părților, exprimat prin act adițional la prezentul contract.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('9. Clauze speciale'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Prezentul contract se completeaza cu prevederile termenilor și conditiilor generale prezente pe site-ul www.traininguri.ro în momentul încheierii.'), 0, 1);
		$pdf->style ('strong');
		$pdf->Cell (0, $h, $pdf->fix('10. Dispoziții finale'), 0, 1);
		$pdf->style ();
		$pdf->MultiCell (0, $h, $pdf->fix('Prezentul contract reprezintă acordul de voință al părților și a fost încheiat astăzi, '.date('d/m/Y', $cnfpa_begin).' în două exemplare, din care unul pentru fiecare parte.'), 0, 1);

		$pdf->style ('strong');
		$pdf->Cell (95, $h, $pdf->fix('Furnizor,'), 0, 0, 'C');
		$pdf->Cell (0, $h, $pdf->fix('Beneficiar,'), 0, 1, 'C');
		$pdf->Cell (95, $h, $pdf->fix('...........................'), 0, 0, 'C');
		$pdf->Cell (0, $h, $pdf->fix('...........................'), 0, 1, 'C');
		$pdf->style ();

		$pdf->AddPage ();
		}

	#pagina 2 - cereri
	$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);

	$pdf->SetY (40);
	$pdf->style ('h2');
	$pdf->Cell (0, $h, $pdf->fix('CERERE ÎNSCRIERE CURS'), 0, 1, 'C');
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->style ('cnfpa');
	$pdf->Cell (0, $h, $pdf->fix('Stimate Domnule Director,'), 0, 1);
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->MultiCell (0, $h, $pdf->fix(($person->get('gender') == 'M' ? 'Subsemnatul' : 'Subsemnata').' '.wp_crm_fill($person->get('last_name')).' '.wp_crm_fill($person->get('first_name')).', posesor al BI/CI seria '.wp_crm_fill($person->get('id_series'),2).' număr '.wp_crm_fill($person->get('id_number'),6).', '.($person->get('gender') == 'M' ? 'fiul' : 'fiica').' lui '.wp_crm_fill($person->get('id_father'),10).' si al '.wp_crm_fill($person->get('id_mother'),10).', rog a-mi aproba înscrierea la programul de formare profesională - specializare pentru ocupația '.$product->get('current anc name').', cod COR '.$product->get('current corno').', organizat de către compania '.$company->get('name').' în perioada '.$cnfpa_interval.'.'), 0, 1);
	$pdf->SetY ($pdf->GetY() + $h);
	$pdf->MultiCell (0, $h, $pdf->fix('Mentionez că am fost '.($person->get('gender') == 'M' ? 'informat' : 'informată').' privind condițiile de înscriere la curs conform legislației în vigoare.'), 0, 1);
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->Cell (95, $h, $pdf->fix('Data:'), 0, 0, 'C');
	$pdf->Cell (0, $h, $pdf->fix('Semnătura:'), 0, 1, 'C');
	$pdf->Cell (95, $h, date('d/m/Y', $cnfpa_begin), 0, 1, 'C');

	$pdf->SetY (149 + 2*$h);
	$pdf->style ('h2');
	$pdf->Cell (0, $h, $pdf->fix('CERERE SUSȚINERE EXAMEN'), 0, 1, 'C');
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->style ('cnfpa');
	$pdf->Cell (0, $h, $pdf->fix('Stimate Domnule Director,'), 0, 1);
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->MultiCell (0, $h, $pdf->fix(($person->get('gender') == 'M' ? 'Subsemnatul' : 'Subsemnata').wp_crm_fill($person->get('last_name')).' '.wp_crm_fill($person->get('first_name')).', posesor al BI/CI seria '.wp_crm_fill($person->get('id_series'), 2).' număr '.wp_crm_fill($person->get('id_number'), 6).', '.($person->get('gender') == 'M' ? 'fiul' : 'fiica').' lui '.wp_crm_fill($person->get('id_father'), 10).' și al '.wp_crm_fill($person->get('id_mother'), 10).', rog a-mi aproba înscrierea pentru susținerea examenului de absolvire, ca urmare a finalizării programului de formare profesională - specializare pentru ocupația '.$product->get('current anc name').', cod COR '.$product->get('current corno').', pe care l-am urmat la compania '.$company->get('name').' în perioada '.$cnfpa_interval.'.'), 0, 1);
	$pdf->SetY ($pdf->GetY() + 2*$h);
	$pdf->Cell (95, $h, $pdf->fix('Data:'), 0, 0, 'C');
	$pdf->Cell (0, $h, $pdf->fix('Semnătura:'), 0, 1, 'C');
	$pdf->Cell (95, $h, date('d/m/Y', $cnfpa_begin), 0, 1, 'C');

	$c++;
	}

/*#pagina 3 = adeverinta
$pdf->AddPage ();
$pdf->Image(dirname(__FILE__).'/portrait.png', 0, 0, 210, 297);

$pdf->SetY (40);
$pdf->Cell (0, $h, $pdf->fix('ADEVERINȚĂ'), 0, 1, 'C');
$pdf->SetY ($pdf->GetY() + 2*$h);
$pdf->MultiCell (0, $h, $pdf->fix('Prin prezenta se adeverește că '.($person->get('gender') == 'M' ? 'domnul' : 'doamna').' '.$person->get('last_name').' '.$person->get('first_name').' având CNP '.$person->get('uin').', a absolvit cursul de '.$product->get('current anc name').', cod COR '.$product->get('current corno').', urmând a i se elibera un certificat de absolvire.'), 0, 1);
$pdf->SetY ($pdf->GetY() + $h);
$pdf->MultiCell (0, $h, $pdf->fix('Se eliberează prezenta pentru a-i servi la nevoie și este valabilă până la eliberarea certificatului.'), 0, 1);
$pdf->SetY ($pdf->GetY() + $h);
$pdf->MultiCell (0, $h, $pdf->fix($company->get('name').' este companie autorizată de Ministerul Muncii, Familiei și Egalității de Șanse și Ministerul Educației, Cercetării, Tineretului și Sportului să organizeze programul de formare profesională pentru ocupația de formator prin autorizația '.$product->get('current anc auth').'.'));
$pdf->SetY ($pdf->GetY() + 2*$h);
$pdf->Cell (0, $h, $pdf->fix('Director'), 0, 1, 'R');
$pdf->Cell (0, $h, $director->get('name'), 0, 1, 'R');*/

$pdf->out ();
?>
