<?php
/**
 * Company is a class for handling corporate clients.
 */
class WP_CRM_Company extends WP_CRM_Model {
	const Padding		= 9327432;

	const Default_Company	= 1;
	const Seller_Company	= 2;

	const Logo		= 'cache/logos';

	private static $TYPES = array (
		'srl'		=> array (
					'title' => 'Societate cu Raspundere Limitata',
					'types' => array (
						'srl-ue' => 'Microintreprindere',
						'srl-se' => 'Intreprindere mica',
						'srl-me' => 'Intreprindere mijlocie',
						'srl-le' => 'Intreprindere mare'
						)
					),
		'uat'		=> array (
					'title' => 'Unitate Administrativ Teritoriala',
					),
		'ong'		=> array (
					'title' => 'Organizatie Non-Guvernamentala'
					),
		'pfa'		=> array (
					'title' => 'Persoana Fizica Autorizata'
					),
		'sa'		=> array (
					'title' => 'Societate pe Actiuni'
					),
		);
	private static $INTERESTS = array (
		'Agricultura',
		'Cercetare',
		'Cooperare Transfrontaliera',
		'Cooperare UE',
		'Dezvoltare rurala',
		'Dezvoltare urbana',
		'Energie',
		'IMM',
		'Infrastructura',
		'Intreprinderi medii',
		'Mediu',
		'Piscicultura',
		'Resurse umane',
		'Servicii sociale',
		'Turism',
		);

	public static $COUNTIES = array (
		'Alba',
		'Arad',
		'Arges',
		'Bacau',
		'Bihor',
		'Bistrita Nasaud',
		'Botosani',
		'Braila',
		'Brasov',
		'Buzau',
		'Calarasi',
		'Caras Severin',
		'Cluj',
		'Constanta',
		'Covasna',
		'Dambovita',
		'Dolj',
		'Galati',
		'Giurgiu',
		'Gorj',
		'Harghita',
		'Hunedoara',
		'Ialomita',
		'Iasi',
		'Ilfov',
		'Maramures',
		'Mehedinti',
		'Mures',
		'Neamt',
		'Olt',
		'Prahova',
		'Salaj',
		'Satu Mare',
		'Sector 1 - Bucuresti',
		'Sector 2 - Bucuresti',
		'Sector 3 - Bucuresti',
		'Sector 4 - Bucuresti',
		'Sector 5 - Bucuresti',
		'Sector 6 - Bucuresti',
		'Sibiu',
		'Suceava',
		'Teleorman',
		'Timis',
		'Tulcea',
		'Valcea',
		'Vaslui',
		'Vrancea'
		);

	public static $T = 'companies';
	protected static $K = array (
		'oid',
		'uid',
		'type',
		'interests',
		'name',
		'description',
		'url',
		'email',
		'rc',
		'uin',
		'address',
		'county',
		'phone',
		'fax',
		'bank',
		'account',
		'flags'
		);

	protected static $M_K = array (
		'logo',							// the logo of this company
		'director',						// who is the manager of this company
		'capital',						// the amount of social capital
		'default_vat',						// what is the default vat for invoices
		'invoice_series',					// the invoices' series
		'invoice_number',					// the invoices' current number
		'templates',						// buying wizzard templates
		'treasury',						// if it has a treasury account, at which treasury
		'treasury_account',					// if it has a treasury account, which is it
		'register',
		'online',						// if it has e-payment methods enabled. 1 = credit europe / 2 = mobilpay 
		'mobilpay',						// the mobilpay key for the e-payment
		'payment',						//
		'size',							// the size of the organization
		'rural',						// if the organization is in the rural area, this should be 1
		'unfavorable'						// if the organization is from an unfavorable area
		);

	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',			/* = office id (link to WP_CRM_Office) */
		'`uid` int(11) NOT NULL DEFAULT 0',			/* = user id (link to WP_CRM_User) */
		'`type` varchar(6) NOT NULL DEFAULT \'srl\'',
		'`interests` text NOT NULL',
		'`name` text NOT NULL',
		'`description` text NOT NULL',
		'`url` text NOT NULL',
		'`email` varchar(128) NOT NULL DEFAULT \'\'',
		'`rc` varchar(32) NOT NULL DEFAULT \'\'',
		'`uin` varchar(13) NOT NULL DEFAULT \'\'',
		'`address` text NOT NULL',
		'`county` varchar(32) NOT NULL DEFAULT \'\'',
		'`phone` varchar(10) NOT NULL DEFAULT \'\'',
		'`fax` varchar(10) NOT NULL DEFAULT 0',
		'`bank` text NOT NULL',
		'`account` text NOT NULL',
		'`flags` int(1) NOT NULL DEFAULT 0',
		'FULLTEXT KEY `name` (`name`,`email`)'
		);
	public static $F = array (
		'new' => array (
			'name' => 'Companie',
			'uin' => 'Cod Fiscal',
			'rc' => 'Reg. Com.',
			'address' => 'Adresa (Strada, Numar, Oras)',
			'county:array;counties' => 'Judet',
			'type:array;types' => 'Tip',
			'interests:multi;interests_list' => 'Domenii de interes',
			'size?type=uat' => 'Populatie',
			'rural:switch?type=uat' => 'UAT in mediul rural?',
			'developed:switch?type=uat' => 'UAT in zona defavorizata?',
			'contact:contact' => 'Persoane de Contact',
			),
		'view' => array (
			'name' => 'Companie',
			'uin' => 'Cod Fiscal',
			'rc' => 'Reg. Com.',
			'address' => 'Adresa',
			'type:array;types' => 'Tip',
//			'account' => 'Cont',
//			'bank' => 'Banca'
			),
		);

	private $flags;
	private $employees;
	private $structure;

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
						if ($this->ID) {
							if ($this->structure instanceof WP_CRM_Company_Structure)
								$this->structure->set ($_value);
							else {
								$this->structure = new WP_CRM_Company_Structure ($this->ID);
								$this->structure->set ($_value);
								}
							}
						else
							$this->structure = $_value;

						unset ($key['contact']);
						break;
					case 'interests':
						$key['interests'] = serialize(self::_unserialize ($_value));
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
			case 'types':
				$out = array ();
				foreach (self::$TYPES as $key => $values) {
					if (isset ($values['types']) && is_array ($values['types']))
						$out[$key] = array (
							'title' => $values['title'],
							'items' => $values['types']
							);
					else
						$out[$key] = $values['title'];
					}
				return $out;
				break;
			case 'interests_list':
				return self::$INTERESTS;
				break;
			case 'counties':
				return self::$COUNTIES;
				break;
			case 'interests':
				return self::_unserialize ($this->data['interests']);
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

	public function save () {
		parent::save ();

		if (!($this->structure instanceof WP_CRM_Company_Structure) && $this->ID) {
			$structure = $this->structure;
			$this->structure = new WP_CRM_Company_Structure ($this->ID);
			$this->structure->set ($structure);
			}
		}
	};
?>
