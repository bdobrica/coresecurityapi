<?php
define ('WP_USE_THEMES', false);
define ('WP_CRM_Debug', TRUE);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

if (!empty($_POST))
foreach ($_POST as $key => $val) {
	if (strpos($key, 'wp_crm_invoice_discount-') !== FALSE) {
		$_POST['wp_crm_invoice_discount'] = $val;
		$_POST['wp_crm_invoice_id'] = (int) str_replace('wp_crm_invoice_discount-', '', $key);
		}
	if (strpos($key, 'wp_crm_instance_toggle-') !== FALSE) {
		$_POST['wp_crm_instance_toggle'] = $val;
		$_POST['wp_crm_instance_code'] = str_replace('wp_crm_instance_toggle-', '', $key);
		}
	}

function wp_crm_scan_email ($client, $since = null) {
	global $wpdb, $gpwd;

	$mail = strtolower($client->get('email'));
	if ($since == null) $since = strtotime('last month');
	else $since += 86400;
	
	$out = array ();
	
	foreach ($gpwd as $email => $data) {
		if (!$data['password']) continue;
		$imap = @imap_open ('{imap.gmail.com:993/imap/ssl}INBOX', $email, $data['password']);
		if ($imap === FALSE) continue;
		$msgs = imap_search ($imap, 'TEXT "'.$mail.'" SINCE "'.date('j F Y', $since).'"');
		if (!empty($msgs))
			foreach ($msgs as $msg) {
				$hdr = imap_header ($imap, $msg);

				$row = array (
					'from' => strtolower(trim($hdr->from[0]->mailbox.'@'.$hdr->from[0]->host)),
					'to' => strtolower(trim($hdr->to[0]->mailbox.'@'.$hdr->to[0]->host)),
					'title' => $hdr->subject,
					'stamp' => $hdr->udate,
					);

				$description = imap_fetchbody ($imap, $msg, '1.1');
				$description = imap_utf8 ($description);
				$row['description'] = $description;

				$initiator = 'auto';
				if ($gpwd[$row['from']]) $initiator = 'responsible';
				if ($gpwd[$row['to']]) $initiator = 'client';
				$row['initiator'] = $initiator;

				$user = 0;

				if ($initiator == 'client')
					$user = $wpdb->get_var ($wpdb->prepare('select ID from `'.$wpdb->prefix.'users` where user_email like %s;', $row['to']));
				else
					$user = $wpdb->get_var ($wpdb->prepare('select ID from `'.$wpdb->prefix.'users` where user_email like %s;', $row['from']));
				if ($user)
					$row['user'] = get_userdata($user);

				$company = null;
				$product = null;

				$event = new WP_CRM_Event (array (
					'user' => $user,
					'stamp' => $hdr->udate,
					'transport' => 'mail',
					'client' => $client,
					'company' => $company,
					'initiator' => $initiator,
					'product' => $product,
					'title' => $row['title'],
					'description' => $description,
					));

				$event->save ();
				$out[] = $row;
				}
		imap_close ($imap);
		}

	return $out;
	}

if ($_POST['wp_crm_quick_add_products']) {
	$post = array ();
	$newp = array ();

	$invoice = new WP_CRM_Invoice ((int) $_POST['wp_crm_quick_invoice']);

	foreach ($_POST as $key => $val) {
		if (strpos($key, 'wp_crm_quick_new_product_name_') !== FALSE) $newp[] = str_replace('wp_crm_quick_new_product_name_','',$key);
		if (strpos($key, 'wp_crm_quick_quantity_') !== FALSE)
			$post[str_replace('wp_crm_quick_quantity_','',$key)] = $val;
		}

	if (!empty($newp))
		foreach ($newp as $key => $val) {
			$product = new WP_CRM_Product (array (
				'name' => $_POST['wp_crm_quick_new_product_name_'.$val],
				'price' => (float) $_POST['wp_crm_quick_new_product_value_'.$val],
				'vat' => (float) $_POST['wp_crm_quick_new_product_vat_'.$val],
				));
			$quantity = (int) $_POST['wp_crm_quick_new_product_quantity_'.$val];
			if ($quantity)
				$invoice->add ($product, $quantity);
			unset ($product);
			}

	if (!empty($post))
		foreach ($post as $key => $val) {
			$series = wp_crm_extract_series ($key);
			$number = wp_crm_extract_number ($key);
			$product = new WP_CRM_Product (array (
				'series' => $series,
				'number' => $number,
				));
			$quantity = (int) $val;
			$invoice->change ($product, $quantity);
			unset ($product);
			}

	die ('OK');
	}

if ($_POST['wp_crm_quick_add_person']) {
	$person = new WP_CRM_Person (array (
		'name'		=> strtoupper($_POST['wp_crm_quick_add_person_last'].' '.$_POST['wp_crm_quick_add_person_first']),
		'first_name'	=> strtoupper($_POST['wp_crm_quick_add_person_first']),
		'last_name'	=> strtoupper($_POST['wp_crm_quick_add_person_last']),
		'email'		=> strtoupper($_POST['wp_crm_quick_add_person_email']),
		'phone'		=> strtoupper($_POST['wp_crm_quick_add_person_phone']),
		'address'	=> strtoupper($_POST['wp_crm_quick_add_person_address']),
		'county'	=> strtoupper($_POST['wp_crm_quick_add_person_county']),
		'uin'		=> intval($_POST['wp_crm_quick_add_person_uin']),
		));

	if ($person->errors() !== FALSE)
		die ("ERROR\n" . $person->errors());
	if ($person->save ()) die ('OK');
	die ("ERROR\n" . $person->errors());
	}


if ($_POST['wp_crm_quick_add_company']) {
	$company = new WP_CRM_Company (array (
		'name'		=> strtoupper($_POST['wp_crm_quick_add_company_name']),
		'rc'		=> strtoupper($_POST['wp_crm_quick_add_company_rc']),
		'uin'		=> strtoupper($_POST['wp_crm_quick_add_company_uin']),
		'address'	=> strtoupper($_POST['wp_crm_quick_add_company_address']),
		'county'	=> strtoupper($_POST['wp_crm_quick_add_company_county']),
		'email'		=> strtoupper($_POST['wp_crm_quick_add_company_email']),
		'phone'		=> strtoupper($_POST['wp_crm_quick_add_company_phone']),
		'fax'		=> strtoupper($_POST['wp_crm_quick_add_company_fax']),
		'bank'		=> strtoupper($_POST['wp_crm_quick_add_company_bank']),
		'account'	=> strtoupper($_POST['wp_crm_quick_add_company_account']),
		));

	if ($company->errors () !== FALSE)
		die ("ERROR\n" . $company->errors());
	if ($company->save ()) die ('OK');
	die ("ERROR\n" . $company->errors());
	}

if ($_POST['wp_crm_quick_add_invoice']) {
	if (wp_crm_detect_person($_POST['wp_crm_quick_add_invoice_buyer']) == 'person') {
		$buyer = new WP_CRM_Person ($_POST['wp_crm_quick_add_invoice_buyer']);
		if ($err = $buyer->errors()) die ("ERROR\n".$err);
		$buyer = new WP_CRM_Buyer ($buyer);
		}
	else {
		$buyer = new WP_CRM_Company (array ('uin' => $_POST['wp_crm_quick_add_invoice_buyer']));
		if ($err = $buyer->errors ()) die ("ERROR\n".$err);
		$buyer = new WP_CRM_Buyer ($buyer);
		}

	$delegate = new WP_CRM_Person ($_POST['wp_crm_quick_add_invoice_delegate']);
	$delegate = $delegate->errors () ? NULL : $delegate;

	$basket = new WP_CRM_Basket ();
	$seller = new WP_CRM_Company ((int) $_POST['wp_crm_quick_add_invoice_seller']);

	$invoice = new WP_CRM_Invoice (array ('seller' => $seller, 'buyer' => $buyer, 'delegate' => $delegate, 'basket' => $basket));

	$invoice->save ();
	$invoice->set ('discount', TRUE);

	die ('OK');
	}

if ($_POST['wp_crm_quick_change_date']) {
	$invoice = new WP_CRM_Invoice ((int) $_POST['wp_crm_quick_invoice']);
	$date = strtotime ($_POST['wp_crm_quick_invoice_date']);
	$invoice->set ('date', $date);
	die ('OK');
	}

if ($_POST['wp_crm_quick_change_paiddate']) {
	$invoice = new WP_CRM_Invoice ((int) $_POST['wp_crm_quick_invoice']);
	$date = strtotime ($_POST['wp_crm_quick_invoice_paiddate']);
	$invoice->set ('paid date', $date);
	die ('OK');
	}

if ($_POST['wp_crm_quick_change_buyer']) {
	$delegate = null;

	if (wp_crm_detect_person($_POST['wp_crm_quick_buyer_uin']) == 'person') {
		$buyer = new WP_CRM_Person ($_POST['wp_crm_quick_buyer_uin']);
		if ($err = $buyer->errors()) die ("ERROR\n".$err);
		$buyer = new WP_CRM_Buyer ($buyer);
		}
	else {
		$buyer = new WP_CRM_Company (array ('uin' => $_POST['wp_crm_quick_buyer_uin']));
		if ($err = $buyer->errors ()) die ("ERROR\n".$err);
		$buyer = new WP_CRM_Buyer ($buyer);
		}

	if (wp_crm_detect_person($_POST['wp_crm_quick_delegate_uin']) == 'person') {
		$delegate = new WP_CRM_Person ($_POST['wp_crm_quick_delegate_uin']);
		if ($err = $delegate->errors()) die ("ERROR\n".$err);
		}

	print_r ($delegate->get ('uin'));

	$invoice = new WP_CRM_Invoice ((int) $_POST['wp_crm_quick_invoice']);
	$invoice->set ('buyer', $buyer);
	if (is_object($delegate)) $invoice->set ('delegate', $delegate);
	die ('OK');
	}

if ($_POST['wp_crm_quick_invoice_delete']) {
	$yes = strtolower(trim($_POST['wp_crm_quick_invoice_delete']));
	if ($yes == 'da') {
		$invoice = new WP_CRM_Invoice ((int) $_POST['wp_crm_quick_invoice']);
		$invoice->delete (array (
			'reason' => $_POST['wp_crm_quick_invoice_delete_reason'],
			'details' => $_POST['wp_crm_quick_invoice_delete_details'],
			));
		die ('OK');
		}
	die ('ERROR');
	}

if ($_POST['wp_crm_quick_company_id']) {
	$company = new WP_CRM_Company((int) $_POST['wp_crm_quick_company_id']);
	$keys = $company->get('keys');
	foreach ($keys as $key) {
		$value = strtoupper(trim($_POST['wp_crm_quick_company_'.$key]));
		if ($value && ($value != $company->get($key)))
			$company->set($key, $value);
		}
	die('OK');
	}

if ($_POST['wp_crm_quick_company_delete']) {
	$yes = strtolower(trim($_POST['wp_crm_quick_company_delete']));
	if ($yes == 'da') {
		$company = new WP_CRM_Company ((int) $_POST['wp_crm_quick_company']);
		$company->delete ();
		die ('OK');
		}
	die ('ERROR');
	}

if ($_POST['wp_crm_quick_person_id']) {
	$person = new WP_CRM_Person ($_POST['wp_crm_quick_person_id']);
	$keys = $person->get('keys');
	$_POST['wp_crm_quick_person_name'] = $_POST['wp_crm_quick_person_last_name'].' '.$_POST['wp_crm_quick_person_first_name'];
	foreach ($keys as $key) {
		$value = strtoupper(trim($_POST['wp_crm_quick_person_'.$key]));
		if ($value && ($value != $person->get($key)))
			$person->set($key, $value);
		}
	die ('OK');
	}

if ($_POST['wp_crm_quick_person_delete']) {
	$yes = strtolower(trim($_POST['wp_crm_quick_person_delete']));
	if ($yes == 'da') {
		$person = new WP_CRM_Person ((int) $_POST['wp_crm_quick_person']);
		$person->delete ();
		die ('OK');
		}
	die ('ERROR');
	}

if ($_POST['wp_crm_quick_send_email']) {
	$invoice = new WP_CRM_Invoice (intval($_POST['wp_crm_quick_invoice']));

	$responsible = get_userdata ($_POST['wp_crm_quick_email_sender']);
	$responsible = explode ("\n", $responsible->user_description);
	$responsible = new WP_CRM_Person (intval($responsible[0]));

	$attachments = array ();

	$invoice->view (FALSE);

	$pdf_invoice_back = dirname(dirname(dirname(__FILE__))).'/cache/template/invoice_back.pdf';
	$pdf_invoice = dirname(dirname(dirname(__FILE__))).'/cache/invoices/'.$invoice->get('invoice_series').$invoice->get('invoice_number').'.pdf';
	$pdf_invoice_plus = dirname(dirname(dirname(__FILE__))).'/cache/invoices/'.$invoice->get('invoice_series').$invoice->get('invoice_number').'-plus.pdf';
	
	$cmd = "pdftk ".escapeshellarg($pdf_invoice).' '.escapeshellarg($pdf_invoice_back).' cat output '.escapeshellarg($pdf_invoice_plus);
	`$cmd`;

	$attachments[] = $pdf_invoice_plus;

	if ($_POST['wp_crm_quick_email_template'] == 'payments') {
		$_POST['wp_crm_quick_email_template'] = 'payment';
		if (!$invoice->is('paid')) die ('ERROR: invoice not paid');
		if ($invoice->is('partial paid')) $_POST['wp_crm_quick_email_template'] = 'partial_payment';
		}

	$mail = dirname(dirname(dirname(__FILE__))).'/cache/template/'.$invoice->seller->get('uin').'_'.$_POST['wp_crm_quick_email_template'].'.txt';
	if (!file_exists($mail)) die ('ERROR: unknown template: '.$_POST['wp_crm_quick_email_template']);


	$sent = array ();

	$receivers = $invoice->get('participants');
	if ($invoice->buyer->get('type') == 'person')
		$receivers[] = $invoice->buyer->get('entity');
	else {
		if (($_POST['wp_crm_quick_email_receiver'] == -1) || ($_POST['wp_crm_quick_email_receiver'] == $invoice->buyer->get('uin'))) {
			$mail = file_get_contents (dirname(dirname(dirname(__FILE__))).'/cache/template/'.$invoice->seller->get('uin').'_'.$_POST['wp_crm_quick_email_template'].'.txt');	
			$mail = wp_crm_template ($mail, $responsible, 'responsible');
			//$mail = wp_crm_template ($mail, $receiver, 'delegate');
			$mail = wp_crm_template ($mail, $invoice, 'invoice');
			$mail = wp_crm_template ($mail, $invoice->seller, 'company');
			$mail = str_replace ('{delegate.title}', 'Catre ', $mail);
			$mail = str_replace ('{delegate.name}', $invoice->buyer->get('name'), $mail);

			list ($subject, $content) = explode ("\n", $mail, 2);
			$subject = str_replace ('SUBJECT: ', '', $subject);

			wp_crm_mail ($invoice->buyer->get('email'), $subject, $content, $attachments, $responsible->get('email'));
			$sent[] = trim(strtolower($invoice->buyer->get('email')));
			//wp_crm_mail ('bdobrica@gmail.com', $subject, $content, $attachments, $responsible->get('email'));
			}
		}

	foreach ($receivers as $receiver) {
		if (($_POST['wp_crm_quick_email_receiver'] != -1) && ($_POST['wp_crm_quick_email_receiver'] != $receiver->get('uin'))) continue;

		$email = trim(strtolower($receiver->get('email')));
		if (in_array ($email, $sent)) continue;		# prevent send 

		$mail = file_get_contents (dirname(dirname(dirname(__FILE__))).'/cache/template/'.$invoice->seller->get('uin').'_'.$_POST['wp_crm_quick_email_template'].'.txt');	
		$mail = wp_crm_template ($mail, $responsible, 'responsible');
		$mail = wp_crm_template ($mail, $receiver, 'delegate');
		$mail = wp_crm_template ($mail, $invoice, 'invoice');
		$mail = wp_crm_template ($mail, $invoice->seller, 'company');
		$mail = str_replace ('{delegate.title}', $receiver->get('gender') == 'M' ? 'Stimate dl.' : 'Stimata dna.', $mail);

		list ($subject, $content) = explode ("\n", $mail, 2);
		$subject = str_replace ('SUBJECT: ', '', $subject);

		wp_crm_mail ($receiver->get('email'), $subject, $content, $attachments, $responsible->get('email'));
		//wp_crm_mail ('bdobrica@gmail.com', $subject, $content, $attachments, $responsible->get('email'));
		}

	
	die ('OK');
	}

if ($_POST['wp_crm_quick_participants']) {
	$invoice = new WP_CRM_Invoice ($_POST['wp_crm_quick_invoice']);
	$products = $invoice->get();
	if (!empty($products))
		foreach ($products as $product) {
			for ($c = 0; $c < $product['quantity']; ) {
				$c++;
				$participant = new WP_CRM_Person (intval($_POST['wp_crm_quick_'.$product['product']->get('current code').'_'.$c]));
				$product['product']->add ($participant, $invoice);
				}
			}
	die ('OK');
	}

if ($_POST['wp_crm_search_person']) {
	$out = '';
	$persons = new WP_CRM_List ('persons', array ('text' => $_POST['wp_crm_search_person']));
	if (!$persons->is('empty')) {
		$out .= '<table class="widefat">';
		foreach (($persons->get()) as $person)
			$out .= '<tr><td>'.$person->get('name').'</td><td>'.$person->get('uin').'</td></tr>';
		$out .= '</table>';
		}
	echo $out;
	}

if ($_POST['wp_crm_search_company']) {
	$out = '';
	$companies = new WP_CRM_List ('companies', array ('text' => $_POST['wp_crm_search_company']));
	if (!$companies->is('empty')) {
		$out .= '<table class="widefat">';
		foreach (($companies->get()) as $company)
			$out .= '<tr><td>'.$company->get('name').'</td><td>'.$company->get('uin').'</td></tr>';
		$out .= '</table>';
		}
	echo $out;
	}

if ($_POST['wp_crm_event_add']) {
	if ($_POST['wp_crm_event_client_new']) {
		}
	else {
		print_r($_POST);
		if (is_numeric($_POST['wp_crm_event_client']) && strlen($_POST['wp_crm_event_client']) == 13) {
			$client = new WP_CRM_Person (intval($_POST['wp_crm_event_client']));
			if ($client->errors() !== FALSE)
				die ('ERROR: '.$client->errors());
			}
		else
		if (preg_match ('/^[a-z0-9_.]+\@[a-z0-9.-]+\.[a-z]{2,4}$/', strtolower($_POST['wp_crm_event_client']))) {
			$persons = new WP_CRM_List ('persons', array ("email like '".strtoupper($_POST['wp_crm_event_client'])."'"));
			if ($persons->is('empty')) die ('ERROR: client missing. invalid email address');
			$client = current($persons->get());
			}
		else
			die ('ERROR: client missing. invalid name');
		}
	
	if ($_POST['wp_crm_event_product'])
		$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_event_product']), 'number' => wp_crm_extract_number ($_POST['wp_crm_event_product'])));

	$event = new WP_CRM_Event (array (
		'client' => $client,
		'company' => null,
		'product' => is_object($product) ? $product : null,
		'initiator' => $_POST['wp_crm_event_trigger'] < 0 ? 'client' : 'responsible',
		'title' => $_POST['wp_crm_event_title'],
		'description' => $_POST['wp_crm_event_description'],
		));

	$event->save ();
	}

if ($_POST['wp_crm_event_history']) {
	$out = '';
	$client = new WP_CRM_Person($_POST['wp_crm_event_client']);
	if ($client->errors() !== FALSE)
		die ('ERROR: '.$client->errors());

	$rows = array ();

	$events = new WP_CRM_List ('events', array ('client' => $client));
	$since = 0;
	if (!$events->is ('empty')) {
		$events->sort ('time', 'desc');
		foreach (($events->get()) as $event) {
			if ($event->get('initiator') == 'client') {
				$from = is_object($event->get('client')) ? strtolower($event->get('client')->get('email')) : '';
				$to = $event->get('user')->user_email;
				}
			else {
				$from = $event->get('user')->user_email;
				$to = is_object($event->get('client')) ? strtolower($event->get('client')->get('email')) : '';
				}

			$rows[] = array (
				'<img src="' . WP_CRM_URL . '/icons/email.png" alt="" title="" class="wp-crm-popup-control" /><div class="wp-crm-popup"><div style="background: #fff; border: 1px solid #ccc; border-radius: 3px; padding: 10px;">'.nl2br($event->get('content')).'</div></div>',
				date ('d-m-Y', $event->get('time')),
				date ('H:i', $event->get('time')),
				$event->get('initiator'),
				$event->get('user')->display_name,
				$event->get('subject'),
				);
			if ($since < $event->get('time')) $since = $event->get('time');
			}
		}

	$msgs = wp_crm_scan_email ($client, $since ? $since : null);
	if (!empty($msgs)) {
		foreach ($msgs as $msg)
			$rows[] = array (
				'<img src="' . WP_CRM_URL . '/icons/email.png" alt="" title="" class="wp-crm-popup-control" /><div class="wp-crm-popup"><div style="background: #fff; border: 1px solid #ccc; border-radius: 3px; padding: 10px;">'.nl2br($msg['description']).'</div></div>',
				date ('d-m-Y', $msg['stamp']),
				date ('H:i', $msg['stamp']),
				$msg['initiator'],
				$msg['user']->display_name,
				$msg['title'],
				);
		}

	$out .= wp_crm_display_table (array ('#', 'Data', 'Time', 'From', 'User', 'Subject'), $rows, array ('class' => 'widefat nofooter'));
	echo $out;
	exit (1);	
	}

if ($_POST['wp_crm_resource_add']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_resource_product']), 'number' => wp_crm_extract_number ($_POST['wp_crm_resource_product'])));
	$value = (float) $_POST['wp_crm_resource_value'];
	$fees = 0.0;
	if ($_POST['wp_crm_resource_fees'] == 'vat') $fees = $value * 0.24;
	if ($_POST['wp_crm_resource_fees'] == 'trainer') $fees = $value * 0.5344;
	$data = array (
		'product' => is_object($product) ? $product : null,
		'title' => $_POST['wp_crm_resource_title'],
		'description' => $_POST['wp_crm_resource_description'],
		'type' => $_POST['wp_crm_resource_type'],
		'value' => $value,
		'fees' => $fees,
		'global' => $_POST['wp_crm_resource_global'] == 'yes' ? TRUE : FALSE
		);
	$resource = new WP_CRM_Resource ($data);
	$resource->save ();
	}

if ($_POST['wp_crm_instance_participant_add']) {
	$invoice = new WP_CRM_Invoice ($_POST['wp_crm_instance_participant_invoice']);
	$participant = new WP_CRM_Person ($_POST['wp_crm_instance_participant_uin']);
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_instance_product']), 'number' => wp_crm_extract_number ($_POST['wp_crm_instance_product'])));

	$product->add ($participant, $invoice);
	}

if ($_POST['wp_crm_quick_structure']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_quick_structure_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_quick_structure_instance'])));
	$struct = 0;
	for ($d = 0; $d<34; $d++)
		$struct |= $_POST['wp_crm_quick_structure_'.$d] ? ( 1 << $d ) : 0;
	$product->set ('structure', $struct);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_name_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$begin = strtotime($_POST['wp_crm_product_instance_begin']);
	$end = strtotime($_POST['wp_crm_product_instance_end']);

	if (date('m', $begin) != date('m', $end))
		$interval = date('j F', $begin) . ' - ' . date('j F Y', $end);
	else {
		if (date('j', $begin) == date('j', $end))
			$interval = date('j F Y', $end);
		else
			$interval = date('j F', $begin) . ' - ' . date('j F Y', $end);
		}

	$name = $_POST['wp_crm_product_instance_name'];
	$name = wp_crm_product_types ($_POST['wp_crm_product_instance_type'], 'value').$name;
	$name .= ' '.wp_crm_product_cities ($_POST['wp_crm_product_instance_city'], 'value');
	if ($_POST['wp_crm_product_instance_package'])
		$name .= ' - ' . $_POST['wp_crm_product_instance_package'];
	$name .= ' (' . wp_crm_date($interval) . ')';

	$product->set ('instance name', $name);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_location_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->set ('location', $_POST['wp_crm_product_instance_location']);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_trainer_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->set ('trainer', $_POST['wp_crm_product_instance_trainer']);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_responsible_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->set ('responsible', $_POST['wp_crm_product_instance_responsible']);
	die ('OK');
	}

if ($_POST['wp_crm_quick_instance_prices_change']) {
	$price = round((float) str_replace(',','.',trim($_POST['wp_crm_product_instance_price'])),2);
	$full = round((float) str_replace(',','.',trim($_POST['wp_crm_product_instance_full'])),2);
	$vat = round((float) str_replace(',','.',trim($_POST['wp_crm_product_instance_vat'])),2);
	$stamp = strtotime($_POST['wp_crm_product_instance_stamp']);
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_quick_instance_price']), 'number' => wp_crm_extract_number ($_POST['wp_crm_quick_instance_price'])));
	$product->set ('price', array (
		'price' => $price,
		'full' => $full,
		'vat' => $vat,
		'stamp' => $stamp,
		));

	die ('OK');
	}

if ($_POST['wp_crm_product_instance_price_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->set ('price', (float) $_POST['wp_crm_product_instance_price']);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_vat_change']) {
	print_r($_POST);
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->set ('vat', (float) $_POST['wp_crm_product_instance_vat']);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_fullprice_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->set ('full price', (float) $_POST['wp_crm_product_instance_fullprice']);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_date_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->modify($_POST['wp_crm_product_instance'], $_POST['wp_crm_product_instance_date']);
	die ('OK');
	}

if ($_POST['wp_crm_product_instance_company_change']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_product_instance']), 'number' => wp_crm_extract_number ($_POST['wp_crm_product_instance'])));
	$product->set('company', (int) $_POST['wp_crm_product_instance_company']);
	die ('OK');
	}

if (isset($_POST['wp_crm_invoice_discount']) && $_POST['wp_crm_invoice_id']) {
	$invoice = new WP_CRM_Invoice ($_POST['wp_crm_invoice_id']);
	$invoice->set ('discount', $_POST['wp_crm_invoice_discount'] ? TRUE : FALSE);
	die ('OK');
	}

if (isset($_POST['wp_crm_instance_toggle']) && $_POST['wp_crm_instance_code']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_instance_code']), 'number' => wp_crm_extract_number ($_POST['wp_crm_instance_code'])));
	if ($_POST['wp_crm_instance_toggle'])
		$product->activate($_POST['wp_crm_instance_code']);
	else
		$product->deactivate($_POST['wp_crm_instance_code']);
	die ('OK');
	}

if ($_POST['wp_crm_quick_instance_cnfpa']) {
	$product = new WP_CRM_Product (array ('series' => wp_crm_extract_series ($_POST['wp_crm_quick_instance_cnfpa']), 'number' => wp_crm_extract_number ($_POST['wp_crm_quick_instance_cnfpa'])));
	$product->set('hours', (int) $_POST['wp_crm_product_instance_hours']);
	$product->set('theory', trim($_POST['wp_crm_product_instance_theory']));
	$product->set('corno', (int) $_POST['wp_crm_product_instance_corno']);
	$product->set('ancauth', strtoupper(trim($_POST['wp_crm_product_instance_ancauth'])));
	$product->set('ancname', strtoupper(trim($_POST['wp_crm_product_instance_ancname'])));
	$product->set('rnffpa', trim($_POST['wp_crm_product_instance_rnffpa']));
	$product->set('ancrep', trim($_POST['wp_crm_product_instance_ancrep']));
	$product->set('studies', trim($_POST['wp_crm_product_instance_studies']));
	$product->set('competences', trim($_POST['wp_crm_product_instance_competences']));
	die ('OK');
	}

if ($_POST['wp_crm_quick_add_task']) {
	$wp_crm_tm_task = new WP_CRM_TM_Task (array (
		'title' => $_POST['wp_crm_quick_add_task_title'],
		'description' => $_POST['wp_crm_quick_add_task_description'],
		'deadline' => $_POST['wp_crm_quick_add_task_deadline'],
		'responsible' => $_POST['wp_crm_quick_add_task_responsible'],
		'importance' => $_POST['wp_crm_quick_add_task_importance'],
		'urgency' => $_POST['wp_crm_quick_add_task_urgency'],
		));
	if ($wp_crm_tm_task->save()) die ('OK');
	die ('ERROR');
	}

if ($_POST['wp_crm_quick_add_product_instance']) {
	$wp_crm_product = new WP_CRM_Product ((int) $_POST['wp_crm_quick_add_product_instance']);
	$wp_crm_product->plan ($wp_crm_product->get('current series'), (int) strtotime($_POST['wp_crm_quick_add_product_instance_date']));
	die ('OK');
	}

if ($_POST['wp_crm_quick_add_new_product']) {
	$wp_crm_product = new WP_CRM_Product (array (
		'post id' => (int) $_POST['wp_crm_quick_add_new_product_pid'],
		'series' => trim($_POST['wp_crm_quick_add_new_product_series']),
		));
	die ('OK');
	}

if ($_POST['wp_crm_quick_client_voucher']) {
	$wp_crm_client = new WP_CRM_Client (array (
		'voucher' => $_POST['wp_crm_quick_client_voucher']
		));
	$wp_crm_client->set ('cnfpa', strtoupper(trim($_POST['wp_crm_quick_client_edit_series'])).str_pad($_POST['wp_crm_quick_client_edit_number'], 8, '0', STR_PAD_LEFT));
	$wp_crm_client->set ('grade', (float) $_POST['wp_crm_quick_client_edit_grade']);
	$wp_crm_client->set ('diploma', (float) $_POST['wp_crm_quick_client_edit_diploma']);
	die ('OK');
	}

print_r($_POST);
?>
