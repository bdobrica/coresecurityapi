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
$months = date('n', $cnfpa_end) - date('n', $cnfpa_end);
if ($months > 0) {
	$cnfpa_interval = date('d/m', $cnfpa_begin).' - '.date('d/m/Y', $cnfpa_end);
	}
else
	$cnfpa_interval = date('d', $cnfpa_begin).' - '.date('d/m/Y', $cnfpa_end);
$months += 1;



$c = 0;

#pagina 1 = contract
$pdf = new PDF ();

$invoices = array ();

foreach ($diplomas as $diploma) {
	$person = new WP_CRM_Person ($diploma);
	$invoice = $product->get('invoice', $person);
	if (!$invoice) continue;
	if (in_array($invoice, $invoices)) continue;

	if ($c) $pdf->AddPage();
	$invoices[] = $invoice;
	
	$invoice = new WP_CRM_Invoice($invoice);
	$pdf = $invoice->view (TRUE, $pdf);

	$c++;
	}

$pdf->out ();
?>
