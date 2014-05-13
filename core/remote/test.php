<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
#include (dirname(__FILE__).'/card/mobilpay.php');

spl_autoload_register (function ($class) {
	include (dirname(dirname(__FILE__)) . '/class/' . strtolower($class) . '.php');
	});

$message = new WP_CRM_Template (1);

$message->set ('content', '{buyer.title} {buyer.name},
<br /><br />
In primul rand dorim sa va multumim pentru ca ati ales serviciile noastre.
<br /><br />
Va transmitem in <strong>atasament factura proforma</strong> aferenta serviciilor pe care le-ati achizitionat, rugandu-va totodata sa o achitati in termenul mentionat pe site in pagina unde v-ati inscris!
<br /><br />
Detaliile de plata sunt mentionate pe a doua pagina a facturii atasate. In masura in care aveti vreo nelamurire, suntem la dispozitia dumneavoastra - datele noastre de contact fiind disponibile mai jos.
<br /><br />
Echipa Extreme Training va doreste o zi excelenta!
<br /><br />
Pentru orice intrebare sau nelamurire contactati coordonatorul programului <strong>Personal Power</strong>:<br />
Madalina Roman - email : madalina@traininguri.ro / Mob.  +40 737 511 511 / Tel: +40 314 25 25 34 / Fax: +40 372 874 354
<br /><br />
<strong>Iti doresc o zi plină de Putere Personala!</strong><br />
<strong>Marian Rujoiu</strong><br />
marian@traininguri.ro
<br /><br />
Extreme Training - Imposibilul devine posibil!<br />
www.personal-power.ro / www.traininguri.ro<br />
Tel: +40 314 25 25 34<br />
Fax: +40 372 874 354<br />
Adresa: Str. dr. Ernest Juvara, nr. 18, etaj 1, sector 6, Bucuresti - 060104');

/*
//$message->assign ('buyer', new WP_CRM_Person(72));
//echo (string) $message;
//$mailer = new WP_CRM_Mail ($message->get ('mid'));

//$mailer->send ('bdobrica@gmail.com', $message);

//echo "\n";

/*
$h = 5;
$pdf = new PDF ();

$pdf->Image (dirname(dirname(__FILE__)).'/images/companies/1.png', 140, 10, 60, 30);

$y = $pdf->GetY();
$pdf->SetY ($y + 30);

$pdf->style ('h3');
$pdf->Cell (48, $h, 'MODALITATI DE PLATA', 'B', 0);
$pdf->Cell (70, $h, 'UNDE PUTETI PLATI?', 'B', 0);
$pdf->Cell (70, $h, 'OBSERVATII', 'B', 1);
$pdf->style ();
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->style ('strong');
$pdf->MultiCell (50, $h, 'Transfer bancar din contul curent', 0);
$pdf->Image (dirname(__FILE__).'/payment/wire.png', $x + 12, $y + $h, 24, 24);
$pdf->style ();
$pdf->SetXY ($x + 48, $y);
$pdf->MultiCell (70, $h, 'Credit Europe Bank, sucursala Cotroceni Park
IBAN: RO52 FNNB 0127 0295 9576 RO01', 0);
$pdf->SetXY ($x + 118, $y);
$pdf->MultiCell (70, $h, 'Atat persoanele fizice cat si persoanele juridice care au internet banking pot achita direct din contul curent in contul Extreme Training.', 0);
$y = $pdf->GetY();
$y += 3 * $h;
$pdf->SetY ($y);
$pdf->Cell (0, $h, '', 'T');
$pdf->style ('strong');
$pdf->SetX ($x);
$pdf->MultiCell (50, $h, 'Cu cardul', 0);
$pdf->Image (dirname(__FILE__).'/payment/card.png', $x + 12, $y + $h, 24, 24);
$pdf->style ();
$pdf->SetXY ($x + 48, $y);
$pdf->multicell (70, $h, 'sediul extreme training
strada', 0);
$pdf->setxy ($x + 118, $y);
$pdf->multicell (70, $h, 'atat persoanele fizice cat si persoanele juridice care au internet banking pot achita direct din contul curent in contul extreme training.', 0);
$y = $pdf->GetY();
$y += 3 * $h;
$pdf->SetY ($y);
$pdf->Cell (0, $h, '', 'T');
$pdf->SetX ($x);
$pdf->style ('strong');
$pdf->MultiCell (50, $h, 'La banca', 0);
$pdf->Image (dirname(__FILE__).'/payment/bank.png', $x + 12, $y + $h, 24, 24);
$pdf->style ();
$pdf->SetXY ($x + 48, $y);
$pdf->MultiCell (70, $h, 'Credit Europe Bank, sucursala Cotroceni Park
IBAN: RO52 FNNB 0127 0295 9576 RO01', 0);
$pdf->SetXY ($x + 118, $y);
$pdf->MultiCell (70, $h, 'Atat persoanele fizice cat si persoanele juridice care au internet banking pot achita direct din contul curent in contul Extreme Training.', 0);
$y = $pdf->GetY();
$y += 3 * $h;
$pdf->SetY ($y);
$pdf->Cell (0, $h, '', 'T');
$pdf->SetX ($x);
$pdf->style ('strong');
$pdf->MultiCell (50, $h, 'Numerar', 0);
$pdf->Image (dirname(__FILE__).'/payment/cash.png', $x + 12, $y + $h, 24, 24);
$pdf->style ();
$pdf->SetXY ($x + 48, $y);
$pdf->MultiCell (70, $h, 'Sediul Extreme Training
IBAN: RO52 FNNB 0127 0295 9576 RO01', 0);
$pdf->SetXY ($x + 118, $y);
$pdf->MultiCell (70, $h, 'Atat persoanele fizice cat si persoanele juridice care au internet banking pot achita direct din contul curent in contul Extreme Training.', 0);

$y = $pdf->GetY();
$y += 4 * $h;
$pdf->SetY ($y);
$pdf->style ('h3');
$pdf->Cell (0, $h, 'TERMENE DE PLATA', 'B', 1);
$pdf->Style ();
$pdf->MultiCell (0, $h, 'Conform termenului menționat în pagina evenimentului/cursului la care v-ați înscris.
Neachitarea în acest termen atrage dupa sine anularea înregistrării dumneavoastră. Vă veţi putea reînscrie ulterior, însă nu putem garanta că în acel moment vor mai fi locuri disponibile. În cazul în care, din diverse motive, nu puteţi achita în termenul menţionat, vă rugam să solicitaţi o programare de plată pe un termen extins.', 0);

$y = $pdf->GetY();
$y += 2 * $h;
$pdf->SetY ($y);
$pdf->style ('h3');
$pdf->Cell (0, $h, 'TERMENI SI CONDITII', 'B', 1);
$pdf->Style ();
$pdf->MultiCell (0, $h, '1.	Taxa de participare nu include transportul sau cazarea cursanţilor pe perioada evenimentului
2.	Detaliile administrative ale evenimentului sunt afişate pe website 
3.	Înregistrarea la curs și achitarea acestei facturi vor servi ca dovadă a acceptării termenilor și condițiilor Extreme Training. Termenii şi condiţiile  de participare acceptate la inscriere sunt aici : www.personal-power.ro/termenisiconditii', 0);


$pdf->SetAutoPageBreak (FALSE);

$pdf->SetY (260);
$pdf->style ('small;strong');
$pdf->style ('color: red');
$pdf->Cell (0, $h-1, 'Extreme Training - Imposibilul devine posibil', 'T', 1, 'C');
$pdf->style ();
$pdf->style ('small');
$pdf->Cell (0, $h-1, '- Excelenta, Integritate si Respect - ', 0, 1, 'C');
$pdf->Cell (0, $h-1, 'Strada Dr. Ernest Juvara nr. 18, etaj 1, Sector 6, Bucuresti, 060104; S.C. Extreme Training Intelligence S.R.L.', 0, 1, 'C');
$pdf->Cell (0, $h-1, 'CUI: 30267884; J40/6263/31.05.2012', 0, 1, 'C');
$pdf->Cell (0, $h-1, 'E-mail: secretariat@traininguri.ro; Tel: 0314 25 25 34; Fax: 0372 874 354', 0, 1, 'C');
$pdf->Cell (0, $h-1, 'www.traininguri.ro / www.personal-power.ro', 0, 1, 'C');

$pdf = new PDF ();

$invoice = new WP_CRM_Invoice (68);

$invoice->view (FALSE, $pdf);
$pdf->AddPage ();
$invoice->back (FALSE, $pdf);


$pdf->out ();
*/
#$template = new WP_CRM_Template (1);
//$template->set ('subject', 'Factura Proforma Personal Power - Starea de Flux');
//$template->set ('content', "<ol><li>Bifeaza datele de facturare alegând persoana fizica sau juridica</li><li>Alege numarul de persoane pe care dorești sa le înscrii</li></ol>");
/*
$template = new WP_CRM_Template (array (
		'cid' => 1,
		'subject' => '',
		'content' => "Dacă deții un cupon de discount introdu-l și apasă <strong>ACTIVEAZĂ</strong> pentru recalcularea totalului!\nApasă <strong>PASUL următor</strong> pentru a trece la pasul 2 (introducerea datelor de contact ale participanților)\nApasă <strong>Continuă navigarea</strong> dacă dorești să te întorci pe site să mai adaugi și alte produse (cursuri)\nPentru a continua înscrierea trebuie să fii de acord cu termenii și condițiile de înscriere și participare."
		));
$template->save ();
echo "Template: " . $template->get () . "\n";

$template = new WP_CRM_Template (array (
		'cid' => 1,
		'subject' => '',
		'content' => "Această etapă este opțională, însă pentru ușurarea procesului administrativ (materiale utile, emiterea diplomelor, îti recomandăm să introduci minim numele, prenumele, emailul și telefonul persoanelor pe care le înscrii)."
		));
$template->save ();
echo "Template: " . $template->get () . "\n";

$template = new WP_CRM_Template (array (
		'cid' => 1,
		'subject' => '',
		'content' => "Apasă <strong>emite factura</strong> pentru a finaliza procesul de înscriere."
		));
$template->save ();
echo "Template: " . $template->get () . "\n";

$template = new WP_CRM_Template (array (
		'cid' => 1,
		'subject' => '',
		'content' => "Înscriere finalizată cu succes!<br />\nÎți mulțumim pentru alegerea făcută! Verifică inboxul adresei de email pentru a te asigura ca ai primit factura proformă. Întrucât este un proces automat verifică și folderul spam. În maxim 30 minute vei primi pe email factura proformă. Dacă nu o primești te rugăm să ne contactezi pentru a verifica și valida înscrierea dumneavoastră!<br /><br />\nÎți doresc o zi plină de putere Personală<br />\nMarian Rujoiu<br />\npower@traininguri.ro<br />\n0314 25 25 34"
		));
$template->save ();
echo "Template: " . $template->get () . "\n";*/

//$invoice = new WP_CRM_Invoice (85);
//$invoice->view (TRUE, null, TRUE);
?>
