<?php
class WP_CRM {
	public static function buy ($data = null) {
		global
			$wpdb,
			$wp_crm_state,
			$wp_crm_buyer,
			$wp_crm_cookie;

		$data = empty($data) ?
				$wp_crm_state->get ('data') :
				array_merge ((array) $wp_crm_state->get ('data'), (array) $data);
		$basket = $wp_crm_state->get ('basket');
		$basket->save ();

		$wp_crm_state->delete ();

		if ($data['tabs'] == 'persoana-fizica') {
			$buyer = new WP_CRM_Person (array (
				'first_name'	=> $data['p_first_name'],
				'last_name'	=> $data['p_last_name'],
				'uin'		=> $data['p_uin'],
				'email'		=> $data['p_email'],
				'phone'		=> $data['p_phone']
				));
			$buyer->save ();

			$delegate = $buyer;
			}
		else {
			$delegate = new WP_CRM_Person (array (
				'first_name'	=> $data['d_first_name'],
				'last_name'	=> $data['d_last_name']
				));
			$delegate->save ();

			$buyer = new WP_CRM_Company (array (
				'name'		=> $data['c_name'],
				'uin'		=> $data['c_uin'],
				'rc'		=> $data['c_rc'],
				'address'	=> $data['c_address'],
				'county'	=> $data['c_county'],
				'bank'		=> $data['c_bank'],
				'account'	=> $data['c_account'],
				'email'		=> $data['c_email'],
				'phone'		=> $daca['c_phone']
				));
			$buyer->save ();
			}

		$invoice = new WP_CRM_Invoice ();

		$invoice->set ('stamp', time ());
		$invoice->set ('buyer', $buyer);
		if ($data['coupon'])
			$invoice->set ('coupon', strtoupper(trim($data['coupon'])));
		if (is_object ($delegate))
			$invoice->set ('did', $delegate->get ());
		$invoice->set ('source', $wp_crm_cookie->get ());

		$invoice_ids = $invoice->save ();
		$invoices = array ();

		if (!empty($invoice_ids))
			foreach ($invoice_ids as $invoice_id) {
				$wp_crm_invoice = new WP_CRM_Invoice ($invoice_id);
				/*
				TODO: should be included in WP_CRM_Invoice::save for speed reasons
				*/
				$wp_crm_invoice->set ('uid', $wp_crm_invoice->seller->get ('uid'));
				$wp_crm_invoice->set ('oid', $wp_crm_invoice->seller->get ('oid'));

				if (!is_object($wp_crm_invoice->buyer)) continue;

				$invoices[$wp_crm_invoice->seller->get ()] = $wp_crm_invoice;

				$attachment = $wp_crm_invoice->view (FALSE, null, TRUE);

				$message = new WP_CRM_Template ($wp_crm_invoice->seller->get ('register'));
				$message->assign ('buyer', $wp_crm_invoice->buyer);
				$mailer = new WP_CRM_Mail ($message->get ('mid'));

				$mailer->send ($wp_crm_invoice->buyer->get ('email'), $message, $attachment);
				}

		foreach ($basket->get ('products') as $product => $quantity) {
			$slug = strtolower ($product);
			$wp_crm_product = new WP_CRM_Product ($product);

			for ($q = 1; $q <= $quantity; $q++) {
				$participant = new WP_CRM_Person (array (
					'first_name'	=> $data[$slug . '_' . $q . '_first_name'],
					'last_name'	=> $data[$slug . '_' . $q . '_last_name'],
					'email'		=> $data[$slug . '_' . $q . '_email'],
					'phone'		=> $data[$slug . '_' . $q . '_phone'],
					'uin'		=> $data[$slug . '_' . $q . '_uin'],
					'stamp'		=> time ()
					));
				$participant->save ();

				$client = new WP_CRM_Client ($participant);
				$client->register ($wp_crm_product, $invoices[$wp_crm_product->get ('cid')]);

				unset ($participant);
				unset ($client);
				}
			unset ($wp_crm_product);
			}
		}

	public static function save ($data = null) {
		global
			$wp_crm_garbage, /** should be removed, replaced by helper */
			$wp_crm_contact, /** should be removed, replaced by helper */
			$wp_crm_helper;

		$req = $_GET['object'] ? $_GET['object'] : $_POST['object'];

		list ($req_o, $filter) = explode (';', $req);
		list ($class, $id) = explode ('-', $req_o);

		//print_r ($req);

		if (!class_exists ($class)) die ('Err.1');
		if (!is_numeric($id)) die ('Err.2');

		$object = $id ? new $class ((int) $id) : new $class ();

		if (is_object ($wp_crm_helper)) {
			try {
				$wp_crm_helper->save ($data);
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				}
			}
		else {
			$object->set ($data);
			try {
				$object->save ();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				//print_r ($wp_crm_exception);
				}
			}
		}

	public static function login ($data = null) {
		global
			$wpdb,
			$wp_crm_state,
			$current_user;

		$user = wp_signon (array (
			'user_login' => $data['username'],
			'user_password' => $data['password']
			), false);

		if (!is_wp_error($user)) $current_user = $user;
		}

	public static function signup ($data = null) {
		global
			$wpdb,
			$wp_crm_state,
			$current_user;

		$user = wp_create_user ($data['username'], $data['password'], $data['email']);
		$current_user = new WP_User ($user);
		/**
		 * wp_crm_customer is the simplest role that a wp_crm user can have.
		 */
		/**
		 * actually, wp_crm_user is the simplest role we have. he can only wp_crm_sleep
		 * meening it need to be woken up by some event. usually, following a link
		 * from a confirmation email.
		 */
		$current_user->set_role ('wp_crm_subscriber');
		/**
		 * create aditional structures for each registered user:
		 */
		try {
			$wp_crm_person = new WP_CRM_Person ($data['email']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			$wp_crm_person = new WP_CRM_Person (array (
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'phone' => $data['phone'],
				'email' => $data['email']
				));
			$wp_crm_person->save ();
			}
		/**
		 * send an email: 
		 */
		$hash = md5 ($current_user->ID . $current_user->user_email);

		$wp_crm_mail = new WP_CRM_Mail ();
	
		$wp_crm_mail->send ($current_user->user_email, array (
			'subject' => 'Activare cont utilizator platforma ' . get_bloginfo ('name'),
			'content' => 'Iti multumim ca te-ai inregistrat pentru a deveni membru al platformei ' . get_bloginfo ('name') . '!<br />
Pentru a putea beneficia in totalitate de facilitatile oferite, trebuie sa activezi contul creat prin accesarea link-ului de mai jos:<br /><br />
' . get_bloginfo ('url') . '/activate?h=' . $hash . '&l=' . urlencode($current_user->user_login) . '<br /><br />
Iti multumim!<br />
--<br />
Echipa ' . get_bloginfo ('name')
			));
		}

	public static function activate ($data = null) {
		if (!is_numeric ($data)) return FALSE;
		$user = new WP_User ((int) $data);
		if (!$user->has_cap ('wp_crm_wakeup')) return TRUE;
		$user->set_role ('wp_crm_customer');
		
		return TRUE;
		}

	public static function newsletter ($data = null) {
		global
			$wpdb,
			$wp_crm_state,
			$wp_crm_site,
			$wp_crm_maillist;

		$wp_crm_state->delete ();

		$wp_crm_mailagent = new WP_CRM_MailAgent ($data);
		$wp_crm_mailagent->save ();
		$wp_crm_mailagent->set ('mailagent', 1);

		$templates = $wp_crm_site->get ('templates');
		$wp_crm_template = new WP_CRM_Template ($templates[WP_CRM_State::NewsRegister]['m']);
		$wp_crm_template->assign ('mailagent', $wp_crm_mailagent);	
		

		$wp_crm_mail = new WP_CRM_Mail ($wp_crm_maillist->get ('mid'));
		$wp_crm_mail->send ($wp_crm_mailagent->get ('email'), $template);
		}
	};
?>
