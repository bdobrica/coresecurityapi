<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include (dirname(__FILE__).'/card/mobilpay.php');
session_start ();

function wp_crm_shop_display_cart ($cart, $echo = FALSE) {
	$out = '';
	if (empty($cart['p'])) {
		$out .= '<div class="wp-crm-shop-cart-wrapper">
<img src="'. WP_PLUGIN_URL .'/'. basename(dirname(dirname(dirname(__FILE__)))) . '/icons/close.png" alt="" title="" class="wp-crm-shop-cart-close" />
<h3>Pasul 1: Alege cursurile care ti se potrivesc:</h3>
<div style="text-align: justify; line-height: 105%; font-style: italic;">Bun! Nu facem cursuri de dirijat circulatia la Polul Nord si nici despre cum sa mananci cu betisoarele intr-o statie orbitala. Insa facem cursuri care iti pot folosi  in <strong>viata profesionala</strong>, sau  in <strong>cea care conteaza cu adevarat</strong>. <a href="/ro/curs-tehnici-de-negociere/">Negociere</a>, <a href="/ro/curs-acreditat-cnfpa-manager-vanzari/">vanzari</a>, <a href="/ro/curs-formare-de-formatori/">trainer</a>, <a href="/ro/curs-acreditat-manager-proiect/">manager de proiect</a>, <a href="/ro/manager-resurse-umane/">HR</a> sau <a href="/ro/modul-iv-curs-strategie-si-persuasiune/">leadership</a>. Foloseste meniul de mai jos pentru a alege cursul potrivit. <strong>Garantat de Extreme Training</strong>.</div>
</div>';
		if ($echo) echo $out;
		return $out;
		}

	$out .= '<div class="wp-crm-shop-cart-wrapper">
<img src="'. WP_PLUGIN_URL .'/'. basename(dirname(dirname(dirname(__FILE__)))) . '/icons/close.png" alt="" title="" class="wp-crm-shop-cart-close" />
<h3>Pasul 1: Alege cursurile care ti se potrivesc:</h3>
<p>Instructiuni de utilizare:</p>
<ul type="square">
	<li>Pentru a te inscrie la cursuri, apasa butonul <b>Inscrie-te acum!</b> aflat pe pagina cursurilor.</li>
	<li>Poti modifica in orice moment numarul de participanti pe care ii inscrii apasand in casuta din stanga numelui cursului si modificand numarul scris cu rosu. Pentru a salva modificarile facute, apasa <b>Actualizeaza</b>.</li>
	<li>Apasa <b>Actualizeaza</b> daca modifici numarul de persoane pe care doresti sa le inscrii la un anumit training (pentru a-ti reface calculele). Completand 0 (zero) la numarul de persoane, vei renunta la cursul respectiv.</li>
	<li>Apasa <b>Emite factura</b> daca doresti sa inchei pentru moment alegerea trainingurilor si sa treci la pasul urmator (emiterea facturii proforme pe e-mail).</li>
</ul>
<h3>Cursurile pe care le-ai ales:</h3>
<form action="" method="post">
<table cellspacing="0" class="wp-crm-shop-cart">
<thead>
	<tr><th>#.</th><th>Curs</th><th>Nr. Pers.</th><th>Pret (lei)</th><th>TVA (lei)</th><th>Valoare (lei)</th></tr>
</thead>
<tbody>
';
	$c = 1; $q = 0; $t = array ('p' => 0,'v' => 0,'t' => 0); $vat = FALSE;
	foreach ($cart['p'] as $p) {
		$product = new WP_CRM_Product (array (
			'series' => wp_crm_extract_series($p[0]),
			'number' => wp_crm_extract_number($p[0])
			));
		$out .= '<tr><td>'.($c++).'.</td><td>'.$product->get('name').'</td><td align="center"><input type="text" value="'.$p[1].'" name="wp-crm-cart-product-'.$product->get('current code').'" /></td><td align="center">'.$product->get('price').'</td><td align="center">'.$product->get('vat value').($product->get('vat') ? '' : '<sup>1</sup>').'</td><td align="center">'.(intval($p[1]) * $product->get('value')).'</td></tr>';
		$q += $p[1];

		if (!$product->get('vat')) $vat = TRUE;

		$t['p'] += intval($p[1]) * $product->get('price');
		$t['v'] += intval($p[1]) * $product->get('vat value');
		$t['t'] += intval($p[1]) * $product->get('value');
		}

	if (($q > 1) && (($t['t']/$q) >= WP_CRM_Discount_Limit)) {
		if ($t['v'] > 0) $d = array (-80.64, -19.36); else $d = array (-100.00, -0.00);
		$t['p'] += $q * $d[0];
		$t['v'] += $q * $d[1];
		$t['t'] += $q * ($d[0] + $d[1]);
		$out .= '<tr><td>'.($c++).'.</td><td align="center">1</td><td>Discount</td><td align="center">'.($d[0] * $q).'</td><td align="center">'.($d[1] * $q).'</td><td align="center">'.(($d[0] + $d[1]) * $q).'</td></tr>';
		}

	if ($q < 2)
		$out .= '<tr><td colspan="6">ATENTIE! Beneficiezi de o ocazie unica! Alegand inca un curs din oferta Extreme Training poti beneficia de un discount de 100 de lei. Discountul se aplica fiecarui produs din cosul tau de cumparaturi insa NU se aplica cursurilor on-line sau produselor cu o valoare mai mica sau egala cu 450 de lei si nu se cumuleaza cu discountul de client vechi.</td></tr>';
	$out .= '</tbody>
<tfoot>
	<tr><th colspan="3">Total: </th><th>'.$t['p'].' lei</th><th>'.$t['v'].' lei</th><th>'.$t['t'].' lei</th></tr>';
	$out .= '</tfoot></table>';
	$out .= '<table style="width: 100%;"><tr><td style="width: 50%; text-align: center;"><input type="button" name="" value="Actualizeaza &raquo;" class="wp-crm-shop-cart-button" rel="update" /></td><td style="text-align: center;"><input type="button" name="" value="Emite factura &raquo;" class="wp-crm-shop-cart-button" rel="buy" /></td></tr>';
	$out .= '<tr><td colspan="2"><sup>1</sup> Scutit de TVA Cf. ART. 141 alin. (1) lit. (f), <a href="http://static.anaf.ro/static/10/Anaf/Cod_fiscal_norme_2012.htm#_Toc304299935" target="_blank">Codul Fiscal 2010/ TITLUL VI/ CAP. IX</a></td></tr>';
	$out .= '</table>';
	return $out;
	}
//	if ($vat)
//	if (FALSE) {

function wp_crm_shop_display_form ($cart) {
	$out = '';
	if (empty($cart)) return $out;

	$out .= '<div class="wp-crm-shop-wrapper">
<img src="'. WP_PLUGIN_URL .'/'. basename(dirname(dirname(dirname(__FILE__)))) . '/icons/close.png" alt="" title="" class="wp-crm-shop-cart-close" />
<div>
<h2>Pasul 2: Instructiuni de facturare:</h2>
<p>
<ul style="type: square;">
	<li>Foloseste casutele de mai jos pentru a alege modul in care doresti sa iti fie emisa factura proforma. Alege <b>Persoana Fizica</b> in cazul in care doresti ca factura proforma sa fie emisa unei persoane fizice sau <b>Persoana Juridica</b> in cazul in care doresti ca factura sa fie emisa unei companii.</li>
	<li>Completeaza in campurile din sectiunea <b>Detalii facturare</b> informatiile necesare atat pentru emiterea facturii, cat si pentru a deschide o comunicare eficienta cu reprezentantii nostri.</li>
	<li>In cazul in care nu stii cine va participa la cursuri, te rugam sa mentionezi acest lucru in campul <b>Mentiuni</b>.</li>
</ul>
</p>
<form action="" method="post">
<table style="width: 100%;">
	<tr><td style="width: 50%; text-align: center;"><input checked type="radio" name="wp_crm_shop_buyer" value="person" class="wp-crm-slide-control" rel="wp-crm-slide-phy" id="wp-crm-slide-control-phy" /><label for="wp-crm-slide-control-phy">Persoana Fizica</label></td><td align="center"><input type="radio" name="wp_crm_shop_buyer" value="company" class="wp-crm-slide-control" rel="wp-crm-slide-jur"  id="wp-crm-slide-control-jur"/><label for="wp-crm-slide-control-jur">Persoana Juridica</label></td></tr>
</table>
<h3>Detalii facturare:</h3>
<div id="wp-crm-slide-phy" class="wp-crm-slide">
	<table class="wp-crm-shop-form wp-crm-shop-pink-table">
		<tr><th>Nume:</th><td><input name="wp_crm_shop_first" type="text" /></td></tr>
		<tr><th>Prenume:</th><td><input name="wp_crm_shop_last" type="text" /></td></tr>
		<tr><th>CNP:</th><td><input name="wp_crm_shop_uin" type="text" class="wp-crm-shop-uin" /></td></tr>
		<tr><th>Telefon:</th><td><input name="wp_crm_shop_phone" type="text" /></td></tr>
		<tr><th>E-Mail:</th><td><input name="wp_crm_shop_email" type="text" class="wp-crm-shop-email" /></td></tr>
	</table>
</div>
<div id="wp-crm-slide-jur" class="wp-crm-slide wp-crm-slide-colapsed">
	<table class="wp-crm-shop-form wp-crm-shop-yellow-table">
		<tr><th>Companie:</th><td><input name="wp_crm_shop_name" type="text" /></td></tr>
		<tr><th>Cod Fiscal:</th><td><input name="wp_crm_shop_uin" type="text" /></td></tr>
		<tr><th>Nr. Reg. Com.:</th><td><input name="wp_crm_shop_rc" type="text" /></td></tr>
		<tr><th>Adresa:</th><td><input name="wp_crm_shop_address" type="text" /></td></tr>
		<tr><th>Telefon:</th><td><input name="wp_crm_shop_phone" type="text" /></td></tr>
		<tr><th>E-Mail:</th><td><input name="wp_crm_shop_email" type="text" class="wp-crm-shop-email" /></td></tr>
		<tr><th>Banca:</th><td><input name="wp_crm_shop_bank" type="text" /></td></tr>
		<tr><th>Cont:</th><td><input name="wp_crm_shop_account" type="text" /></td></tr>
		<tr><th>Delegat:</th><td><input name="wp_crm_shop_delegate" type="text" /></td></tr>
		<tr><th>CNP Delegat:</th><td><input name="wp_crm_shop_delegate_uin" type="text" class="wp-crm-shop-uin" /></td></tr>
	</table>
</div>
<h3>Detalii participanti:</h3>';

	$z = 0; $x = 0;
	foreach ($cart['p'] as $p) {
		$product = new WP_CRM_Product (array (
			'series' => wp_crm_extract_series ($p[0]),
			'number' => wp_crm_extract_number ($p[0]),));

		$out .= '<table class="wp-crm-shop-form wp-crm-shop-'.(($x++)%2 ? 'green' : 'blue').'-table">
<tr><th colspan="2" style="text-align: left!important;">'.$product->get('name').'</th></tr>';
		for ($c = 0; $c<$p[1]; $c++) {
			$z ++;
			$out .= '<tr><th colspan="2" style="text-align: left!important;">Participant #'.($c+1).':</th></tr>
<tr><th>Nume si Prenume:</th><td><input name="wp_crm_shop_p_name_'.$z.'" type="text" /></td></tr>
<tr><th>CNP:</th><td><input type="text" name="wp_crm_shop_p_uin_'.$z.'" class="wp-crm-shop-uin" /></td></tr>
<tr><th>Telefon:</th><td><input type="text" name="wp_crm_shop_p_phone_'.$z.'" /></td></tr>
<tr><th>E-Mail:</th><td><input type="text" name="wp_crm_shop_p_email_'.$z.'" class="wp-crm-shop-email" /></td></tr>';
			}
		$out .= '</table>';
		}
	$out .= '<table class="wp-crm-shop-form wp-crm-shop-purple-table">
<tr><td colspan="2" align="center"><input type="checkbox" name="wp_crm_shop_old" value="1" id="wp-crm-shop-old" /><label for="wp-crm-shop-old"> Sunt client vechi sau am primit un voucher de la un prieten.</label></td></tr>
<tr><td colspan="2" align="center"><label>Alte mentiuni sau observatii pe care le consideri importante (optional):</label><br />
<textarea name="wp_crm_shop_mentions" rows="5"></textarea></td></tr>
<tr><td colspan="2" align="center"><br /><label>Cum ai aflat de cursurile Extreme Training?</label><br />';

	$hears = array (
		'google' => 'cautand pe Google',
		'facebook' => 'de pe Facebook',
		'youtube' => 'de pe Youtube',
		'radio' => 'de la radio',
		'tv' => 'de la televizor',
		'print' => 'din presa scrisa',
		'friend' => 'de la un prieten',
		'site' => 'de pe un site (completeaza mai jos)',
		);
	foreach ($hears as $key => $value)
		$out .= '<input type="radio" name="wp_crm_shop_heard" id="wp-crm-shop-heard-'.$key.'" value="'.$key.'"><label for="wp-crm-shop-heard-'.$key.'">'.$value.'</label>';

	$out .= '<br /><br /><input type="text" name="wp_crm_shop_heard_details"/><br /><br /></td></tr>
<tr><td style="text-align: center!important;">
<input type="button" name="" value="Intoarce-te la cursuri &raquo;" class="wp-crm-shop-cart-button" />
</td><td style="text-align: center!important;">
<input type="button" name="" value="Finalizeaza Inscrierea &raquo;" class="wp-crm-shop-cart-button" rel="pay" />
</td></tr>
</table>
</form>
</div>
</div>';
	if ($echo) echo $out;
	return $out;
	}

global $visitor;
$out = '';

$cart = unserialize ($_SESSION['WP_CRM_SHOP']);
if (!$cart) $cart = array ('p' => array(), 'b' => array (), 'c' => array ());

if ($_POST['a']) { # action
	if ($_POST['a'] == 'add') {
		if ($_POST['c']) {
			$product = new WP_CRM_Product (array (
				'series' => wp_crm_extract_series($_POST['c']),
				'number' => wp_crm_extract_number($_POST['c'])
				));
			
			if (empty($cart['p']))
				$cart['p'][] = array ($product->get('current code'), 1);
			else {
				$inside = FALSE;
				foreach ($cart['p'] as $key => $val) {
					if ($val[0] == $product->get('current code')) {
						$inside = TRUE;
						//$cart['p'][$key][1] ++;	# modificare ceruta de marian pentru a nu mai actualiza cosul de produse 26/09/2012
						}
					}
				if (!$inside)
					$cart['p'][] = array ($product->get('current code'), 1);
				}
			}
		$out .= wp_crm_shop_display_cart ($cart);
		}
	else
	if ($_POST['a'] == 'update') {
		$data = explode('&', $_POST['p']);
		$post = array ();
		foreach ($data as $key => $val) {
			list ($k, $v) = explode ('=', $val);
			$post[str_replace('wp-crm-cart-product-','',$k)] = $v;
			}
		$data = null;
		foreach ($cart['p'] as $key => $val) {
			if ($post[$val[0]] == 0) unset($cart['p'][$key]);
			else $cart['p'][$key][1] = $post[$val[0]];
			}
		$out .= wp_crm_shop_display_cart ($cart);
		}
	else
	if ($_POST['a'] == 'buy') {
		$out .= wp_crm_shop_display_form ($cart);
		}
	else
	if ($_POST['a'] == 'pay') {
		$log = '';
		$log .= "\n".date('Y-m-d H:i:s')." ".$_SERVER['REMOTE_ADDR']."\n";
		$data = explode('&', $_POST['p']);
		$post = array ();
		foreach ($data as $key => $val) {
			list ($k, $v) = explode ('=', $val);
			$k = str_replace('wp_crm_shop_','',$k);
			$post[$k] = $post[$k] ? $post[$k] : strtoupper(urldecode($v));
			$log .= $k . ' => ' . $post[$k] . "\n";
			}

		if (strtolower($post['buyer']) == 'person') {
			$buyer = new WP_CRM_Person (array (
				'uin' => intval($post['uin']),
				'first_name' => $post['first'],
				'last_name' => $post['last'],
				'name' => $post['last'].' '.$post['first'],
				'email' => $post['email'],
				'phone' => $post['phone'],
				));
			$buyer->save ();
			$delegate = $buyer;
			}
		else {
			$buyer = new WP_CRM_Company (array (
				'uin' => $post['uin'],
				'name' => $post['name'],
				'rc' => $post['rc'],
				'address' => $post['address'],
				'phone' => $post['phone'],
				'email' => $post['email'],
				'bank' => $post['bank'],
				'account' => $post['account'],
				));
			$buyer->save ();
			$delegate = new WP_CRM_Person (array (
				'uin' => $post['delegate_uin'],
				'name' => $post['delegate_name'],
				'phone' => $post['phone'],
				'email' => $post['email'],
				));
			$delegate->save ();
			}

		$buyer = new WP_CRM_Buyer ($buyer);
		$basket = new WP_CRM_Basket ();

		$sellers = new WP_CRM_List ('companies', array ('flags=1'));
		$invoices = array ();
		foreach (($sellers->get()) as $seller) {
			$invoice = new WP_CRM_Invoice (array (
				'seller' => $seller,
				'buyer' => $buyer,
				'basket' => $basket,
				'delegate' => $delegate,
				));
			$invoice->save();

			$invoice->set ('discount', TRUE);
			$invoice->set ('cookie', $visitor);
			$invoice->set ('ip', $_SERVER['REMOTE_ADDR']);
			$invoice->set ('source', $_COOKIE[WP_CRM_Track_Cookie]);

			$log .= 'invoice ' . $invoice->get('id') . "\n";

			$invoices[$seller->get()] = $invoice; 
			}


		$old_log = '';
		if (file_exists (dirname(__FILE__).'/log')) $old_log = file_get_contents (dirname(__FILE__).'/log');
		@file_put_contents (dirname(__FILE__).'/log', $old_log . $log);

		if (!empty($cart['p'])) {
			$z = 0;
			foreach ($cart['p'] as $p) {
				$product = new WP_CRM_Product (array (
					'series' => wp_crm_extract_series($p[0]),
					'number' => wp_crm_extract_number($p[0]),
					));
				$invoices[$product->get('current company')]->add ($product, intval($p[1]));

				for ($c = 0; $c<$p[1]; $c++) {
					$z++;
					$participant = new WP_CRM_Person (array (
						'name' => $post['p_name_'.$z],
						'uin' => $post['p_uin_'.$z],
						'email' => $post['p_email_'.$z],
						'phone' => $post['p_phone_'.$z],
						));
					$participant->save ();
					if ($post['old']) $participant->set ('paying customer', TRUE);
					$participant->register ($product, $invoices[$product->get('current company')], time());
					}
				}
			}

		foreach ($invoices as $invoice) {
			if ($invoice->is('empty')) {
				$invoice->delete('is empty');
				continue;
				}

			$responsible = get_userdata ($invoice->get('responsible'));
			$responsible = explode ("\n", $responsible->user_description);
			$responsible = new WP_CRM_Person (intval($responsible[0]));

			$mail = file_get_contents (dirname(dirname(dirname(__FILE__))).'/cache/template/'.$invoice->seller->get('uin').'_register.txt');
			
			$mail = wp_crm_template ($mail, $responsible, 'responsible');
			$mail = wp_crm_template ($mail, $delegate, 'delegate');
			$mail = wp_crm_template ($mail, $invoice->seller, 'company');
			$mail = str_replace ('{delegate.title}', $delegate->get('gender') == 'M' ? 'Stimate dl.' : 'Stimata dna.', $mail);

			list ($subject, $content) = explode ("\n", $mail, 2);
			$subject = str_replace ('SUBJECT: ', '', $subject);

			$attachments = array ();

			$invoice->view (FALSE);

			$pdf_invoice_back = dirname(dirname(dirname(__FILE__))).'/cache/template/'.$invoice->seller->get('uin').'_invoice_back.pdf';
			$pdf_invoice = dirname(dirname(dirname(__FILE__))).'/cache/invoices/'.$invoice->get('invoice_series').$invoice->get('invoice_number').'.pdf';
			$pdf_invoice_plus = dirname(dirname(dirname(__FILE__))).'/cache/invoices/'.$invoice->get('invoice_series').$invoice->get('invoice_number').'-plus.pdf';
			
			$cmd = "pdftk ".escapeshellarg($pdf_invoice).' '.escapeshellarg($pdf_invoice_back).' cat output '.escapeshellarg($pdf_invoice_plus);
			`$cmd`;

			$attachments[] = $pdf_invoice_plus;

			wp_crm_mail ($buyer->get('email'), $subject, $content, $attachments, $responsible->get('email'));

			if (defined('WP_AFFILIATE_Cookie')) {
				if (isset($_COOKIE[WP_AFFILIATE_Cookie])) {
					$afl = new WP_AFL_Affiliate ($_COOKIE[WP_AFFILIATE_Cookie]);
					if ($afl->get()) {
						$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'affiliate_log` (uid,iid,referer,address,stamp) values (%d,%d,%s,%s,%d);', array (
							$afl->get(),
							$invoice->get('id'),
							$_COOKIE[WP_CRM_Track_Cookie],
							$_SERVER['REMOTE_ADDR'],
							time()
							));
						$wpdb->query ($sql);
						$invoice->set ('affiliate', $afl->get());
						}
					}
				}
			$invoice->set ('heard', array ('from' => $post['heard'], 'details' => $post['heard_details']));

			$out .= '<div class="wp-crm-shop-cart-wrapper">
	<img src="'. WP_PLUGIN_URL .'/'. basename(dirname(dirname(dirname(__FILE__)))) . '/icons/close.png" alt="" title="" class="wp-crm-shop-cart-close" />
	<div style="text-align: justify; line-height: 105%; font-style: italic;">
	<p>Iti multumim pentru interesul acordat! Vei primi in cel mai scurt timp pe emailul furnizat factura proforma!</p>
	<br />';
//			if ($invoice->seller->get('crediteurope payment'))
//				$out .= wp_crm_crediteurope_payment ($invoice);
			if ($invoice->seller->get('mobilpay payment'))
				$out .= wp_crm_mobilpay_payment ($invoice);
			if ($invoice->seller->get('no online payment'))
				$out .= '<p>Inscrierea dumneavoastra a fost realizata cu succes! In cel mai scurt timp veti primi prin email factura proforma in baza careia puteti achita serviciile achizitionate.</p><p>Platile online sunt suspendate pana la data de 15 octombrie 2012. In acest interval puteti folosi oricare dintre celelalte metode de plata pe care le gasiti in instructiunile atasate facturii. Va multumim!</p>';
			$out .= '</div>
	</div>';
			}

		$cart = array ('p' => array(), 'b' => array (), 'c' => array ());
		}
	}
else
	$out .= wp_crm_shop_display_cart ($cart);

$_SESSION['WP_CRM_SHOP'] = serialize ($cart);

echo $out;

//wp_crm_card_payment (new WP_CRM_Invoice (638), TRUE);
?>
