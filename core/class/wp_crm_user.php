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
			),
		'view' => array (
			),
		'edit' => array (
			)
		);
	protected static $Q = null;
	private $SRP = '$';

	public function __construct ($data = null) {
		global $wpdb;
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
		}
	}
?>
