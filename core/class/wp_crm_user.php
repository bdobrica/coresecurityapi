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
	public static $ROLES = array (
		'wp_crm_admin'	=> array (
			'title' => 'WP CRM Office Administrator',
			'capabilities' => array (
				'wp_crm_admin'	=> true,
				'wp_crm_pay'	=> true,
				'wp_crm_work'	=> true,
				),
			),
		'wp_crm_accountant'	=> array (
			'title' => 'WP CRM Office Accountant',
			'capabilities' => array (
				'wp_crm_pay'	=> true,
				'wp_crm_work'	=> true,
				),
			),
		'wp_crm_user'	=> array (
			'title' => 'WP CRM Office User',
			'capabilities' => array (
				'wp_crm_work'	=> true,
				),
			),
		'wp_crm_client'	=> array (
			'title' => 'WP CRM Client',
			'capabilities' => array (
				'wp_crm_loyal'	=> true,
				'wp_crm_shop'	=> true,
				),
			),
		'wp_crm_customer'	=> array (
			'title' => 'WP CRM Customer',
			'capabilities' => array (
				'wp_crm_shop'	=> true,
				),
			),
		'wp_crm_subscriber'	=> array (
			'title' => 'WP CRM Subscriber',
			'capabilities' => array (
				'wp_crm_shop'	=> true,
				),
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
				case 'password':
				case 'confirm_password':
					return '';
					break;
				case 'products':
					$product_ids = '0';
					if (is_object ($this->person)) {
						$sql = $wpdb->prepare ('select group_concat(pid) from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where bid=%d;', $this->person->get());
						$product_ids = $wpdb->get_var ($sql);
						}
					return new WP_CRM_List ('WP_CRM_Product', array ('id in (' . $product_ids . ')'));
					break;
				case 'first_name':
				case 'last_name':
					if (is_object ($this->person)) {
						return $this->person->get ($key);
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
				}
			}

		return parent::get ($key, $opts);
		}

	public function set ($key = null, $value = null) {
		global
			$wpdb,
			$wp_roles;

		if (is_array ($key)) {
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
				}
			}

		return parent::set ($key, $value);
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
	}
?>
