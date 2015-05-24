<?php
/**
 * Class to access Wordpress user data
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_User extends WP_CRM_Model {
	const Defaults	= '_wp_crm_defaults';
	const Layout	= '_wp_crm_layout';
	const Settings	= '_wp_crm_settings';

	public static $DEFAULTS = array (
		'promoter_invitations'	=>	5,
		);
	public static $LAYOUT = array (
		'dashboard' => array (
			/** Rows, of widgets:								*/
			/** array ( array ('widget' => WIDGET, 'size' => {1...12}), ... ), ...		*/
			),
		);
	public static $SETTINGS  = array (
		'root_folder'		=>	0,
		'scan_folder'		=>	0,
		'companies_folder'	=>	0,
		'default_office'	=>	0,
		'default_company'	=>	0
		);

	private static $CAPABILITIES = array (
		/** System */
		'wp_crm_system'			=> '',
		'wp_crm_admin'			=> '',
		'wp_crm_work'			=> '',
		/** Users */
		'wp_crm_promote'		=> '',
		'wp_crm_loyal'			=> '',
		'wp_crm_shop'			=> '',
		'wp_crm_news'			=> '',
		/** User Management */
		'wp_crm_add_user'		=> '',
		'wp_crm_edit_user'		=> '',
		'wp_crm_delete_user'		=> '',
		/** Chat */
		'wp_crm_use_chat'		=> '',
		/** Evaluation Form */
		'wp_crm_add_evaluation_form'	=> '',
		'wp_crm_edit_evaluation_form'	=> '',
		'wp_crm_delete_evaluation_form'	=> '',
		/** Forum */
		'wp_crm_add_topic'		=> '',
		'wp_crm_edit_topic'		=> '',
		'wp_crm_delete_topic'		=> '',
		'wp_crm_add_reply'		=> '',
		'wp_crm_edit_reply'		=> '',
		'wp_crm_delete_reply'		=> '',
		/** Dictionary */
		'wp_crm_add_dictionary_term'	=> '',
		'wp_crm_edit_dictionary_term'	=> '',
		'wp_crm_delete_dictionary_term'	=> '',
		/** Blog */
		'wp_crm_add_post'		=> '',
		'wp_crm_edit_post'		=> '',
		'wp_crm_delete_post'		=> '',
		/** Courses */
		'wp_crm_add_course'		=> '',
		'wp_crm_edit_course'		=> '',
		'wp_crm_delete_course'		=> '',
		/** Resources */
		'wp_crm_add_resource'		=> '',
		'wp_crm_edit_resource'		=> '',
		'wp_crm_delete_resource'	=> '',
		/** Polls */
		'wp_crm_add_poll'		=> '',
		'wp_crm_edit_poll'		=> '',
		'wp_crm_delete_poll'		=> '',
		/** Tests */
		'wp_crm_add_test'		=> '',
		'wp_crm_edit_test'		=> '',
		'wp_crm_delete_test'		=> '',
		/** Wiki */
		'wp_crm_add_wikipage'		=> '',
		'wp_crm_edit_wikipage'		=> '',
		'wp_crm_delete_wikipage'	=> '',
		/** Live Streaming*/
		'wp_crm_add_livestream'		=> '',
		'wp_crm_close_livestream'	=> '',
		/** Products */
		'wp_crm_add_product'		=> '',
		'wp_crm_edit_product'		=> '',
		'wp_crm_delete_product'		=> '',
		/** Invoices */
		'wp_crm_add_invoice'		=> '',
		'wp_crm_edit_invoice'		=> '',
		'wp_crm_delete_invoice'		=> '',
		'wp_crm_pay_invoice'		=> '',
		#''		=> '',
		);

	public static $ROLES = array (
		/**
		 * Employees:
		 */
		'wp_crm_admin'	=> array (
			'title' => 'WP CRM Office Administrator',
			'capabilities' => array (
				'wp_crm_system'	=> true,
				'wp_crm_admin'	=> true,
				),
			'group_id' => -1,
			),
		'wp_crm_accountant'	=> array (
			'title' => 'WP CRM Office Accountant',
			'capabilities' => array (
				'wp_crm_admin'	=> true,
				'wp_crm_pay'	=> true,
				'wp_crm_work'	=> true,
				),
			'group_id' => -2,
			),
		'wp_crm_user'	=> array (
			'title' => 'WP CRM Office User',
			'capabilities' => array (
				'wp_crm_work'	=> true,
				),
			'group_id' => -3,
			),
		'wp_crm_promoter' => array (
			'title' => 'WP CRM Office Promoter',
			'capabilities' => array (
				'wp_crm_shop' => true,
				'wp_crm_promote' => true,
				'wp_crm_news' => true,
				),
			'group_id' => -4,
			),
		'wp_crm_lecture_manager' => array (
			'title' => 'WP CRM Lecture Manager',
			'capabilities' => array (
				'wp_crm_content' => true,
				'wp_crm_lecture' => true,
				),
			'group_id' => -5
			),
		'wp_crm_lecturer' => array (
			'title' => 'WP CRM Lecturer',
			'capabilities' => array (
				'wp_crm_lecture' => true,
				),
			'group_id' => -6
			),
		/**
		 * Clients:
		 */
		'wp_crm_client'	=> array (
			'title' => 'WP CRM Client',
			'capabilities' => array (
				'wp_crm_loyal'	=> true,
				'wp_crm_shop'	=> true,
				'wp_crm_news' => true,
				),
			'group_id' => -97,
			),
		'wp_crm_customer'	=> array (
			'title' => 'WP CRM Customer',
			'capabilities' => array (
				'wp_crm_shop'	=> true,
				'wp_crm_news' => true,
				),
			'group_id' => -98,
			),
		'wp_crm_subscriber'	=> array (
			'title' => 'WP CRM Subscriber',
			'capabilities' => array (
				'wp_crm_shop'	=> true,
				),
			'group_id' => -99,
			),
		);

	public static $T = 'users';
	public static $K = array (
		'user_login',
		'user_pass',
		'user_nicename',
		'user_email',
		'user_registered',
		'user_status',
		'display_name'
		);
	public static $F = array (
		'new' => array (
			'user_login' => 'Nume utilizator',
			'user_nicename' => 'Nume',
			'password:password' => 'Parola',
			'user_email' => 'Adresa de email',
			'role:array;role_list' => 'Nivel de Acces'
			),
		'view' => array (
			'user_login' => 'Nume utilizator',
			'user_nicename' => 'Nume',
			'user_email' => 'Adresa de email',
			'role:array;role_list' => 'Nivel de Acces'
			),
		'edit' => array (
			'user_login' => 'Nume utilizator',
			'user_nicename' => 'Nume',
			'password:password' => 'Parola',
			'user_email' => 'Adresa de email',
			'role:array;role_list' => 'Nivel de Acces'
			)
		);
	protected static $Q = null;
	private $SRP = '$';

	private $person;

	private $offices;
	private $companies;

	private $defaults;
	private $layout;
	private $settings;

	public function __construct ($data = null) {
		global
			$current_user,
			$wpdb;

		if ($data === FALSE) {
			$current_user = wp_get_current_user ();
			$data = (int) $current_user->ID;
			if (!$data)
				throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
			}

		if (is_string ($data) && !is_numeric ($data)) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . self::$T . '` where user_login=%s;', array ($data));
			$row = $wpdb->get_row ($sql, ARRAY_A);
			if (!empty($row)) {
				$this->ID = (int) $row['ID'];
				$this->data = $row;
				}
			else
				throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
			}		

		parent::__construct ($data);

		if ($this->ID) {
			/**
			 * Adding the defaults meta-keys to this user:
			 * Defaults are settings that can be changed by admins
			 */
			if (($this->defaults = get_user_meta ($this->ID, self::Defaults, TRUE)) === '') {
				add_user_meta ($this->ID, self::Defaults, self::$DEFAULTS, TRUE);
				$this->defaults = self::$DEFAULTS;
				}
			if (empty ($this->defaults)) $this->defaults = self::$DEFAULTS;

			/**
			 * Adding the layout meta-keys to this user:
			 * Layout are settings that this user can change, in order to alter the layout of the app
			 */
			if (($this->layout = get_user_meta ($this->ID, self::Layout, TRUE)) === '') {
				add_user_meta ($this->ID, self::Layout, self::$LAYOUT, TRUE);
				$this->layout = self::$LAYOUT;
				}
			if (empty ($this->layout)) $this->settings = self::$LAYOUT;

			/**
			 * Adding the settings meta-keys to this user:
			 * Settings can be changed by this user alone.
			 */
			if (($this->settings = get_user_meta ($this->ID, self::Settings, TRUE)) === '') {
				add_user_meta ($this->ID, self::Settings, self::$SETTINGS, TRUE);
				$this->settings = self::$SETTINGS;
				}
			if (empty ($this->settings)) $this->settings = self::$SETTINGS;

			$this->offices = get_user_meta ($this->ID, WP_CRM_Office::MetaKey, TRUE);
			$this->offices = $this->offices === '' ? array () : (is_array ($this->offices) ? $this->offices : array ($this->offices));

			
			$this->companies = get_user_meta ($this->ID, WP_CRM_Company::MetaKey, TRUE);
			$this->companies = $this->companies === '' ? array () : (is_array ($this->companies) ? $this->companies : array ($this->companies));
			}

		try {
			$this->person = new WP_CRM_Person ($this->data['user_email']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			$this->person = null;
			}
		}

	public function get ($key = null, $opts = null) {
		global
			$wpdb,
			$wp_roles;

		if (is_string ($key)) {
			switch ($key) {
				case 'person':
					return $this->person instanceof WP_CRM_Person ? $this->person : NULL;
					break;
				case 'password':
				case 'confirm_password':
					return '';
					break;
				case 'offices':
					return $this->offices;
					break;
				case 'office_list':
					break;
				case 'offices_query':
					return sizeof ($this->offices) == 1 ? sprintf ('oid=%d', current($this->offices)) : sprintf ('oid in (%s)', implode (',', $this->offices));
					break;
				case 'companies':
					return $this->companies;
					break;
				case 'company_list':
					return new WP_CRM_List ('WP_CRM_Company', array ($this->get ('offices_query')));
					break;
				case 'products':
					$products = array ();
					$sql = $wpdb->prepare ('select pid,bid,buyer from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where uid=%d;', $this->ID);
					$results = $wpdb->get_results ($sql);
					if ($results)
					foreach ($results as $result)
						$products[] = array (
							'product' => new WP_CRM_Product ((int) $result->pid),
							'company' => new WP_CRM_Company ((int) $result->bid)
							);
					return $products;
					break;
				case 'first_name':
				case 'last_name':
					if (is_object ($this->person)) {
						return $this->person->get ($key);
						}
					break;
				case 'full_name':
					if (is_object ($this->person)) {
						return $this->person->get ('first_name') . ' ' . $this->person->get ('last_name');
						}
					break;
				case 'role':
					$sql = $wpdb->prepare ('select meta_value from `' . $wpdb->usermeta . '` where meta_key=%s and user_id=%d;', array (
							$wpdb->prefix . 'capabilities',
							$this->ID
							));
					$roles = $wpdb->get_var ($sql);
					$roles = unserialize ($roles);
					if (is_array ($roles)) return current (array_keys ($roles));
					return FALSE;
					break;
				case 'role_list':
					$roles = $wp_roles->roles;
					$out = array ();
					if (!empty ($roles))
					foreach ($roles as $key => $capabilities) {
						if (strpos ($key, 'wp_crm_') === 0)
							$out[$key] = $capabilities['name'];
						else
						if (strpos ($key, 'admin') === 0)
							$out[$key] = $capabilities['name'];
						}
					return $out;
					break;
				case 'capability_list':
					return self::$CAPABILITIES;
					break;
				case 'role_path':
					return get_template_directory () . '/template/' . str_replace ('wp_crm_', '', $this->get ('role')) . '/' . $opts;
					break;
				case 'defaults':
					if (in_array ($opts, array_keys ($this->defaults))) return $this->defaults[$opts];
					return FALSE;
					break;
				}
			}

		if (!in_array ($key, static::$K) && WP_CRM_Person::has_key ($key))
			return is_object ($this->person) ? $this->person->get ($key, $opts) : NULL;
		return parent::get ($key, $opts);
		}

	public function set ($key = null, $value = null) {
		global
			$wpdb,
			$wp_roles;

		if (is_array ($key)) {
			if (isset ($key['offices'])) {
				update_user_meta ($this->ID, WP_CRM_Office::MetaKey, $key['offices']);
				unset ($key['offices']);
				}
			if (isset ($key['companies'])) {
				update_user_meta ($this->ID, WP_CRM_Company::MetaKey, $key['companies']);
				unset ($key['companies']);
				}

			if (isset ($key['user_pass'])) unset ($key['user_pass']);

			if (isset ($key['password'])) {
				if (!empty($key['password']) && !empty($key['confirm_password']) && ($key['password'] == $key['confirm_password']))
					$key['user_pass'] = wp_hash_password ($key['password']);
				
				unset ($key['password']);
				unset ($key['confirm_password']);
				}

			if (isset ($key['role']) && isset ($wp_roles->roles[$key['role']])) {
				$user = new WP_User ($this->ID);
				$user->set_role ($key['role']);
				unset ($key['role']);
				}
			}
		else {
			switch ($key) {
				case 'defaults':
					if (!is_array ($value)) return FALSE;
					foreach ($value as $_k => $_v) {
						if (in_array ($_k, array_keys ($this->defaults)))
							$this->defaults[$_k] = $_v;
						}
					update_user_meta ($this->ID, self::Defaults, $this->defaults);
					return TRUE;
					break;
				case 'settings':
					if (!is_array ($value)) return FALSE;
					foreach ($value as $_k => $_v) {
						if (in_array ($_k, array_keys ($this->settings)))
							$this->settings[$_k] = $_v;
						}
					update_user_meta ($this->ID, self::Settings, $this->settings);
					return TRUE;
					break;
				case 'password':
					$key = 'user_pass';
					$value = wp_hash_password ($value);
					break;
				case 'role':
					if (!isset($wp_roles->roles[$value])) return FALSE;
					$user = new WP_User ($this->ID);
					$user->set_role ($value);
					return TRUE;
					break;
				case 'offices':
					update_user_meta ($this->ID, WP_CRM_Office::MetaKey, $value);
					return TRUE;
					break;
				case 'companies':
					update_user_meta ($this->ID, WP_CRM_Company::MetaKey, $value);
					return TRUE;
					break;
				}
			}

		return parent::set ($key, $value);
		}

	public function is ($what = null, $opts = null) {
		global $current_user;

		if (is_string ($what))
		switch ($what) {
			default:
				return ($current_user->ID && ($current_user->ID == $this->ID)) ? TRUE : FALSE;
			}
		}

	public function save () {
		if (!preg_match ('/^[0-9a-f]{32}$/', strtolower($this->data['user_pass'])))
			$this->data['user_pass'] = md5($this->data['user_pass']);

		parent::save ();
		}

	public function srp ($action = '', $opts = null) {
		switch ((string) $action) {
			case 'register':
				if (!$this->ID) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
				add_user_meta ($this->ID, '_wp_crm_verifier', $opts['verifier'], true);
				break;
			case 'init':
				if ($this->SRP != '$') return;
				$this->SRP = get_user_meta ($this->ID, '_wp_crm_verifier', true);
				if ($this->SRP == '') {
					$this->SRP = '$';
					throw new WP_CRM_Exception (WP_CRM_Exception::Missing_SRP_Verifier);
					}
				$srp = new WP_CRM_SRP ($this->SRP);
				$out = $srp->challenge ($opts['A'], $this->data['user_login']);
				
				if (!session_id()) throw new WP_CRM_Exception (WP_CRM_Exception::Session_Required);

				$_SESSION['M'] = $srp->get ('m64');
				$_SESSION['KEY'] = $srp->get ('key');
				$_SESSION['HAMK'] = $srp->get ('hamk64');

				$out['session'] = session_id ();

				return json_encode ($out);
				break;
			case 'server_check':
				if (!session_id()) throw new WP_CRM_Exception (WP_CRM_Exception::Session_Required);
				if ($_SESSION['M'] != $opts['M']) throw new WP_CRM_Exception (WP_CRM_Exception::SRP_M_Check_Failed);
				return json_encode (array ('HAMK' => $_SESSION['HAMK']));
				break;
			case 'encrypt':
				if (!session_id()) throw new WP_CRM_Exception (WP_CRM_Exception::Session_Required);
				if (!$_SESSION['KEY']) throw new WP_CRM_Exception (WP_CRM_Exception::Missing_SRP_Key);

				$key = $_SESSION['KEY'];
				
				$data = $opts; #base64_decode ($opts);

				$iv_size = mcrypt_get_iv_size (MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
				$iv = mcrypt_create_iv ($iv_size, MCRYPT_DEV_URANDOM);

				$padlen = strlen($data) + $iv_size - (strlen($data) % $iv_size);

				$data = str_pad ($data, $padlen, "\x00");

				$encd = $iv . mcrypt_encrypt (MCRYPT_BLOWFISH, $key, $data, MCRYPT_MODE_CBC, $iv);
				$hash = hash ('sha1', $iv . $data, true);

				return base64_encode ($encd . $hash);
				break;
			case 'decrypt':
				if (!session_id()) throw new WP_CRM_Exception (WP_CRM_Exception::Session_Required);
				if (!$_SESSION['KEY']) throw new WP_CRM_Exception (WP_CRM_Exception::Missing_SRP_Key);

				$key = $_SESSION['KEY'];

				$data = base64_decode ($opts);

				$iv_size = mcrypt_get_iv_size (MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
				$iv = substr ($data, 0, $iv_size);

				$hash = substr ($data, -20);
				$data = substr ($data, $iv_size, -20);

				$decd = mcrypt_decrypt (MCRYPT_BLOWFISH, $key, $data, MCRYPT_MODE_CBC, $iv);
				$dech = hash ('sha1', $iv . $decd, true);
				
				if ($dech != $hash) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_SRP_Checksum);
				return $decd;
				break;
			}
		}

	public static function install () {
		/* stub */
		if (!empty (self::$ROLES))
		foreach (self::$ROLES as $role => $options)
			add_role ($role, $options['title'], $options['capabilities']);
		}

	public function can ($what = null) {
		if (is_null ($what)) return TRUE;
		if (!in_array ($what, array_keys (self::$CAPABILITIES))) return FALSE;
		if (!$this->ID) return FALSE;
		return user_can ($this->ID, $what);
		}

	public function check ($what = null, $against = null) {
		if (is_string ($what)) {
			switch ($what) {
				case 'password':
				case 'pass':
				case 'pwd':
					return wp_check_password (trim($against), $this->data['user_pass'], $this->ID);
					break;
				}
			}
		return FALSE;
		}
	}
?>
