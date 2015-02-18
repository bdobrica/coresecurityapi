<?php
class WP_CRM_Person extends WP_CRM_Model {
	public static $T = 'persons';
	protected static $K = array (
		'name',
		'avatar',
		'first_name',
		'last_name',
		'email',
		'address',
		'county',
		'phone',
		'company',
		'position',
		'uin',
		'id_series',
		'id_number',
		'id_issuer',
		'id_expire',
		'id_place',
		'id_father',
		'id_mother',
		'card',
		'password',
		'notes',
		'interests',
		'stamp',
		'language',
		'flags'
		);
	protected static $M_K = array (
		'id_copy',
		'diploma_copy'
		);
	public static $F = array (
		'new' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume',
			'avater:file' => 'Avatar',
			'email' => 'E-Mail',
			'address' => 'Adresa',
			'phone' => 'Telefon',
			),
		'edit' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume',
			'avater:file' => 'Avatar',
			'email' => 'E-Mail',
			'address' => 'Adresa',
			'phone' => 'Telefon',
			),
		'view' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume',
			'email' => 'E-Mail',
			'address' => 'Adresa',
			'phone' => 'Telefon',
			),
		'extended' => array (
			'id_series' => 'Serie CI',
			'id_number' => 'Numar CI',
			'id_issuer' => 'Eliberat de',
			'id_expire' => 'La data de',
			'id_place' => 'Locul nasterii',
			'id_father' => 'Numele tatalui',
			'id_mother' => 'Numele mamei',
			),
		'private' => array (
			)
		);
	protected static $U = array (
		'email'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uin` varchar(13) NOT NULL DEFAULT \'\'',
		'`id_type` enum(\'CI\',\'BI\') DEFAULT \'CI\'',
		'`id_series` varchar(2) NOT NULL DEFAULT \'\'',
		'`id_number` int(11) NOT NULL DEFAULT 0',
		'`id_issuer` varchar(64) NOT NULL DEFAULT \'\'',
		'`id_expire` int(11) NOT NULL DEFAULT 0',
		'`id_place` varchar(64) NOT NULL DEFAULT \'\'',
		'`id_father` varchar(64) NOT NULL DEFAULT \'\'',
		'`id_mother` varchar(64) NOT NULL DEFAULT \'\'',
		'`avatar` text NOT NULL',
		'`name` varchar(128) NOT NULL DEFAULT \'\'',
		'`first_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`last_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`address` mediumtext NOT NULL',
		'`county` varchar(32) NOT NULL DEFAULT \'\'',
		'`email` varchar(64) NOT NULL DEFAULT \'\'',
		'`phone` varchar(12) NOT NULL DEFAULT \'\'',
		'`company` varchar(64) NOT NULL DEFAULT \'\'',
		'`position` varchar(64) NOT NULL DEFAULT \'\'',
		'`language` varchar(2) NOT NULL DEFAULT \'ro\'',
		'`card` int(10) NOT NULL DEFAULT 0',
		'`password` varchar(40) NOT NULL DEFAULT \'\'',
		'`notes` text NOT NULL',
		'`interests` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'`flags` int(11) NOT NULL DEFAULT 0',
		'UNIQUE(`email`)',
		'FULLTEXT KEY `first_name` (`first_name`,`last_name`,`name`,`email`)',
		);

	private $flags;

	public function __construct ($data = array()) {
		global $wpdb;

		if (is_string($data) && strpos($data, '@')) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . self::$T . '` where email like %s;', trim($data));
			$data = $wpdb->get_row ($sql, ARRAY_A);
			if (empty($data)) throw new WP_CRM_Exception ( __CLASS__ . ' :: Unknown E-Mail', WP_CRM_Exception::Unknown_Email );
			parent::__construct ( $data );
			}
		else
		if (is_string($data) && strlen($data) > 11) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . self::$T . '` where uin=%d;', $data);
			$data = $wpdb->get_row ($sql, ARRAY_A);
			if (empty($data)) throw new WP_CRM_Exception ( __CLASS__ . ' :: Unknown UIN', WP_CRM_Exception::Unknown_UIN );
			parent::__construct ( $data );
			}
		else
			parent::__construct ( $data );

		$this->uin();
		$this->flags = isset($data['flags']) ? (int) $data['flags'] : 0;
		}

	private function uin () {
		$uin = preg_replace('/[^0-9]+/','',$this->data['uin']);
		if (strlen($uin) != 13) { $this->errors[] = 'UIN number error'; return FALSE; }
		$gender	= substr ($uin, 0, 1);
		$year	= substr ($uin, 1, 2);
		$month	= substr ($uin, 3, 2);
		$day	= substr ($uin, 5, 2);

		if (!in_array($gender, array(1,2,5,6))) { $this->errors[] = 'UIN gender error'; return FALSE; }
		if (($gender == 1 || $gender == 2) && ($birthday = strtotime('19'.$year.'-'.$month.'-'.$day)) === FALSE) { $this->errors[] = 'UIN birthday error'; return FALSE; }
		if (($gender == 5 || $gender == 6) && ($birthday = strtotime('20'.$year.'-'.$month.'-'.$day)) === FALSE) { $this->errors[] = 'UIN birthday error'; return FALSE; }

		$key = array (2,7,9,1,4,6,3,5,8,2,7,9);
		$control = 0;
		for ($c = 0; $c<12; $c++) $control += ((int)substr($uin,$c,1))*$key[$c];
		$control %= 11;
		if ($control == 10) $control = 1;
		if (substr($uin,12,1) != $control) { $this->errors[] = 'UIN checksum error'; return FALSE; }
		$this->data['gender'] = $gender%2 ? 'M' : 'F';
		$this->data['age'] = date('Y')-date('Y',$birthday);
		if (date('d')-date('d',$birthday) < 0 || date('m')-date('m',$birthday) < 0) $this->data['age']--;
		$this->data['birthday'] = $birthday;
		$this->data['happy_birthday'] = (date('m-d') == date('m-d', $birthday)) ? TRUE : FALSE;
		return TRUE;
		}

	private function products () {
		global $wpdb;
		$sql = $wpdb->prepare ('select series,number,stamp from `'.$wpdb->prefix.'clients` where uin=%s;', $this->data['uin']);
		$products = $wpdb->get_results ($sql);
		$this->data['products'] = array();
		foreach ($products as $product) {
			$this->data['products'][] = array (
				'product' => new WP_CRM_Product(array('series' => $product->series, 'number' => $product->number)),
				'stamp' => $product->stamp,
				);
			}
		}
	private function invoices () {
		global $wpdb;
		$sql = $wpdb->prepare ('select iid,stamp from `'.$wpdb->prefix.'clients` where uin=%s;', $this->data['uin']);
		$invoices = $wpdb->get_results ($sql);
		$skip = array ();
		$this->data['invoices'] = array();
		foreach ($invoices as $invoice) {
			$skip[] = (int) $invoice->iid;
			$this->data['invoices'][] = array (
				'invoice' => new WP_CRM_Invoice((int) $invoice->iid),
				'stamp' => $invoice->stamp,
				);
			}
		$sql = $wpdb->prepare ('select id,stamp from `'.$wpdb->prefix.'new_invoices` where bid=%s and buyer=\'person\';', $this->ID);
		$invoices = $wpdb->get_results ($sql);
		foreach ($invoices as $invoice) {
			if (!empty($skip) && in_array($invoice->id, $skip)) continue;
			$this->data['invoices'][] = array (
				'invoice' => new WP_CRM_Invoice((int) $invoice->id),
				'stamp' => $invoice->stamp,
				);
			}
		}

	public function register ($product, $invoice = NULL, $stamp = NULL) {
		global $wpdb;
		if (!is_object($product)) return FALSE;
		$code = $product->get('active');
		$series = trim(preg_replace('/[^A-Z]+/','',strtoupper($code)));
		$number = intval(preg_replace('/[^0-9]+/','',$code));

		$sql = $wpdb->prepare('insert into wp_clients (iid,uin,pid,series,number,stamp,flags) values (%d,%d,%d,%s,%d,%d,%d);', array (
			is_object($invoice) ? $invoice->get('id') : 0,
			$this->data['uin'],
			$product->get(),
			$series,
			$number,
			$stamp ? $stamp : time(),
			0));
		if (WP_CRM_Debug)
		echo "WP_CRM_Person::register::sql( $sql )\n";
		$wpdb->query ($sql);
		}

	private function invoice ($code = '') {
		global $wpdb;
		$series = wp_crm_extract_series($code);
		$number = wp_crm_extract_number($code);
		$invoice_id = $wpdb->get_var($wpdb->prepare('select iid from `'.$wpdb->prefix.'clients` where uin=%s and series=%s and number=%d;', $this->data['uin'], $series, $number));
		$this->data['invoice'] = new WP_CRM_Invoice ($invoice_id);
		}

	public function move ($code_a, $code_b) {
		global $wpdb;
		if (!$this->data['uin']) return FALSE;
		$sql = $wpdb->prepare ('update `wp_clients` set series=%s,number=%d where uin=%d and series=%s and number=%d;', array (
			wp_crm_extract_series ($code_b),
			wp_crm_extract_number ($code_b),
			$this->data['uin'],
			wp_crm_extract_series ($code_a),
			wp_crm_extract_number ($code_a),
			));
		$wpdb->query ($sql);
		}

	public function get ($key = null, $value = null) {
		global $wpdb;

		switch ((string) $key) {
			case 'email':
			case 'e-mail':
			case 'mail':
				return trim (strtolower ( $this->data['email'] ));
				break;
			case 'first_name':
			case 'last_name':
				return str_replace (' ', '-', ucwords( str_replace ( '-', ' ', trim( strtolower( $this->data[$key] )))));
				break;
			case 'name':
				return ucwords( strtolower( $this->data['first_name'] . ' ' . $this->data['last_name'] ));
				break;
			case 'keys':
				return array_merge (parent::get ('keys'), array (
					'title',
					'gender',
					'birthday'));
				break;
			case 'gender':
				return ((int) substr($this->data['uin'], 0, 1)) % 2 ? 'M' : 'F';
				break;
			case 'title':
				$gender = (int) substr($this->data['uin'], 0, 1);
				return $gender%2 ?
						'Stimate dl.' : (
						$gender ?
							'Stimata dna.' :
							'Stimate' );
				break;
				
			}

		return parent::get ($key, $value);
		/*
		HIST: sa nu uitam proprietatile
		*/
		if (!$key) return $this->ID;
		if ($key == 'keys') return $this->keys;
		if ($key == 'invoice') {
			$this->invoice($value);
			return $this->data['invoice'];
			}
		if ($key == 'data') return $this->data;
		if ($key == 'initial') {
			$initials = preg_replace ('/[ ;,.-]+/', ' ', strtoupper($this->data['id_father']));
			$initials = mb_split (' ', $initials);
			$out = array ();
			foreach ($initials as $initial) {
				$initial = trim($initial);
				if (!$initial) continue;
				$out[] = mb_substr($initial, 0, 1).'.';
				}
			if (empty($out)) return '';
			return implode('-', $out);
			}
		if ($this->data[$key]) return $this->data[$key];
		if ($key == 'invoices') {
			$this->invoices();
			return $this->data[$key];
			}
		if ($key == 'products') {
			$this->products();
			return $this->data[$key];
			}
		if ($key == 'products icons') {
			$this->products();
			$out = array ();
			if (!empty($this->data['products']))
				foreach ($this->data['products'] as $product)
					$out[] = $product['product']->get('icon');
			return $out;
			}
		if ($key == 'type') return 'person';
		if ($key == 'voucher') {
			$voucher = $wpdb->get_var($wpdb->prepare('select id from `'.$wpdb->prefix.'clients` where uin=%s and series=%s and number=%s;', $this->data['uin'], wp_crm_extract_series($value), wp_crm_extract_number($value)));
			return 'VCX'.str_pad($voucher, 6, 0, STR_PAD_LEFT);
			}
		if ($key == 'cnfpa') {
			return $wpdb->get_var($wpdb->prepare('select cnfpa from `'.$wpdb->prefix.'clients` where uin=%s and series=%s and number=%s;', $this->data['uin'], wp_crm_extract_series($value), wp_crm_extract_number($value)));
			}
		if ($key == 'grade') {
			return (float) $wpdb->get_var($wpdb->prepare('select grade from `'.$wpdb->prefix.'clients` where uin=%s and series=%s and number=%s;', $this->data['uin'], wp_crm_extract_series($value), wp_crm_extract_number($value)));
			}
		if ($key == 'diploma') {
			return (int) $wpdb->get_var($wpdb->prepare('select diploma from `'.$wpdb->prefix.'clients` where uin=%s and series=%s and number=%s;', $this->data['uin'], wp_crm_extract_series($value), wp_crm_extract_number($value)));
			}
		if ($key == 'when') {
			if (!is_object($value)) return FALSE;
			return $wpdb->get_var ($wpdb->prepare ('select stamp from `'.$wpdb->prefix.'clients` where uin=%s and series=%s and number=%s;', $this->data['uin'], $value->get('current series'), $value->get('current number')));
			}
		if ($key == 'invoice') return $this->invoice($value);
		return FALSE;
		}

	public function is ($key = null, $opts = null) {
		global $wpdb;
		/*
		if ($key == 'customer' || $key == 'paying customer') {
			if ((intval($this->flags) & 2) == 2) return TRUE;
			if (is_numeric($value))
				$sql = $wpdb->prepare ('select iid,flags from `'.$wpdb->prefix.'clients` where uin=%s and iid!=%d;', $this->data['uin'], $value);
			else
				$sql = $wpdb->prepare ('select iid,flags from `'.$wpdb->prefix.'clients` where uin=%s;', $this->data['uin']);

			$clients = $wpdb->get_results ($sql);
			if (empty($clients)) return FALSE;
			if ($key == 'customer') return TRUE;
			$paying = FALSE;
			foreach ($clients as $client) {
				$sql = $wpdb->prepare ('select paidby from `'.$wpdb->prefix.'new_invoices` where id=%d;', $client->iid);
				$paidby = $wpdb->get_var ($sql);
				if ($paidby != 'none') $paying = TRUE;
				else {
					if ((((int) $client->flags) & 1) == 1) $paying = TRUE;
					}
				}
			return $paying;
			}
		*/
		}

	public function set ($key = null, $value = null) {
		parent::set ($key, $value);

		/*
		HIST: paying customers = sparla, used for discount only
		*//*
		if ($key == 'paying customer') {
			$this->flags = $value ? ((intval($this->flags)) | 2) : ((intval($this->flags)) & (~2));
			if ($this->ID) {
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'persons` set flags=%d where id=%d;', $this->flags, $this->ID));
				}
			}
		*/

		return FALSE;
		}

	public function auth ($pass = null, $check = true) {
		if (!$check) {
			$hash = sha1 ($data['stamp'] . $pass);
			$this->set ('password', $hash);
			return $hash;
			}
		if (!$data['password']) return TRUE;
		if (sha1 ($data['stamp'] . $pass) == $data['password']) return TRUE;
		return FALSE;
		}

	public function save () {
		global $wpdb;

		/*
		INFO: Fix the missing names on the invoices generated from the admin iface
		*/
		if (($this->data['first_name'] && $this->data['last_name']) && !$this->data['name'])
			$this->data['name'] = $this->data['first_name'] . ' ' . $this->data['last_name'];
		if ((!$this->data['first_name'] && !$this->data['last_name']) && $this->data['name']) {
			$names = explode (' ', trim ($this->data['name']));
			if (count ($names) < 2) {
				$this->data['first_name'] = $names[0];
				$this->data['last_name'] = $names[0];
				}
			else {
				$this->data['last_name'] = array_pop ($names);
				$this->data['first_name'] = implode (' ', $names);
				}
			}

		$missing = 0;
		foreach (self::$U as $key)
			$missing += $this->data[$key] ? 0 : 1;

		if ($missing == count (self::$U)) throw new WP_CRM_Exception (WP_CRM_Exception::Saving_Failure);

		parent::save ();
		}

	public function __destruct () {
		}
	};
?>
