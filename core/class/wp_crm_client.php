<?php
/**
 * Client is an extension of Person. Actually a person that bought something.
 * It holds methods to access invoices and products. Works only for Person.
 */
class WP_CRM_Client extends WP_CRM_Person {
	const Max_Clients	= 9;

	public static $T = 'clients';
	protected static $K = array (
		'uid',
		'iid',
		'pid',
		'cid',
		'stamp'
		);

	public static $F = array (
		'new' => array (
			'person:person' => 'Participant',
			'iid:invoice' => 'Factura',
			'pid:product' => 'Produs'
			),
		'edit' => array (
			'person:person' => 'Participant',
			'iid:invoice' => 'Factura',
			'pid:product' => 'Produs'
			),
		'view' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume',
			'invoice_series' => 'Factura',
			'invoice_paid:bool' => 'Platita',
			'email' => 'E-Mail',
			'address' => 'Adresa',
			'county' => 'Judet',
			'phone' => 'Telefon',
			'uin' => 'CNP',
			),
		'public' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume',
			'invoice_series' => 'Factura',
			'invoice_paid:bool' => 'Platita',
			'email' => 'E-Mail',
			'address' => 'Adresa',
			'county' => 'Judet',
			'phone' => 'Telefon',
			'uin' => 'CNP',
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

	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`iid` int(11) NOT NULL DEFAULT 0',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		);

	private $person;
	private $products;
	private $invoice;

	public function __construct ($data = null) {
		global $wpdb;

		$this->products = array ();

		if ($data instanceof WP_CRM_Person) {
			$this->person = $data;
			parent::__construct (array ('uid' => $data->get()));
			}
		else {
			parent::__construct ($data);
			}

		if ($this->data['iid']) {
			try {
				$this->invoice = new WP_CRM_Invoice ((int) $this->data['iid']);
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$this->invoice = null;
				throw new WP_CRM_Exception (WP_CRM_Exception::Invoiceless_Client);
				}
			}

		if (!($this->person instanceof WP_CRM_Person)) {
			try {
				$this->person = $this->data['uid'] ?
					$this->person = new WP_CRM_Person ((int) $this->data['uid']) :
					null;
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$this->person = null;
				}
			}

		if ($this->data['uid']) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where uid=%d;', $this->data['uid']);
			$rows = $wpdb->get_results ($sql);
			if (!empty($rows))
				foreach ($rows as $row)
					$this->products[$row->pid] = $row->iid;
			}
		}

	public function get ($key = null, $opts = null) {
		if (in_array ((string) $key, self::$K)) return parent::get ($key, $opts);
		switch ((string) $key) {
			case 'invoice_series':
				if ($this->invoice instanceof WP_CRM_Invoice)
					return $this->invoice->get ('series');
				break;
			case 'invoice_paid':
				if ($this->invoice instanceof WP_CRM_Invoice)
					return $this->invoice->get ('paid');
				break;
			case 'person_id':
				if ($this->person instanceof WP_CRM_Person) return $this->person->get ();
				return FALSE;
				break;
			case 'id':
			case '':
				return $this->ID;
				break;
			}

		if ($this->person instanceof WP_CRM_Person) return $this->person->get ($key, $opts);
		return $this->ID;
		}

	public function set ($key = null, $value = null) {
		if (in_array ((string) $key, self::$K))
			parent::set ($key, $value);
		else {
			if ($this->person instanceof WP_CRM_Person) {
				try {
					return $this->person->set ($key, $value);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					return FALSE;
					}
				}
			}
		}

	public function register ($product, $invoice = null) {
		global $wpdb;
		$pid = (int) ($product instanceof WP_CRM_Product ? $product->get () : $product);
		$iid = (int) ($invoice instanceof WP_CRM_Invoice ? $invoice->get () : $invoice);
		
		if (!isset ($this->products[$pid])) {
			$this->products[$pid] = $iid;
			$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '` (uid,iid,pid,stamp) values (%d,%d,%d,%d);', array (
				$this->data['uid'],
				$iid < 0 ? 0 : $iid,
				$pid,
				time ()
				));
			$wpdb->query ($sql);
			}
		else {
			if ($iid < 0) {
				unset ($this->products[$pid]);
				$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . static::$T . '` where uid=%d and pid=%d;', array (
					$this->data['uid'],
					$pid
					));
				$wpdb->query ($sql);
				}
			else {
				$this->products[$pid] = $iid;
				$sql = $wpdb->prepare ('update `' . $wpdb->prefix . static::$T . '` set iid=%d,stamp=%d where uid=%d and pid=%d;', array (
					$iid,
					time (),
					$this->data['uid'],
					$pid
					));
				$wpdb->query ($sql);
				}
			}
		}

	public function __destruct () {
		}
	};
?>
