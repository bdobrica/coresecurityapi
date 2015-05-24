<?php
class WP_CRM {
	public static function buy ($data = null) {
		global
			$wpdb,
			$wp_crm_cookie,
			$wp_crm_state,
			$wp_crm_user;

		$log = new WP_CRM_Log (array ('action' => 'buy'));
		$log->save ();

		$data = empty($data) ?
				$wp_crm_state->get ('data') :
				array_merge ((array) $wp_crm_state->get ('data'), (array) $data);

		if (empty ($data['buyer'])) return array ('message' => array (
			'type' => 'error',
			'content' => 'Nu poti cumpara fara a specifica un cumparator'
			));

		list ($class, $id) = explode ('-', $data['buyer']);
		if (!in_array ($class, array (
			'WP_CRM_Person',
			'WP_CRM_Company'
			))) return array ('message' => array (
			'type' => 'error',
			'content' => 'Eroare de sistem! Cumparatorul nu este un obiect valid.'
			));
		try {
			$buyer = new $class ((int) $id);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			$buyer = null;
			}

		if (is_null ($buyer)) return array ('message' => array (
			'type' => 'error',
			'content' => 'Eroare de sistem! Cumparatorul, desi este un obiect valid nu a fost gasit in baza de date.'
			));

		$basket = $wp_crm_state->get ('basket');
		$basket->save ();

		$wp_crm_state->delete ();

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
				try {
					$wp_crm_invoice = new WP_CRM_Invoice ($invoice_id);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					continue;
					}
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

			/*
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
			*/
			unset ($wp_crm_product);
			}

		return array ('invoice_ids' => $invoice_ids);
		}

	/**
	 * Callback used to create new/update old objects. In order to create a new object,
	 * first creates the object with new Object ([optional-id]), uses the method set
	 * with an array argument (key, value) pairs, than tries to use the method save.
	 * The order of methods applied:
	 * __construct([id]) -> set (array(key => value)) -> save
	 * If a wp_crm_helper object is present, saves that.
	 * __construct([id]) -> ... wp_crm_helper->save (array(key => value))
	 * Returns an associative array that contains the saved object.
	 */

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
		
		$log = new WP_CRM_Log (array ('action' => 'save', 'object' => $object->get ('self')));
		$log->save ();


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

		return array ('object' => $object);
		}

	public static function login ($data = null) {
		global
			$wpdb,
			$wp_crm_state,
			$current_user;

		$log = new WP_CRM_Log (array ('action' => 'login_attempt', 'details' => json_encode (array ('username' => $data['username']))));
		$log->save ();

		$user = wp_signon (array (
			'user_login' => $data['username'],
			'user_password' => $data['password']
			), false);

		if (!is_wp_error($user)) {
			$current_user = $user;

			$log = new WP_CRM_Log (array ('action' => 'login_attempt_success'));
			$log->save ();

			return array ();
			}

		$log = new WP_CRM_Log (array ('action' => 'login_attempt_failed'));
		$log->save ();

		return array ('message' => array (
			'type' => 'error',
			'content' => 'Numele de utilizator sau parola nu sunt corecte!'
			));
		}

	public static function signup ($data = null) {
		global
			$wpdb,
			$wp_crm_state,
			$current_user;

		$user = wp_create_user ($data['username'], $data['password'], $data['email']);
		$current_user = new WP_User ($user);
		
		$log = new WP_CRM_Log (array ('action' => 'signup'));
		$log->save ();
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
				'email' => $data['email'],
				'interests' => serialize($data['interests'])
				));
			$wp_crm_person->save ();
			}
		/**
		 * send an email: 
		 */
		$hash = md5 ($current_user->ID . $current_user->user_email);
		/**
		 * send an email from the default email account:
		 */
		$wp_crm_settings = new WP_CRM_Settings ();

		$wp_crm_mail = new WP_CRM_Mail ($wp_crm_settings->get ('email_settings'));
	
		$activation_url = get_bloginfo ('url') . '/activate?h=' . $hash . '&l=' . urlencode($current_user->user_login);

		$wp_crm_template = new WP_CRM_Template ($wp_crm_settings->get ('registration_mail'));
		$wp_crm_template->assign ('activation_url', $activation_url);
		$wp_crm_template->assign ('current_user.user_login', $current_user->user_login);

		$wp_crm_mail->send ($current_user->user_email, $wp_crm_template);
		
		return array ();
		}

	public static function activate ($data = null) {
		if (!is_numeric ($data)) return array (); # FALSE
		$user = new WP_User ((int) $data);

		$log = new WP_CRM_Log (array ('uid' => $user->ID, 'action' => 'activate_attempt'));
		$log->save ();

		if (!$user->has_cap ('wp_crm_wakeup')) {
			$log = new WP_CRM_Log (array ('uid' => $user->ID, 'action' => 'activate_attempt_failed'));
			$log->save ();
			return array (); # TRUE
			}
		$user->set_role ('wp_crm_customer');

		$log = new WP_CRM_Log (array ('uid' => $user->ID, 'action' => 'activate_success'));
		$log->save ();
		
		return array (); # TRUE
		}

	public static function forgot ($data = null) {
		if (!$data['email']) return array ('message' => 'Adresa de email nu este valida!');
		$data['email'] = strtolower (trim ($data['email']));
		if (!filter_var ($data['email'], FILTER_VALIDATE_EMAIL)) return array ('message' => array ('type' => 'error', 'content' => 'Adresa de email nu este valida!'));
		if (!($user_id = email_exists ($data['email']))) return array ('message' => array ('type' => 'error', 'content' => 'Adresa de email nu este valida!'));

		$src = str_split ('ABCDEFGHIJKLMNOPQRSTUWXYZabcdefghijklmnopqrstuvwxyz0123456789~!@#$%^&*()_+-=');
		$len = 8;
		$pass = '';
		for ($c = 0; $c<$len; $c++) $pass .= $src[rand(0, sizeof ($src) - 1)];

		$user = new WP_User ((int) $user_id);

		$wp_set_password ($user_id, $pass);

		$wp_crm_mail = new WP_CRM_Mail ();

		$wp_crm_mail->send ($data['email'], array (
			'subject' => 'Recuperare parola platforma ' . get_bloginfo ('name'),
			'content' => 'Salut ' . $user->display_name . ',<br />
Noile date de acces pentru contul tau de pe platforma ' . get_bloginfo ('name') . ' sunt:<br />
<ul>
	<li>Nume de utilizator: ' . $user->user_login . '</li>
	<li>Parola: ' . $pass . '</li>
</ul><br />
Iti multumim,
--<br />
Echipa ' . get_bloginfo ('name')
			));

		return array ('message' => 'A fost trimis un email cu noile date de acces pe adresa ' . $data['email'] . '.');
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

	public static function order ($data = null) {
		global
			$wpdb,
			$wp_crm_buyer;

		if (empty($data['object'])) return;
		list ($class, $id) = explode ('-', $data['object']);
		if (!class_exists ($class)) return;
		if ($class != 'WP_CRM_Product') return;
		
		$object = new $class ((int) $id);

		$company = new WP_CRM_Company ((int) $data['company']);
		$wp_crm_buyer = new WP_CRM_Buyer ($company);

		$object->buy ();
		}

	public static function _ ($message) {
		return __ ($message, 'WP_CRM_Plugin');
		}

	public static function debug ($message, $object = null) {
		echo '<!--' . "\n" . (is_null ($object) ? '' : ('Object: ' . $object->get ('self') . "\n")) . $message . "\n" . '-->' . "\n\n";
		}
	};
?>
