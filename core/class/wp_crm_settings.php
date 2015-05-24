<?php
class WP_CRM_Settings {
	protected static $PREFIX = '_wp_crm_';

	protected static $K = array (
		'website' => array (
			'label' => 'Site Web',
			'type' => 'string',
			'default' => 'http://www.einvest.ro'
			),
		'language' => array (
			'label' => 'Limba',
			'type' => 'string',
			'default' => 'Romana'
			),
		'mail_server' => array (
			'label' => 'Adresa server de mail',
			'type' => 'string',
			'default' => 'smtp.gmail.com'
			),
		'mail_server_port' => array (
			'label' => 'Portul serverului de mail',
			'type' => 'string',
			'default' => '465'
			),
		'mail_server_security' => array (
			'label' => 'Securitatea serverului de mail',
			'type' => 'string',
			'default' => 'SSL'
			),
		'mail_account_email' => array (
			'label' => 'Contul implicit de email',
			'type' => 'string',
			'default' => 'SSL'
			),
		'mail_account_name' => array (
			'label' => 'Numele contului implicit de email',
			'type' => 'string',
			'default' => 'SSL'
			),
		'mail_account_password' => array (
			'label' => 'Parola contului implict de email',
			'type' => 'string',
			'default' => 'SSL'
			),
		'mail_registration_subject' => array (
			'label' => 'Subiectul mailului de confirmare a inregistrarii pe site',
			'type' => 'string',
			'default' => 'SSL'
			),
		'mail_registration_content' => array (
			'label' => 'Continutul mailului de confirmare a inregistrarii pe site',
			'type' => 'rte',
			'default' => '',
			),
		'mail_invitation_subject' => array (
			'label' => 'Subiectul mailului invitatie pentru un prieten',
			'type' => 'string',
			'default' => 'SSL'
			),
		'mail_invitation_content' => array (
			'label' => 'Continutul mailului invitatie pentru un prieten',
			'type' => 'rte',
			'default' => '',
			),
		);

	private $data;

	public function __construct () {
		}

	public function get ($key = null, $opts = null) {
		if (is_string ($key))
		switch ($key) {
			case 'fields':
				$out = array ();
				foreach (self::$K as $key => $options) {
					$options['default'] = $this->get ($key);
					$out[$key] = $options;
					}
				return $out;
				break;
			case 'email_settings':
				return array (
					'host'		=> $this->get ('mail_server'),
					'secure'	=> $this->get ('mail_server_security'),
					'port'		=> $this->get ('mail_server_port'),
					'username'	=> $this->get ('mail_account_email'),
					'password'	=> $this->get ('mail_account_password'),
					'name' 		=> $this->get ('mail_account_name')
					);
				break;
			case 'mail_registration':
			case 'registration_mail':
				return array (
					'subject'	=> $this->get ('mail_registration_subject'),
					'content'	=> $this->get ('mail_registration_content')
					);
				break;
			case 'mail_invitation':
			case 'invitation_mail':
				return array (
					'subject'	=> $this->get ('mail_invitation_subject'),
					'content'	=> $this->get ('mail_invitation_content')
					);
				break;
			default:
				if (!in_array ($key, array_keys (self::$K))) return FALSE;
				if (isset ($this->data[$key])) return trim (stripslashes (implode ('', explode ('\\', $this->data[$key]))));
				return trim (stripslashes (implode ('', explode ('\\', ($this->data[$key] = get_option (self::$PREFIX . $key, self::$K[$key]['default']))))));
			}
		}

	public function set ($key = null, $value = null) {
		if (is_string ($key)) {
			if (!in_array ($key, array_keys (self::$K))) return FALSE;
			$this->data[$key] = $value;
			update_option (self::$PREFIX . $key, $value);
			}
		if (is_array ($key)) {
			foreach ($key as $_k => $_v)
				if (in_array ($_k, array_keys (self::$K))) {
					update_option (self::$PREFIX . $_k, $_v);
					$this->data[$_k] = $_v;
					}
			}
		return TRUE;
		}

	public function save () {
		}

	public static function install ($uninstall = FALSE) {
		if ($uninstall) {
			foreach (self::$K as $key => $options)
				delete_option (self::$PREFIX . $key);
			return TRUE;
			}
		foreach (self::$K as $key => $options)
			add_option (self::$PREFIX . $key, $options['default']);

		return TRUE;
		}

	public function __destruct () {
		}
	}
?>
