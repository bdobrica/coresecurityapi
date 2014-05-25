<?php
/**
 * Company is a class for handling corporate clients.
 */
class WP_CRM_Company extends WP_CRM_Model {
	const Padding		= 9327432;

	const Default_Company	= 1;
	const Seller_Company	= 2;

	const Logo		= 'cache/logos';

	private static $TYPE = array (
		'uat'		=> array (
					'title' => 'Unitate Administrativ Teritoriala'
					),
		'ong'		=> array (
					'title' => 'Organizatie Non-Guvernamentala'
					),
		'srl'		=> array (
					'title' => 'Societate cu Raspundere Limitata'
					),
		'pfa'		=> array (
					'title' => 'Persoana Fizica Autorizata'
					),
		'sa'		=> array (
					'title' => 'Societate pe Actiuni'
					),
		);

	public static $T = 'companies';
	protected static $K = array (
		'oid',
		'uid',
		'type',
		'name',
		'description',
		'url',
		'logo',
		'email',
		'rc',
		'uin',
		'capital',
		'address',
		'county',
		'phone',
		'fax',
		'bank',
		'account',
		'treasury',
		'treasury_account',
		'default_vat',
		'invoice_series',
		'invoice_number',
		'director',
		'templates',
		'register',
		'online',
		'mobilpay',
		'payment',
		'flags'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',			/* = office id (link to WP_CRM_Office) */
		'`uid` int(11) NOT NULL DEFAULT 0',			/* = user id (link to WP_CRM_User) */
		'`type` enum(\'uat\', \'ong\', \'srl\', \'pfa\', \'sa\') NOT NULL DEFAULT \'srl\'',
		'`name` text NOT NULL',
		'`description` text NOT NULL',
		'`logo` text NOT NULL',
		'`url` text NOT NULL',
		'`email` varchar(128) NOT NULL DEFAULT \'\'',
		'`rc` varchar(32) NOT NULL DEFAULT \'\'',
		'`uin` varchar(13) NOT NULL DEFAULT \'\'',
		'`capital` float(9,2) NOT NULL DEFAULT 200.00',
		'`address` text NOT NULL',
		'`county` varchar(32) NOT NULL DEFAULT \'\'',
		'`phone` varchar(10) NOT NULL DEFAULT \'\'',
		'`fax` varchar(10) NOT NULL DEFAULT 0',
		'`bank` text NOT NULL',
		'`account` text NOT NULL',
		'`treasury` text NOT NULL',
		'`treasury_account` text NOT NULL',
		'`default_vat` float(4,2) NOT NULL DEFAULT 24.00',
		'`invoice_series` varchar(4) NOT NULL DEFAULT \'\'',
		'`invoice_number` int(11) NOT NULL DEFAULT 0',
		'`director` int(11) NOT NULL DEFAULT 0',
		'`templates` text NOT NULL',
		'`register` int(11) NOT NULL DEFAULT 0',
		'`online` int(11) NOT NULL DEFAULT 0 COMMENT \'|1 = crediteurope; |2 = mobilpay\'',
		'`mobilpay` varchar(25) NOT NULL DEFAULT \'\'',
		'`payment` int(11) NOT NULL DEFAULT 0',
		'`flags` int(1) NOT NULL DEFAULT 0',
		'FULLTEXT KEY `name` (`name`,`email`)'
		);
	public static $F = array (
		'new' => array (
			'name' => 'Companie',
			'uin' => 'Cod Fiscal',
			'rc' => 'Reg. Com.',
			'address' => 'Adresa',
			'contact:contact' => 'Contact',
			'logo:file' => 'Logo'
			),
		'view' => array (
			'name' => 'Companie',
			'uin' => 'Cod Fiscal',
			'rc' => 'Reg. Com.',
			'address' => 'Adresa',
			'contact:contact' => 'Contact',
			'logo:file' => 'Logo'
//			'account' => 'Cont',
//			'bank' => 'Banca'
			),
		);

	private $flags;
	private $employees;

	public function __construct ($data = null) {
		parent::__construct ($data);
		/*
		global $wpdb;


		if (is_array ($data) && isset ($data['uin'])) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where uin=%s;', $data['uin']);
			$data = $wpdb->get_row ($sql, ARRAY_A);
			if (!empty ($data))
				parent::__construct ($data);
			else
				$data = null;
			}

		$this->flags = isset ($data['flags']) ? ((int) $data['flags']) : 0;
		$this->employees = array ();
		*/
		}

	public function add ($person, $default = true) {
		if (is_object($person)) {
			$this->persons[] = $person;
			}
		}

	public function set ($key = null, $value = null) {
		global $wpdb;

		if (is_array ($key)) {
			if (!empty ($key))
			foreach ($key as $_key => $_value) {
				switch ((string) $_key) {
					case 'contact':
						$wp_crm_structure = new WP_CRM_Company_Structure ($this->ID);
						$wp_crm_structure->set ($_value);
						unset ($key['contact']);
						break;
					}
				}
			}

		parent::set ($key, $value);
		}

	public function get ($key = null, $opts = null) {
		global $wpdb;

		switch ((string) $key) {
			case 'logo path':
				return dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/' . ltrim (preg_replace('/^http[s]?:\/\/[^\/]+/', '', $this->get ('logo')), '/');
				break;
			case 'title':
		/*
		TODO: create language file
		*/
				return 'In atentia: ';
				break;
			case 'contact':
				$wp_crm_contact = new WP_CRM_Company_Structure ((int) $this->ID);
				return $wp_crm_contact->get ();
				break;
			}
		
		return parent::get ($key, $opts);
		/*
		HIST: for historical reasons
		*/
		if ($key == 'name') return $this->data['name'];
		if ($key == 'first_name') return $this->data['name'];
		if ($key == 'last_name') return '';
		if ($key == 'nice name') {
			$out = $this->data['name'];
			$out = str_replace (array ('S.R.L.', 'S.R.L', 'SRL', 'S.C.', 'S.C ', 'SC '), '', $out);
			return trim($out);
			}
		if ($key == 'invoice_series') return $this->data['invoice_series'];
		if ($key == 'default_vat') return $this->data['default_vat'];
		if ($key == 'rc') return $this->data['rc'];
		if ($key == 'uin') return $this->data['uin'];
		if ($key == 'address') return $this->data['address'];
		if ($key == 'city') {
			$pieces = preg_split ('/[ ,;]+/', $this->data['address']);
			return $pieces[count($pieces)-1];
			}
		if ($key == 'county') return $this->data['county'];
		if ($key == 'bank') return $this->data['bank'];
		if ($key == 'account') return $this->data['account'];
		if ($key == 'capital') return $this->data['capital'];
		if ($key == 'phone') return $this->data['phone'];
		if ($key == 'fax') return $this->data['fax'];
		if ($key == 'email') return $this->data['email'];
		if ($key == 'keys') return $this->keys;
		if ($key == 'type') return 'company';
		if ($key == 'director') return $this->data['director'] ? new WP_CRM_Person ($this->data['director']) : null;
		if ($key == 'crediteurope payment') return (((((int) $this->data['online']) & 1) == 1) && (function_exists('wp_crm_crediteurope_payment'))) ? TRUE : FALSE;
		if ($key == 'mobilpay payment') return (((((int) $this->data['online']) & 2) == 2) && (function_exists('wp_crm_mobilpay_payment'))) ? TRUE : FALSE;
		if ($key == 'no online payment') return $this->data['online'] ? FALSE : TRUE;
		if ($key == 'mobilpay' || $key == 'mobilpay key') return $this->data['mobilpay'];
		return $this->ID;
		}

	public function is ($key) {
		if ($key == 'default')
			return (((int) $this->flags) & WP_CRM_Company::Default_Company) == WP_CRM_Company::Default_Company ? TRUE : FALSE;
		if ($key == 'seller')
			return (((int) $this->flags) & WP_CRM_Company::Seller_Company) == WP_CRM_Company::Seller_Company ? TRUE : FALSE;
		return FALSE;
		}

	public function can ($key, $opts = null) {
		switch ((string) $key) {
			case 'payment':
				$opts = (int) $opts;
				$mask = (int) $this->data['payment'];
				return ($opts & $mask) == $opts ? TRUE : FALSE;
				break;
			}
		}
	};
?>
