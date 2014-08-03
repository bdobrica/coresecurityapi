<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

if (is_numeric($_GET['i'])) {
	$invoice = new WP_CRM_Invoice ($_GET['i']);
	}
else
if ($_POST['wp-crm-invoice-id']) {
	$delegate = new WP_CRM_Person ( array (
		'name' => $_POST['wp-crm-buyer-person'],
		'uin' => $_POST['wp-crm-buyer-person-uin'],
		'id_series' => $_POST['wp-crm-buyer-person-id-series'],
		'id_number' => $_POST['wp-crm-buyer-person-id-number'],
		'id_issuer' => $_POST['wp-crm-buyer-person-id-issuer'],
		'id_expire' => $_POST['wp-crm-buyer-person-id-expire'],
		'email' => $_POST['wp-crm-buyer-person-email'],
		'phone' => $_POST['wp-crm-buyer-person-phone'],
		));
	$delegate->save ();

	if ($_POST['wp-crm-generate-storno']) {
		$old_invoice = new WP_CRM_Invoice (intval($_POST['wp-crm-invoice-id']));

		$invoice = $old_invoice->copy();
		$invoice->set ('real', TRUE);
		$invoice->set ('storno', TRUE);
		$invoice->save ();
		$invoice->set ('delegate', $delegate);
		$invoice->pay (array (
			'paid by' => 'bank',
			'paid details' => 'RETURNARE CASA CF. '.$old_invoice->get('invoice_series').$old_invoice->get('invoice_number'),
			'paid date' => time(),
			'paid value' => '',
			));
		}
	else {
		$invoice = new WP_CRM_Invoice (intval($_POST['wp-crm-invoice-id']));
		

		if ($_POST['wp-crm-generate-discount']) $invoice->set ('discount', true);

		$basket = wp_crm_get_invoice_products ();
		$invoice->set('basket', $basket);
		if ($_POST['wp-crm-invoice-paidby'] != 'none') {
			$paid = array (
				'paid value' => floatval($_POST['wp-crm-partial-value']),
				'paid by' => $_POST['wp-crm-invoice-paidby'],
				'paid date' => strtotime($_POST['wp-crm-invoice-paid-date']),
				'paid details' => $_POST['wp-crm-invoice-paid-details'],
				);
			$invoice->pay ($paid);
			}
		}
	}
else {
	$seller = new WP_CRM_Company ($_POST['wp-crm-seller']);

	$paid = array (
		'paidvalue' => floatval($_POST['wp-crm-partial-value']),
		'paidby' => $_POST['wp-crm-invoice-paidby'],
		'paiddate' => strtotime($_POST['wp-crm-invoice-paid-date']),
		'paiddetails' => $_POST['wp-crm-invoice-paid-details'],
		);

	$paid_amount = floatval($_POST['wp-crm-partial-value']);

	if (strlen($_POST['wp-crm-buyer-uin']) == 13) {
		$buyer = new WP_CRM_Person (array (
			'uin' => $_POST['wp-crm-buyer-uin'],
			'name' => $_POST['wp-crm-buyer-name'],
			'id_series' => $_POST['wp-crm-buyer-person-id-series'],
			'id_number' => $_POST['wp-crm-buyer-person-id-number'],
			'id_issuer' => $_POST['wp-crm-buyer-person-id-issuer'],
			'id_expire' => $_POST['wp-crm-buyer-person-id-expire'],
			'address' => $_POST['wp-crm-buyer-address'],
			'county' => $_POST['wp-crm-buyer-county'],
			'email' => $_POST['wp-crm-buyer-person-email'],
			'phone' => $_POST['wp-crm-buyer-person-phone'],
			'stamp' => time(),
			));
		$employee = $buyer;
		}
	else {
		$buyer = new WP_CRM_Company (array (
			'uin' => $_POST['wp-crm-buyer-uin'],
			'name' => $_POST['wp-crm-buyer-name'],
			'rc' => $_POST['wp-crm-buyer-rc'],
			'address' => $_POST['wp-crm-buyer-address'],
			'county' => $_POST['wp-crm-buyer-county'],
			'account' => $_POST['wp-crm-buyer-account'],
			'bank' => $_POST['wp-crm-buyer-bank'],
			'stamp' => time(),
			));
		$employee = new WP_CRM_Person (array (
			'name' => $_POST['wp-crm-buyer-person'],
			'uin' => $_POST['wp-crm-buyer-person-uin'],
			'id_series' => $_POST['wp-crm-buyer-person-id-series'],
			'id_number' => $_POST['wp-crm-buyer-person-id-number'],
			'id_issuer' => $_POST['wp-crm-buyer-person-id-issuer'],
			'id_expire' => $_POST['wp-crm-buyer-person-id-expire'],
			'email' => $_POST['wp-crm-buyer-person-email'],
			'phone' => $_POST['wp-crm-buyer-person-phone'],
			'stamp' => time(),
			));
		$buyer->add ($employee, true);
		}


	$buyer = new WP_CRM_Buyer ($buyer);
	$basket = wp_crm_get_invoice_products ();
	$participants = wp_crm_get_invoice_participants ();

	$invoice = new WP_CRM_Invoice (array ('basket' => $basket, 'buyer' => $buyer, 'seller' => $seller, 'delegate' => $employee));

	$invoice->set('discount', $_POST['wp-crm-generate-discount'] ? TRUE : FALSE);
	$invoice->set('storno', $_POST['wp-crm-generate-storno'] ? TRUE : FALSE);

	$invoice->save();

	$participant = current($participants);
	foreach (($basket->get('products')) as $product) {
		for ($c = 0; $c<$product['quantity']; $c++) {
			if (!is_object($participant)) continue;
			$participant->register ($product, $invoice, time());
			}
		}

	if ($_POST['wp-crm-make-payment']) $invoice->pay ($paid);
	}

$invoice->view(FALSE);

$pdf_invoice_back = dirname(dirname(dirname(__FILE__))).'/cache/template/'.$invoice->seller->get('uin').'_invoice_back.pdf';
$pdf_invoice = dirname(dirname(dirname(__FILE__))).'/cache/invoices/'.$invoice->get('invoice_series').$invoice->get('invoice_number').'.pdf';
$pdf_invoice_plus = dirname(dirname(dirname(__FILE__))).'/cache/invoices/'.$invoice->get('invoice_series').$invoice->get('invoice_number').'-plus.pdf';

//if (!file_exists($pdf_invoice_plus)) {
	$cmd = "pdftk ".escapeshellarg($pdf_invoice).' '.escapeshellarg($pdf_invoice_back).' cat output '.escapeshellarg($pdf_invoice_plus);
	`$cmd`;
//	}
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="'.$invoice->get('invoice_series').$invoice->get('invoice_number').'.pdf"');
readfile ($pdf_invoice_plus);


if ($_POST['wp-crm-generate-confirmation']) {
	wp_crm_mail ('bdobrica@gmail.com', 'Confirmare plata '.$invoice->get('paid value').' lei, conform facturii '.$invoice->get('invoice_series').$invoice->get('invoice_number'), '', array ('factura.pdf' => dirname(dirname(dirname(__FILE__))).'/cache/invoices/'.$invoice->get('invoice_series').$invoice->get('invoice_number').'.pdf'));
	}
?>
