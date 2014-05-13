<?php
#define (WP_CRM_Debug, TRUE);
//if ($_SERVER['REMOTE_ADDR'] == '86.125.6.22') define (WP_CRM_Debug, TRUE);
if (!defined(WP_CRM_Debug)) define (WP_CRM_Debug, FALSE);
define (WP_CRM_E_Payment, 5);
define (WP_CRM_Default_Seller, 1);
define (WP_CRM_URL, WP_PLUGIN_URL . '/' . basename(dirname(dirname(__FILE__))));
define (WP_CRM_DIR, dirname(dirname(__FILE__)));
define (WP_CRM_Cache, dirname(dirname(__FILE__)).'/cache');

class WP_CRM_Person {
	public $ID;
	private $data;
	private $keys;
	private $flags;
	private $errors;

/* table : wp_persons
uin,
first_name,
last_name,
email,
phone,
stamp,
flags
*/
	
	public function __construct ($data = array()) {
		global $wpdb;
		$this->errors = array ();

		$this->keys = array ('name', 'email', 'address', 'county', 'phone', 'uin', 'stamp', 'gender', 'age', 'birthday', 'happy_birthday', 'products', 'id_series', 'id_number', 'id_issuer', 'id_expire', 'id_place', 'id_father', 'id_mother', 'first_name', 'last_name');
		if (!is_object($data) && strpos($data, '@')) {
			$data = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where email like %s;', trim($data)), ARRAY_A);
			$this->ID = $data['id'];
			$this->data = array ();
			foreach ($this->keys as $key) if ($data[$key]) $this->data[$key] = $data[$key];
			$this->uin();
			}
		else
		if (!is_object($data) && strlen($data) > 11) {
			$data = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where uin=%d;', $data), ARRAY_A);
			$this->ID = $data['id'];
			$this->data = array ();
			foreach ($this->keys as $key) if ($data[$key]) $this->data[$key] = $data[$key];
			$this->uin();
			}
		else
		if (is_numeric($data)) {
			$this->ID = $data;
			$data = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where id=%d;', $this->ID), ARRAY_A);
			$this->data = array ();
			foreach ($this->keys as $key) if ($data[$key]) $this->data[$key] = $data[$key];
			$this->uin();
			if (WP_CRM_Debug) echo "WP_CRM_Person::_contruct::".print_r($data,TRUE)."\n";
			}
		else {
			if (is_object($data)) $data = (array) $data;
			if ($data['id']) $this->ID = $data['id'];
//			if ($data['uin'] && !$this->ID) $this->ID = $wpdb->get_var ($wpdb->prepare('select id from `'.$wpdb->prefix.'persons` where uin=%d;', $this->data['uin']));
			$this->data = array ();
			foreach ($this->keys as $key) if ($data[$key]) $this->data[$key] = $data[$key];
			$this->uin();
			if (WP_CRM_Debug) echo "uin: ".$this->data['uin']."\n";
			}
		$this->flags = (int) $data['flags'];
		}

	private function uin () {
		$uin = preg_replace('/[^0-9]+/','',$this->data['uin']);
		if (strlen($uin) != 13) { $this->errors[] = 'UIN number error'; return FALSE; }
		$gender = substr($uin,0,1);
		$year = substr($uin, 1, 2);
		$month = substr($uin, 3, 2);
		$day = substr($uin, 5, 2);

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

	public function add ($key = '', $val = '') {
		$_keys = array ();
		$_vals = array ();
		foreach ($this->data as $_key => $_val) if (in_array($_key, $this->keys)) { $_keys[] = $_key; $_vals[] = $_val; }
		}

	public function delete () {
		global $wpdb;
		if (!$this->ID) return FALSE;
		$sql = $wpdb->prepare ('delete from `'.$wpdb->prefix.'persons` where id=%d;', $this->ID);
		if (WP_CRM_Debug) echo "WP_CRM_Person::__construct::sql( $sql )\n";
		$wpdb->query ($sql);
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

	public function save () {
		global $wpdb;
		if ($this->ID) {
			$this->errors[] = 'user exists';
			return FALSE;
			}

		if ($this->data['uin']) {
			$instance = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where uin=%d;', $this->data['uin']), ARRAY_A);
			if (!empty($instance)) {
				$this->ID = $instance['id'];
				
				/*$data = array ();
				foreach ($this->keys as $key) {
					if (in_array ($key, array ('gender', 'age', 'birthday', 'happy_birthday', 'products'))) continue;
					$data[] = $wpdb->prepare ("$key=%s",(strlen($instance[$key]) < strlen($this->data[$key]) ? $instance[$key] : $this->data[$key]));
					}
				$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'persons` set '.implode(',',$data).' where id=%d;', $this->ID);
				if (WP_CRM_Debug)
				echo "WP_CRM_Person::save::sql ($sql)\n";
				$wpdb->query ($sql);*/
				return TRUE;
				}
			}
		else {
			$this->errors[] = 'missing UIN';
			return FALSE;
			}

		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'persons` (name,first_name,last_name,email,uin,phone,address,county,stamp,flags) values (%s,%s,%s,%s,%d,%s,%s,%s,%d,%d);',
		array (
			$this->data['name'],
			$this->data['first_name'],
			$this->data['last_name'],
			$this->data['email'],
			$this->data['uin'],
			$this->data['phone'],
			$this->data['address'],
			$this->data['county'],
			time(),
			0));
		if (WP_CRM_Debug)
		echo "WP_CRM_Person::save::sql ($sql)\n";

		if ($wpdb->query ($sql) === FALSE) {
			$this->errors[] = 'sql error: ('.$sql.')';
			return FALSE;
			}

		$this->ID = $wpdb->get_var ('select last_insert_id();');
		return TRUE;
		}
	public function get ($key = '', $value = '') {
		global $wpdb;
		if (!$key) return $this->ID;
		if ($key == 'keys') return $this->keys;
		if ($key == 'invoice') {
			$this->invoice($value);
			return $this->data['invoice'];
			}
		if ($key == 'data') return $this->data;
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
		if ($key == 'when') {
			if (!is_object($value)) return FALSE;
			return $wpdb->get_var ($wpdb->prepare ('select stamp from `'.$wpdb->prefix.'clients` where uin=%s and series=%s and number=%s;', $this->data['uin'], $value->get('current series'), $value->get('current number')));
			}
		if ($key == 'invoice') return $this->invoice($value);
		return FALSE;
		}
	public function is ($key, $value = '') {
		global $wpdb;
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
		}
	public function set ($key, $value = '') {
		global $wpdb;
		if ($key == 'paying customer') {
			$this->flags = $value ? ((intval($this->flags)) | 2) : ((intval($this->flags)) & (~2));
			if ($this->ID) {
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'persons` set flags=%d where id=%d;', $this->flags, $this->ID));
				}
			}
		if (in_array($key, $this->keys)) {
			$this->data[$key] = $value;
			if ($this->ID) {
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'persons` set '.$key.'=%s where id=%d;', $value, $this->ID));
				}
			}
		return FALSE;
		}

	public function errors () {
		if (empty($this->errors)) return FALSE;
		return implode ("\n", $this->errors);
		}

	public function __destruct () {
		}
	};

class WP_CRM_Client extends WP_CRM_Person {
/* table : wp_clients
iid,
uin,
pid,
series,
number,
flags
*/
	private $products;
	public function __construct ($data = array()) {
		if (is_object($data)) $data = (array) $data;
		parent::__construct ($data);
		$this->products = $data['products'];
		}
	public function add ($product, $invoice) {
		global $wpdb;
		if (!is_object($product)) return FALSE;
		if (!is_object($invoice)) return FALSE;

		$product_code = $product->get('active');
		$product_series = trim(preg_replace('/[^A-Z]+/','',$product_code));
		$product_number = intval(trim(preg_replace('/[^0-9]+/','',$product_code)));
		
		$wpdb->query ($wpdb->prepare ('insert into `'.$wpdb->prefix.'clients` (iid,uin,pid,series,number,flags) values (%d,%s,%d,%s,%d,%d);', $invoice->get('id'), $this->get(), $product->get(), $product_series, $product_number, 0));
		}
	public function del () {
		}
	public function save () {
		parent::save();
		}
	public function __destruct () {
		}
	};
	
class WP_CRM_Buyer {
	private $entity;
	private $entity_type;
	private $invoices;
	private $data;
	private $keys;
	public function __construct ($data) {
		$this->keys = array ('name', 'type', 'email', 'phone', 'uin', 'rc', 'company', 'address', 'bank', 'account');
		if (is_object($data)) {
			if (get_class($data) == 'WP_CRM_Person') {
				$this->entity_type = 'person';
				$this->entity = $data;
				}
			if (get_class($data) == 'WP_CRM_Company') {
				$this->entity_type = 'company';
				$this->entity = $data;
				}
			if (!is_object($this->entity)) {
				if (WP_CRM_Debug) echo "WP_CRM_Buyer::entity::error!\n";
				}
			else {
				if (!$this->entity->get()) {
					if (WP_CRM_Debug) echo "WP_CRM_Buyer::entity::save\n";
					$this->entity->save();
					}
				else {
					if (WP_CRM_Debug) echo "WP_CRM_Buyer::entity::".$this->entity->get()."\n";
					}
				}
			}
		}
	public function add ($key, $value = true) {
		$this->entity->add ($key, $value);
		}
	public function del () {
		}
	public function get ($key = '') {
		if ($key == 'type') return $this->entity_type;
		if ($key == 'entity') return $this->entity;
		return $this->entity->get($key);
		}
	public function save () {
		$this->entity->save();
		}
	public function __destruct () {
		}
	};

class WP_CRM_Company {
/*
table : wp_companies
name,
rc,
uin,
capital,
address,
account,
bank,
default_vat,
invoice_series,
invoice_number,
message,
flags
*/
	private $ID;
	private $persons;
	private $data;
	private $keys;
	private $default;
	private $seller;
	private $errors;

	public function __construct ($data = array()) {
		global $wpdb;
		$this->errors = array ();
		$this->default = false;
		$this->seller = false;
		$this->keys = array ('name', 'email', 'rc', 'uin', 'capital', 'address', 'county', 'phone', 'fax', 'bank', 'account', 'default_vat', 'invoice_series', 'invoice_number', 'message');
		$this->data = array ();

		if (is_numeric($data)) {
			$this->ID = $data;
			if (WP_CRM_Debug) echo "WP_CRM_Company::__contruct::ID=$data\n";
			$data = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'companies` where id=%d;', $this->ID), ARRAY_A);
			$this->default = $data['flags'] == 1 ? TRUE : FALSE;
			$this->seller = $data['flags'] > 0 ? TRUE : FALSE;
			}
		if (isset($data['uin'])) {
			if (WP_CRM_Debug) echo "WP_CRM_Company::__contruct::uin=".$data['uin']."\n";
			$temp = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'companies` where uin=%s;', $data['uin']), ARRAY_A);
			if (!empty($temp)) {
				$data = $temp;
				$this->ID = (int) $data['id'];
				$this->default = $data['flags'] == 1 ? TRUE : FALSE;
				$this->seller = $data['flags'] > 0 ? TRUE : FALSE;
				}
			}

		foreach ($this->keys as $key)
			$this->data[$key] = $data[$key];

		$this->persons = array ();
		}

	public function save () {
		global $wpdb;
		if ($this->ID) return;
		if ($this->data['uin']) {
			$this->data['uin'] = trim(preg_replace('/[^A-Z0-9]+/','',strtoupper($this->data['uin'])));
			$id = $wpdb->get_var ($wpdb->prepare('select id from `'.$wpdb->prefix.'companies` where uin=%s;', $this->data['uin']));
			if ($id) {
				echo $wpdb->prepare('select id from `'.$wpdb->prefix.'companies` where uin=%s;', $this->data['uin']);
				$this->ID = $id;
				$this->errors[] = 'company exists';
				return FALSE;
				}
			}

		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'companies` ('.implode(',', $this->keys).') values (\''.implode('\',\'', $this->data).'\');');
		if (WP_CRM_Debug) echo "WP_CRM_Company::save::sql( $sql )\n";
		if ($wpdb->query ($sql) === FALSE) {
			$this->errors[] = 'company save sql error ( '.$sql.' )';
			return FALSE;
			}
		$this->ID = $wpdb->get_var ('select last_insert_id();');
		return TRUE;
		}

	public function add ($person, $default = true) {
		if (is_object($person)) {
			$this->persons[] = $person;
			}
		}

	public function set ($key, $value = '') {
		if (!in_array($key, $this->keys)) return FALSE;
		if ($key == 'id') return FALSE;
		$data[$key] = $value;
		return TRUE;
		}

	public function get ($key = '') {
		if ($key == 'name') return $this->data['name'];
		if ($key == 'invoice_series') return $this->data['invoice_series'];
		if ($key == 'default_vat') return $this->data['default_vat'];
		if ($key == 'rc') return $this->data['rc'];
		if ($key == 'uin') return $this->data['uin'];
		if ($key == 'address') return $this->data['address'];
		if ($key == 'county') return $this->data['county'];
		if ($key == 'bank') return $this->data['bank'];
		if ($key == 'account') return $this->data['account'];
		if ($key == 'capital') return $this->data['capital'];
		if ($key == 'phone') return $this->data['phone'];
		if ($key == 'fax') return $this->data['fax'];
		if ($key == 'email') return $this->data['email'];
		if ($key == 'keys') return $this->keys;
		if ($key == 'type') return 'company';
		return $this->ID;
		}

	public function is ($key) {
		if ($key == 'default') return $this->default;
		if ($key == 'seller') return $this->seller;
		if ($key == 'buyer') return !$this->seller;
		return FALSE;
		}

	public function delete () {
		global $wpdb;
		if (!$this->ID) return FALSE;
		$sql = $wpdb->prepare ('delete from `'.$wpdb->prefix.'companies` where id=%d;', $this->ID);
		if (WP_CRM_Debug) echo "WP_CRM_Company::delete::sql( $sql )\n";
		$wpdb->query ($sql);
		}

	public function errors () {
		if (empty($this->errors)) return FALSE;
		return implode ("\n", $this->errors);
		}
	};

class WP_CRM_Product {
/*
table : wp_products
series,
number,
pid,
price,
vat,
stamp,
state
*/
	private $ID;
	private $keys;
	private $name;
	private $date;
	private $price;
	private $fullprice;
	private $vat;
	private $planning;
	private $current;
	private $responsible;
	private $errors;
	
	public function __construct ($data = array()) {
		global $wpdb;
		$this->keys = array ('nice name', 'nice date', 'nice hour', 'location address', 'location map', 'location directions');
		$this->errors = array ();
		if (is_numeric($data)) {
			$this->ID = $data;
			$this->name = get_the_title($this->ID);
			$this->price = get_post_meta($this->ID, WPSHOP_PRICE, TRUE);
			$this->price = floatval(str_replace(',','.',$this->price));
			$this->vat = get_post_meta($this->ID, WPSHOP_VAT, TRUE);
			if (!$this->price) $this->errors[] = 'no product price';
			$this->current = $wpdb->get_row($wpdb->prepare('select series,number,stamp,price,vat,rcost,lid from `'.$wpdb->prefix.'products` where pid=%d and state=1;', $this->ID), ARRAY_A);
			if (empty($this->current)) $this->errors[] = 'no current session';
			}
		else
		if ($data['series'] && $data['number']) {
			$this->ID = $wpdb->get_var ($wpdb->prepare ('select pid from `'.$wpdb->prefix.'products` where series=%s and number=%d;', $data['series'], $data['number']));
			if (WP_CRM_Debug) echo "WP_CRM_Product::__construct::sql ( ".$wpdb->prepare ('select pid from `'.$wpdb->prefix.'products` where series=%s and number=%d;', $data['series'], $data['number'])." )\n";
			$this->name = get_the_title($this->ID);
			$this->price = get_post_meta($this->ID, WPSHOP_PRICE, TRUE);
			$this->price = floatval(str_replace(',','.',$this->price));
			$this->vat = get_post_meta($this->ID, WPSHOP_VAT, TRUE);
			
			$this->current = $wpdb->get_row($wpdb->prepare('select series,number,stamp,price,vat,rcost,lid as location from `'.$wpdb->prefix.'products` where series=%s and number=%d;', $data['series'], $data['number']), ARRAY_A);
			}
		else {
			$this->name = $data['name'];
			$this->price = floatval(str_replace(',','.',$data['price']));
			$this->vat = $data['vat'];

			//$this->ID = $wpdb->get_var ($wpdb->prepare ('select ID from `'.$wpdb->prefix.'posts` where post_title=%s;', $this->name));
			}
		}

	public function save () {
		$this->ID = wp_insert_post (array (
			'post_title' => $this->name,
			'post_type' => 'post',
			'post_status' => 'private',
			'post_author' => 0,
			));
		add_post_meta ($this->ID, WPSHOP_PRICE, $this->price, TRUE);
		add_post_meta ($this->ID, WPSHOP_VAT, $this->vat, TRUE);
		}

	public function del ($trash = TRUE) {
		wp_delete_post ($this->ID, $trash ? FALSE : TRUE);
		}

	public function copy () {
		$data = array (
			'name' => $this->name,
			'price' => $this->price,
			'vat' => $this->vat,
			);
		return new WP_CRM_Product ($data);
		}

	public function is ($key) {
		global $wpdb;
		if ($key == 'trash') {
			return get_post_status($this->ID) == 'trash' ? TRUE : FALSE;
			}
		if ($key == 'active') {
			$sql = $wpdb->prepare ('select series,number from `'.$wpdb->prefix.'products` where pid=%d and state=1;', $this->ID);
			$active = $wpdb->query ($sql);
			return $active !== FALSE ? TRUE : FALSE;
			}
		}

	public function set ($key, $value = FALSE) {
		global $wpdb;
		if (is_array($key)) {
			}
		else {
			if ($key == 'name') {
				$this->name = $value;
				wp_update_post (array('ID' => $this->ID, 'post_title' => $value));
				}
			if ($key == 'price') {
				if (is_array($value)) {
					$this->price = $value['price'];
					$this->fullprice = $value['full'];
					$this->vat = $value['vat'];

					$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'product_prices` where pid=%d and stamp=%d;', $this->ID, $value['stamp']);
					if ($price = $wpdb->get_var($sql))
						$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'product_prices` set price=%f,full=%f,vat=%f where id=%d', array (
							$this->price,
							$this->fullprice,
							$this->vat,
							$price
							));
					else
						$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'product_prices` (pid,price,full,vat,stamp,ip) values (%d,%f,%f,%f,%d)', array (
							$this->ID,
							$this->price,
							$this->fullprice,
							$this->vat,
							$value['stamp'],
							$_SERVER['REMOTE_ADDR']
							));
					echo $sql;
					$wpdb->query ($sql);
					}
				else {
					$this->price = round((float) str_replace (',', '.', trim($value)),2);
					update_post_meta ($this->ID, WPSHOP_PRICE, $this->price);
					}
				}
			if ($key == 'vat') {
				$this->vat = round((float) str_replace (',', '.', trim($value)),2);
				update_post_meta ($this->ID, WPSHOP_VAT, $this->vat);
				}
			if ($key == 'full price') {
				$this->fullprice = round((float) str_replace (',', '.', trim($value)),2);
				$wpdb->prepare ('update `'.$wpdb->prefix.'products` set rcost=%f where series=%s and number=%d;', array (
					$this->fullprice,
					$this->current['series'],
					$this->current['number'],
					));
				}

			if ($key == 'instance name') {
				if (!$this->ID) return FALSE;
				if (!is_array($this->current)) return FALSE;
				$this->current['name'] = $value;
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set title=%s where series=%s and number=%d;', array (
					$this->current['name'],
					$this->current['series'],
					$this->current['number']
					)));
				return TRUE;
				}
			if ($key == 'responsible') {
				/* new =)
				if (!$this->ID) return FALSE;
				if (!is_array($this->current)) return FALSE;
				$this->responsible = intval($value);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set uid=%d where series=%s and number=%d;', array (
					$this->responsible,
					$this->current['series'],
					$this->current['number']
					)));
				return TRUE;
				*/
				$this->responsible = intval($value);
				if (!$this->ID) return FALSE;
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set uid=%d where pid=%d;', $this->responsible, $this->ID));
				return TRUE;
				}
			if ($key == 'location') {
				if (!$this->ID) return FALSE;
				if (!is_array($this->current)) return FALSE;
				$this->current['location'] = intval($value);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set lid=%d where series=%s and number=%d;', array (
					$this->current['location'],
					$this->current['series'],
					$this->current['number']
					)));
				return TRUE;
				}
			if ($key == 'structure') {
				if (!$this->ID) return FALSE;
				if (!is_array($this->current)) return FALSE;
				$this->current['struct'] = intval($value);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set struct=%d where series=%s and number=%d;', array (
					$this->current['struct'],
					$this->current['series'],
					$this->current['number']
					)));
				return TRUE;
				}
			if ($key == 'trainer') {
				if (!$this->ID) return FALSE;
				if (!is_array($this->current)) return FALSE;
				$this->current['trainer'] = intval($value);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set tid=%d where series=%s and number=%d;', array (
					$this->current['trainer'],
					$this->current['series'],
					$this->current['number']
					)));
				return TRUE;
				}
			}
		}

	public function get ($key = '', $value = null) {
		global $wpdb;
		if ((get_class($value) == 'WP_CRM_Invoice') && $this->ID) {
			if ($key == 'name') {
				$name = $wpdb->get_var ($wpdb->prepare ('select product from `'.$wpdb->prefix.'new_basket` where iid=%d and pid=%d and code=%s;', $value->get('id'), $this->ID, $this->get('current code')));
				return is_null($name) ? '' : $name;
				}
			if ($key == 'price') {
				$price = $wpdb->get_var ($wpdb->prepare ('select price from `'.$wpdb->prefix.'new_basket`  where iid=%d and pid=%d and code=%s;', $value->get('id'), $this->ID, $this->get('current code')));
				return is_null($price) ? 0.0 : ((float) round($price, 2));
				}
			if ($key == 'vat') {
				$vat = $wpdb->get_var ($wpdb->prepare ('select vat from `'.$wpdb->prefix.'new_basket`  where iid=%d and pid=%d and code=%s;', $value->get('id'), $this->ID, $this->get('current code')));
				return is_null($vat) ? 0.0 : ((float) round($vat, 2));
				}
			if ($key == 'vat value') {
				$product = $wpdb->get_row ($wpdb->prepare ('select price,vat from `'.$wpdb->prefix.'new_basket`  where iid=%d and pid=%d and code=%s;', $value->get('id'), $this->ID, $this->get('current code')));
				if (!$product) return 0.0;
				// hack;
				return (float) round($product->price * $product->vat / 100, 2);
				}
			if ($key == 'value') {
				$product = $wpdb->get_row ($wpdb->prepare ('select price,vat from `'.$wpdb->prefix.'new_basket`  where iid=%d and pid=%d and code=%s;', $value->get('id'), $this->get(), $this->get('current code')));
				if (!$product) return 0.0;
				return (float) round($product->price * (100 + $product->vat) / 100, 2);
				}
			return FALSE;
			}
		if (is_string ($value) && $this->ID) {
			if ($key == 'name') {
				$name = $wpdb->get_var ($wpdb->prepare ('select title from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				return is_null($name) ? $this->name : $name;
				}
			if ($key == 'location') {
				$location = $wpdb->get_var ($wpdb->prepare ('select lid from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				return $location;
				}
			if ($key == 'trainer') {
				$trainer = $wpdb->get_var ($wpdb->prepare ('select tid from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				return $trainer;
				}
			if ($key == 'responsible') {
				$responsible = $wpdb->get_var ($wpdb->prepare ('select uid from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				return $responsible;
				}
			if ($key == 'structure') {
				$structure = $wpdb->get_var ($wpdb->prepare ('select struct from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				return (int) $structure;
				}
			if ($key == 'participants number') return $wpdb->get_var($wpdb->prepare('select count(1) from `'.$wpdb->prefix.'clients` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number($value)));
			if ($key == 'paying number') {
				$out = 0;
				$participants = $wpdb->get_results($wpdb->prepare('select uin,iid from `'.$wpdb->prefix.'clients` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				foreach ($participants as $participant) {
					$invoice = new WP_CRM_Invoice (intval($participant->iid));
					$out += $invoice->is('paid') ? 1 : 0;
					}
				return $out;
				}
			if ($key == 'price') {
				return round((float) $wpdb->get_var ($wpdb->prepare ('select price from `'.$wpdb->prefix.'products` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number ($value))),2);
				}
			if ($key == 'vat') {
				return round((float) $wpdb->get_var ($wpdb->prepare ('select vat from `'.$wpdb->prefix.'products` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number ($value))),2);
				}
			if ($key == 'full price') {
				return round((float) $wpdb->get_var ($wpdb->prepare ('select rcost from `'.$wpdb->prefix.'products` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number ($value))),2);
				}
			}

		if (is_numeric($value)) {
			if ($key == 'price') {
				return round((float) $wpdb->get_var ($wpdb->prepare ('select price from `'.$wpdb->prefix.'product_prices` where pid=%d and code=%s and stamp<%d order by stamp desc limit 0,1;', array (
					$this->ID,
					$this->current['series'].str_pad($this->current['number'], 3, '0', STR_PAD_LEFT),
					(int) $value
					))));
				}
			if ($key == 'full price') {
				}
			}

		if ($key == 'keys') return $this->keys;
		if ($key == 'name') return $this->name;
		if ($key == 'nice name') return str_replace(array ('&#8211;'), array('-'), trim(preg_replace ('/\(.+\)/', '', $this->name)));
		if ($key == 'link') return get_permalink($this->ID);
		if ($key == 'price') return round($this->price,2);
		if ($key == 'full price') {
			return (float) $this->current['rcost'];
			}
		if ($key == 'vat') return round($this->vat,2);
		if ($key == 'vat value') return round($this->price * $this->vat / 100, 2);
		if ($key == 'value') return round($this->price * (100 + $this->vat) / 100, 2);
		if ($key == 'html value') {
			$val = round($this->price * (100 + $this->vat) / 100, 2);
			$dec = intval(100*($val - intval($val)));
			return intval($val).'<sup>'.($dec < 10 ? "0$dec" : $dec).'</sup>';
			}
		if ($key == 'html full value') {
			$val = round($this->current['rcost'] * (100 + $this->vat) / 100, 2);
			$dec = intval(100*($val - intval($val)));
			return intval($val).'<sup>'.($dec < 10 ? "0$dec" : $dec).'</sup>';
			}
		if ($key == 'planning') {
			$this->planning = array();
			$plans = $wpdb->get_results($wpdb->prepare('select series,number,stamp from `'.$wpdb->prefix.'products` where pid=%d order by stamp;', $this->ID));
			foreach ($plans as $plan)
				$this->planning[$plan->series.str_pad($plan->number, 3, '0', STR_PAD_LEFT)] = $plan->stamp;
			return $this->planning;
			}
		if ($key == 'price list') {
			$out = array ();
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'product_prices` where pid=%d order by stamp;', $this->ID);
			$rows = $wpdb->get_results ($sql, ARRAY_A);
			return $rows;
			}
		if ($key == 'interval') {
			$e_stamp = $wpdb->get_var($wpdb->prepare('select stamp from `'.$wpdb->prefix.'products` where pid=%d and stamp>unix_timestamp() order by stamp limit 0,1;', $this->ID));
			$b_stamp = $wpdb->get_var($wpdb->prepare('select stamp from `'.$wpdb->prefix.'products` where pid=%d and stamp<%d order by stamp desc limit 0,1;', $this->ID, $e_stamp));
			return array (
				'begin' => $b_stamp ? $b_stamp : 0,
				'end' => $e_stamp ? $e_stamp : 0
				);	
			}
		if ($key == 'active') {
			if ($this->current) return $this->current['series'].str_pad($this->current['number'], 3, '0', STR_PAD_LEFT);
			$plan = $wpdb->get_row($wpdb->prepare('select series,number from `'.$wpdb->prefix.'products` where pid=%d and state=1;', $this->ID));
			if ($plan)
				return $plan->series.str_pad($plan->number, 3, '0', STR_PAD_LEFT);

			return FALSE;
			}
		if ($key == 'potential') {
			$out = 0;
			$items = $wpdb->get_results($wpdb->prepare('select price,vat from `'.$wpdb->prefix.'new_basket` where pid=%d and code=%s;', $this->ID, $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT)));
			foreach ($items as $item)
				$out += round(((float) $item->price) * (100 + (float) $item->vat) / 100, 2);
			return $out;
			}
		if ($key == 'invoices') {
			$out = array ();
			$invoices = $wpdb->get_col($wpdb->prepare('select iid from `'.$wpdb->prefix.'new_basket` where pid=%d and code=%s;', $this->ID, $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT)));
			foreach ($invoices as $invoice)
				$out[] = new WP_CRM_Invoice($invoice);
			return $out;
			}
		if ($key == 'planning invoices') {
			$out = array ();
			$rows = $wpdb->get_results($wpdb->prepare('select iid,code from `'.$wpdb->prefix.'new_basket` where pid=%d;', $this->ID));
			foreach ($rows as $row) {
				if (!isset($out[$row->code])) $out[$row->code] = array ();
				$out[$row->code][] = new WP_CRM_Invoice($row->iid);
				}
			return $out;
			}
		if ($key == 'income') {
			$out = 0;
			$items = $wpdb->get_results($wpdb->prepare('select iid,price,vat from `'.$wpdb->prefix.'new_basket` where pid=%d and code=%s;', $this->ID, $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT)));
			foreach ($items as $item) {
				$invoice = new WP_CRM_Invoice ($item->iid);
				if (!$invoice->is('paid')) continue;
				$out += round(((float) $item->price) * (100 + (float) $item->vat) / 100, 2);
				}
			return $out;
			}
		if ($key == 'income invoices') {
			$out = array ();
			$invoices = $wpdb->get_col($wpdb->prepare('select iid from `'.$wpdb->prefix.'new_basket` where pid=%d and code=%s;', $this->ID, $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT)));
			foreach ($invoices as $invoice) {
				$invoice = new WP_CRM_Invoice ($invoice);
				if (!$invoice->is('paid')) continue;
				$out[] = $invoice;
				}
			return $out;
			}
		if ($key == 'current series') return is_array($this->current) ? $this->current['series'] : FALSE;
		if ($key == 'icon' || $key == 'current icon') return is_array($this->current) ? substr($this->current['series'], 0, 3) : ($this->name != 'Discount' ? 'UNK' : '');
		if ($key == 'current number') return is_array($this->current) ? $this->current['number'] : FALSE;
		if ($key == 'current stamp' || $key == 'current date') return is_array($this->current) ? $this->current['stamp'] : FALSE;
		if ($key == 'nice date') return is_array($this->current) ? str_replace (array (
			'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun',
			'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
			), array (
			'luni', 'marti', 'miercuri', 'joi', 'vineri', 'sambata', 'duminica',
			'ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie', 'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie'
			), date ('D, j M Y', $this->current['stamp'])) : FALSE;
		if ($key == 'nice hour') return is_array($this->current) ? date ('H<\s\u\p>i</\s\u\p>', $this->current['stamp']) : FALSE;

		if ($key == 'location') return is_array($this->current) ? $this->current['location'] : FALSE;
		if ($key == 'location address') {
			if (!is_array($this->current)) return FALSE;
			$sql = $wpdb->prepare ('select address from `'.$wpdb->prefix.'product_locations` where id=%d;', $this->current['location']);
			return $wpdb->get_var ($sql);
			}

		if ($key == 'location directions') {
			if (!is_array($this->current)) return FALSE;
			$sql = $wpdb->prepare ('select directions from `'.$wpdb->prefix.'product_locations` where id=%d;', $this->current['location']);
			return $wpdb->get_var ($sql);
			}

		if ($key == 'location map') {
			if (!is_array($this->current)) return FALSE;
			$sql = $wpdb->prepare ('select map from `'.$wpdb->prefix.'product_locations` where id=%d;', $this->current['location']);
			return $wpdb->get_var ($sql);
			}

		if ($key == 'current code') return is_array($this->current) ? $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT) : FALSE;
		if ($key == 'responsible') {
			if ($this->responsible) return $this->responsible;
			if (!$this->ID) return FALSE;
			$this->responsible = $wpdb->get_var ($wpdb->prepare('select uid from `'.$wpdb->prefix.'products` where pid=%d;', $this->ID));
			return $this->responsible ? $this->responsible : FALSE;
			}

		if ($key == 'participants number') return (int) $wpdb->get_var($wpdb->prepare('select count(1) from `'.$wpdb->prefix.'clients` where series=%s and number=%d;', $this->current['series'], $this->current['number']));
		if ($key == 'paying number') {
			$out = 0;
			$participants = $wpdb->get_results($wpdb->prepare('select uin,iid from `'.$wpdb->prefix.'clients` where series=%s and number=%d;', $this->current['series'], $this->current['number']));
			foreach ($participants as $participant) {
				$invoice = new WP_CRM_Invoice (intval($participant->iid));
				$out += $invoice->is('paid') ? 1 : 0;
				}
			return (int) $out;
			}
		if ($key == 'participants') {
			$out = array ();
			$participants = $wpdb->get_col($wpdb->prepare('select uin from `'.$wpdb->prefix.'clients` where series=%s and number=%d;', $this->current['series'], $this->current['number']));
			foreach ($participants as $participant) $out[] = new WP_CRM_Person ($participant);
			return $out;
			}
		if ($key == 'days left') return $this->current['stamp'] > time() ? ceil(($this->current['stamp'] - time())/86400) : '-';
		return $this->ID;
		}

	public function plan ($series, $date) {
		global $wpdb;
		$series = strtoupper($series);
		$number = 1 + $wpdb->get_var($wpdb->prepare('select max(number) from `'.$wpdb->prefix.'products` where pid=%d;', $this->ID));
		$responsible = $wpdb->get_var($wpdb->prepare('select uid from `'.$wpdb->prefix.'products` where pid=%d and uid>0 order by id desc limit 0,1;', $this->ID));
		$stamp = strtotime($date);
		$sql = $wpdb->prepare('insert into `'.$wpdb->prefix.'products` (series,number,pid,uid,stamp) values (%s,%d,%d,%d,%d);', array (
			$series,
			$number,
			$this->ID,
			$responsible ? $responsible : 0,
			$stamp));
		echo "WP_CRM_Product::plan::sql( $sql )\n";
		$wpdb->query($sql);
		}

	public function modify ($id, $date) {
		global $wpdb;
		$stamp = strtotime($date);
		$sql = $wpdb->prepare('update `'.$wpdb->prefix.'products` set stamp=%d where series=%s and number=%d and pid=%d;', array (
			$stamp,
			wp_crm_extract_series($id),
			wp_crm_extract_number($id),
			$this->ID
			));
		echo $sql;
		$wpdb->query($sql);
		return $stamp;
		}

	public function change ($id, $date) {
		global $wpdb;
		return $this->modify ($id, $date);
		}

	public function cancel ($id) {
		global $wpdb;
		$id = strtoupper($id);
		$series = preg_replace('/[0-9]+/','',$id);
		$number = intval(preg_replace('/[^0-9]+/','',$id));
		$sql = $wpdb->prepare ('delete from `'.$wpdb->prefix.'products` where series=%s and number=%d and id=%d;', $series, $number, $this->ID);
		}

	public function active ($id) {
		global $wpdb;
		$id = strtoupper($id);
		$series = preg_replace('/[0-9]+/','',$id);
		$number = intval(preg_replace('/[^0-9]+/','',$id));
		return $wpdb->get_var ($wpdb->prepare ('select state from `'.$wpdb->prefix.'products` where series=%s and number=%s;', $series, $number)) ? TRUE : FALSE;
		}

	public function activate ($id) {
		global $wpdb;
		$id = strtoupper($id);
		$series = preg_replace('/[0-9]+/','',$id);
		$number = intval(preg_replace('/[^0-9]+/','',$id));
		$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set state=0 where series=%s', $series));
		$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set state=1 where series=%s and number=%s', $series, $number));
		}

	public function deactivate ($id) {
		global $wpdb;
		$id = strtoupper($id);
		$series = preg_replace('/[0-9]+/','',$id);
		$number = intval(preg_replace('/[^0-9]+/','',$id));
		$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'products` set state=0 where series=%s and number=%s', $series, $number));
		}

	public function add ($participant, $invoice = null, $paid = true) {
		global $wpdb;
		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'clients` (iid,uin,pid,series,number,stamp,flags) values (%d,%d,%d,%s,%d,%d,%d);', array (
			is_object($invoice) ? $invoice->get('id') : 0,
			$participant->get('uin'),
			$this->ID,
			$this->get('current series'),
			$this->get('current number'),
			time(),
			0
			));
		if (WP_CRM_Debug) echo "WP_CRM_Product::add::sql( $sql )\n";
		if ($wpdb->query ($sql) === FALSE) return FALSE;
		return TRUE;
		}

	public function errors () {
		if (empty($this->errors)) return FALSE;
		return implode ("\n", $this->errors);
		}

	public function __destruct () {
		}
	};
	
class WP_CRM_Basket {
/*
table : wp_new_basket
bid,
pid,
product,
iid,
price,
quantity,
stamp,
flags
*/
	private $ID;
	private $products;
	
	public function __construct ($data = array()) {
		global $wpdb;
		if (is_numeric($data)) {
			$this->products = array();
			$basket = $wpdb->get_results($wpdb->prepare('select * from `'.$wpdb->prefix.'new_basket` where iid=%d;', $data));
			if (!empty($basket))
			foreach ($basket as $item) {

				if ($item->code) {
					$id = strtoupper($item->code);
					$series = wp_crm_extract_series($id);
					$number = wp_crm_extract_number($id);
		
					$product = new WP_CRM_Product (array('series' => $series, 'number' => $number));
					}
				else
					$product = new WP_CRM_Product (array('name' => $item->product, 'price' => $item->price, 'vat' => $item->vat));

				$this->products[] = array (
					'product' => $product,
					'quantity' => $item->quantity,
					);
				}
			}
		else {
			$this->products = array();
			}
		}

	public function copy ($basket) {
		$this->products = $basket->get('products');
		}

	public function save ($invoice_id) {
		global $wpdb;
		if (!empty($this->products))
		foreach ($this->products as $product) {
			$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'new_basket` (bid,pid,code,product,iid,price,vat,quantity,stamp,flags) values (%d,%d,%s,%s,%d,%f,%f,%d,%d,%d);',
				array (
				$this->buyer->get(),
				$product['product']->get(),
				$product['product']->get('active'),
				$product['product']->get('name'),
				$invoice_id,
				$product['product']->get('price'),
				$product['product']->get('vat'),
				$product['quantity'],
				$this->get('date'),
				1));
			$wpdb->query ($sql);
			}
		}

	public function add ($product, $quantity) {
		if (!isset($this->products[$product->get()]))
			$this->products[$product->get()] = array (
				'product' => $product,
				'quantity' => $quantity,
				);
		else {
			$this->products[$product->get()]['quantity'] += $quantity;
			if (!$this->products[$product->get()]['quantity'])
				$this->products[$product->get()] = NULL;
			}
		}

	public function change ($product, $quantity) {
		if (WP_CRM_Debug) echo "WP_CRM_Basket::change::products( ".print_r($this->products, TRUE)." )\n";
		if (isset($this->products[$product->get()]))
			$this->products[$product->get()]['quantity'] = $quantity;
		else
			$this->products[$product->get()] = array (
				'product' => $product,
				'quantity' => $quantity,
				);
		if (WP_CRM_Debug) echo "WP_CRM_Basket::change::products( ".print_r($this->products, TRUE)." )\n";
		}

	public function set ($key, $value = FALSE) {
		}

	public function get ($key = '') {
		if ($key == 'responsible') {
			if (empty($this->products)) return FALSE;
			$tmp = array ();
			foreach ($this->products as $product) {
				if ($product['product']->get('current stamp'))
					$tmp[$product['product']->get('current stamp')] = $product['product']->get('responsible');
				}

			ksort ($tmp);
			reset ($tmp);

			return current($tmp);
			}
		return $this->products;
		}

	public function __destruct () {
		}
	};

class WP_CRM_Receipt {
	private $ID;
	private $series;
	private $number;
	private $invoice;
	private $value;
	private $date;

	public function __construct ($data) {
		global $wpdb;
		if (is_numeric($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'receipts` where id=%d;', $data);
			$receipt = $wpdb->get_row ($sql);
			$this->ID = $receipt->id;
			$this->series = $receipt->series;
			$this->number = $receipt->number;
			$this->invoice = new WP_CRM_Invoice ($receipt->iid);
			$this->value = (float) $receipt->value;
			$this->date = (int) $receipt->stamp;
			}
		else
		if (is_string($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'receipts` where series=%s and number=%d;', wp_crm_extract_series($data), wp_crm_extract_number($data));
			$receipt = $wpdb->get_row ($sql);
			$this->ID = $receipt->id;
			$this->series = $receipt->series;
			$this->number = $receipt->number;
			$this->invoice = new WP_CRM_Invoice ($receipt->iid);
			$this->value = (float) $receipt->value;
			$this->date = (int) $receipt->stamp;
			}
		else
		if (is_array($data)) {
			if (is_numeric($data['invoice'])) $data['invoice'] = new WP_CRM_Invoice ((int) $data['invoice']);
			if (is_object($data['invoice']) && is_numeric($data['value'])) {
				$this->series = 'R'.$data['invoice']->get('invoice_series');
				$this->invoice = $data['invoice'];
				$this->value = (float) $data['value'];
				$this->date = $data['date'] ? (int) $data['date'] : time(); 
				}
			}
		}

	public function get ($key) {
		if ($key == 'value') return (float) $this->value;
		if ($key == 'invoice') return (float) $this->invoice;
		if ($key == 'stamp' || $key == 'time') return (int) $this->date;
		if ($key == 'code') return $this->series.str_pad($this->number, 5, STR_PAD_LEFT);
		if ($key == 'series') return $this->series;
		if ($key == 'number') return $this->number;
		return $this->ID;
		}

	public function save () {
		global $wpdb;
		$cache = dirname(dirname(__FILE__)).'/cache/series';
		if ($this->ID) return FALSE;

		if (file_exists($cache . '/' . $this->series.'.num'))
			$this->number = intval(file_get_contents($cache.'/'.$this->series.'.num')) + 1;
		else
			$this->number = 1;
		file_put_contents($cache.'/'.$this->series.'.num', $this->number);

		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'receipts` (series,number,value,iid,stamp) values (%s,%d,%f,%d,%d);', array (
			$this->series,
			$this->number,
			$this->value,
			$this->invoice->get(),
			$this->date
			));
		if (WP_CRM_Debug) echo "WP_CRM_Receipt::save::sql( $sql )\n";
		$wpdb->query ($sql);
		return TRUE;
		}

	public function __destruct () {
		}
	};
 
class WP_CRM_Invoice extends WP_CRM_Basket {
/*
table : wp_new_invoices
sid,
bid,
buyer,
series,
stamp,
value,
vat,
content,
paidby,
paidvalue,
paiddate,
paiddetails,
flags
*/
	private $ID;
	private $keys;

	private $date;
	public $seller;
	public $buyer;
	public $delegate;
	private $real;		# proforma?	flags & 1
	private $paid;		# array

	private $parent;	# iid
	private $storno;	# is a storno	flags & 2
	private $discount;	# has discount	flags & 4
	private $advance;	# advance	flags & 8
	private $receipts;	# array

	private $ip;
	private $cookie;
	private $source;
	
	public $series;
	public $number;
	
	public function __construct ($data = array()) {
		global $wpdb;
		$this->keys = array ('paid value', 'invoice_series', 'invoice_number', 'value');
		$this->products = array();
		if (is_string($data)) {
			$series = wp_crm_extract_series ($data);
			$number = wp_crm_extract_number ($data);
			if ($series && $number) {
				$invoice_id = $wpdb->get_var ($wpdb->prepare ('select id from `'.$wpdb->prefix.'new_invocies` where series=%s and number=%d;', $series, $number));
				if ($invoice_id)
					$data = intval($invoice_id);
				}
			}
		if (is_numeric($data)) {
			$this->ID = $data;
			$invoice = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID));

			parent::__construct ($this->ID);

			$this->date = intval($invoice->stamp);
		
			$buyer = $invoice->buyer == 'person' ?
				new WP_CRM_Person ($invoice->bid) :
				new WP_CRM_Company ($invoice->bid);
			$this->buyer = new WP_CRM_Buyer ($buyer);
			$this->seller = new WP_CRM_Company ($invoice->sid);

			if ($this->buyer->get('type') == 'person') $this->delegate = $buyer;
			else $this->delegate = new WP_CRM_Person ($invoice->did);
			
			if (!$this->delegate->get()) $this->delegate->save();

			$this->paid = array (
				'by' => $invoice->paidby,
				'date' => $invoice->paiddate,
				'details' => $invoice->paiddetails,
				'value' => $invoice->paidvalue,
				);

			$this->series = $invoice->series;
			$this->number = $invoice->number;

			$this->cookie = (int) $invoice->cookie;
			$this->ip = $invoice->ip;
			$this->source = $invoice->source;

			$this->real = (((int) $invoice->flags)&1) == 1 ? TRUE : FALSE;
			$this->storno = (((int) $invoice->flags)&2) == 2 ? TRUE : FALSE;
			$this->discount = (((int) $invoice->flags)&4) == 4 ? TRUE : FALSE;
			$this->advance = (((int) $invoice->flags)&8) == 8 ? TRUE : FALSE;

			$receipts = new WP_CRM_List ('receipts', array ('invoice' => $this));
			if (!$receipts->is('empty')) {
				$receipts->sort ('time', 'desc');
				$this->receipts = $receipts->get();
				}
			
			$this->parent = $invoice->iid;
			}
		else
		if (is_object($data['buyer']) && is_object($data['basket'])) {
			$this->buyer = $data['buyer'];

			if (is_object($data['seller']))
				$this->seller = $data['seller'];
			else
				$this->seller = new WP_CRM_Company (WP_CRM_Default_Seller);

			if (is_object($data['delegate'])) {
				$this->delegate = $data['delegate'];
				if (!$this->delegate->get()) $this->delegate->save();
				}

			parent::copy($data['basket']);
			$this->date = time();
			$this->discount = TRUE;
			$this->real = FALSE;
			$this->paid = FALSE;
			}
		else {
			if (is_object($data)) $data = (array) $data;
			parent::__construct ($data);
			$this->date = time();
			$this->discount = TRUE;
			$this->real = FALSE;
			$this->paid = FALSE;
			}
		}

	public function copy () {
		$basket = new WP_CRM_Basket ();
		$basket->copy($this);
		$clone = new WP_CRM_Invoice (array (
			'buyer' => $this->buyer,
			'seller' => $this->seller,
			'delegate' => $this->delegate,
			'basket' => $basket,
			));
		return $clone;
		}

	public function add ($product, $quantity) {
		global $wpdb;

		parent::add ($product, $quantity);

		if ($this->ID) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'new_basket` where iid=%d and pid=%d;', array (
				$this->ID,
				$product->get()));
			if (WP_CRM_Debug) echo "WP_CRM_Invoice::add::sql( $sql )\n";
			$invoice_product = $wpdb->get_row ($sql);
			if ($invoice_product)
				$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'new_basket` set quantity=%d where iid=%d and pid=%d;', array (
					$invoice_product->quantity + intval($quantity),
					$this->ID,
					$product->get()));
			else
				$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'new_basket` (bid,pid,code,product,iid,price,vat,quantity,stamp,flags) values (%d,%d,%s,%s,%d,%f,%f,%d,%d,%d);', array (
					$this->buyer->get(),
					$product->get(),
					$product->get('current code'),
					$product->get('name'),
					$this->ID,
					$product->get('price'),
					$product->get('vat'),
					intval($quantity),
					time(),
					0));
			
			if (WP_CRM_Debug) echo "WP_CRM_Invoice::add::sql( $sql )\n";
			$wpdb->query ($sql);
			}
		}

	public function change ($product, $quantity) {
		global $wpdb;

		if (isset($this->products[$product->get()]))
			$this->products[$product->get()]['quantity'] = $quantity;
		else
			$this->products[$product->get()] = array (
				'product' => $product,
				'quantity' => $quantity,
				);

		if ($this->ID) {
			if (WP_CRM_Debug) echo "WP_CRM_Invoice::change::$this->ID\n";
			if (!empty($this->products)) {
				foreach ($this->products as $product) {
					$quantity = $product['quantity'];
					$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'new_basket` where iid=%d and code=%s;', array (
						$this->ID,
						$product['product']->get('current code')));
					if (WP_CRM_Debug) echo "WP_CRM_Invoice::change::sql( $sql )\n";
					$basket = $wpdb->get_var ($sql);

					$sql = '';
					if ($basket) {
						if ($product['quantity'])
							$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'new_basket` set quantity=%d where id=%d;', array (
								$product['quantity'],
								$basket	));
						else
							$sql = $wpdb->prepare ('delete from `'.$wpdb->prefix.'new_basket` where id=%d;', $basket);
						}
					else {
						if ($product['quantity'])
							$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'new_basket` (bid,pid,code,product,iid,price,vat,quantity,stamp,flags) values (%d,%d,%s,%s,%d,%f,%f,%d,%d,%d);', array (
								$this->buyer->get(),
								$product['product']->get(),
								$product['product']->get('current code'),
								$product['product']->get('name'),
								$this->ID,
								$product['product']->get('price'),
								$product['product']->get('vat'),
								intval($quantity),
								time(),
								0));
						}

					if ($sql) {
						if (WP_CRM_Debug) echo "WP_CRM_Invoice::change::sql( $sql )\n";
						$wpdb->query ($sql);
						}
					}
				}
			}
		}

	public function pay ($paid = array()) {
		global $wpdb;
		$cache = dirname(dirname(__FILE__)).'/cache/series';
		if (!$this->ID) return FALSE;

		if (!$paid['paid value']) $paid['paid value'] = ($this->storno ? -1 : 1) * $this->get('value');

		if (abs($paid['paid value'] - $this->get('value')) < WP_CRM_E_Payment) {
			$this->set('real', TRUE);	# asta e o factura fiscala
				
			# paid total
			if ($paid['paid by'] == 'cash') {
				$receipt = new WP_CRM_Receipt (array (
					'invoice' => $this,
					'value' => (float) $paid['paid value'],
					'date' => $paid['paid date']
					));
				$receipt->save ();
				$this->receipts[] = $receipt;

				$paid['paid by'] = 'cash';
				$paid['paid details'] = $receipt->get('code');
				}

			if (!empty($paid)) {
				$this->paid['by'] = $paid['paid by'];
				$this->paid['value'] = $paid['paid value'];
				$this->paid['date'] = $paid['paid date'];
				$this->paid['details'] = $paid['paid details'];
				}

			if (is_object($receipt)) {
				$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set receipt=%s,paidby=%s,paidvalue=%f,paiddate=%d,paiddetails=%s where id=%d;', array (
					$receipt->get('code'),
					$this->paid['by'],
					$this->paid['value'],
					$this->paid['date'],
					$this->paid['details'],
					$this->ID
					));
				}
			else
				$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set paidby=%s,paidvalue=%f,paiddate=%d,paiddetails=%s where id=%d;', array (
					$this->paid['by'],
					$this->paid['value'],
					$this->paid['date'],
					$this->paid['details'],
					$this->ID
					));

			$wpdb->query ($sql);
			}
		else {
			if (abs($paid['paid value'] + $this->get('paid value') - $this->get('value')) > WP_CRM_E_Payment) {
				if (!$this->parent)		# second invoice is always virtual!!!
					$this->set ('real', TRUE);
				# first step of the payment
				if ($paid['paid by'] == 'cash') {
					# first step, cash payment
					
					$receipt = new WP_CRM_Receipt (array (
						'invoice' => $this,
						'value' => (float) $paid['paid value'],
						'date' => $paid['paid date']
						));
					$receipt->save ();
					$this->receipts[] = $receipt;

					if (WP_CRM_Debug || TRUE) echo "WP_CRM_Invoice::pay=*part*cash*first*copy\n";
					# copy the invoice!
					$invoice = $this->copy();
					$invoice->set ('parent', $this->ID);
					$invoice->save ();
					}
				else {
					# first step, normal payment
					}
				$this->paid['by'] = $paid['paid by'];
				$this->paid['value'] = $paid['paid value'];
				$this->paid['date'] = $paid['paid date'];
				$this->paid['details'] = $paid['paid details'];
				}
			else {
				# last step of the payment
				$invoices = $this->get('children');
				if ($paid['paid by'] == 'cash') {
					if (empty($invoices)) {
						# previous invoice was not paid cash, so we can attach the receipt
						$receipt = new WP_CRM_Receipt (array (
							'invoice' => $this,
							'value' => (float) $paid['paid value'],
							'date' => $paid['paid date']
							));
						$receipt->save ();
						$this->receipts[] = $receipt;
						}
					else {
						# previous invoice was paid cash, this one also
						foreach ($invoices as $invoice) $invoice->pay ($paid);
						}
					}
				else {
					if (empty($invoices)) {
						# previous invoice was not paid cash, so we don't bother with anything
						}
					else {
						# previous invoice was paid cash, so we need to delete the virtual invoice
						foreach ($invoices as $invoice) $invoice->delete();
						}
					}

				$this->paid['by'] = $paid['paid by'];
				$this->paid['value'] += $paid['paid value']; # in any case, add the difference!
				$this->paid['date'] = $paid['paid date'];
				$this->paid['details'] = $paid['paid details'];
				}
		
			if (is_object($receipt))	
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set paidby=%s,paidvalue=%f,paiddate=%d,paiddetails=%s,receipt=%s where id=%d;', array (
					$this->paid['by'],
					floatval($this->paid['value']),
					intval($this->paid['date']),
					$this->paid['details'],
					$receipt,
					$this->ID,
					)));
			else
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set paidby=%s,paidvalue=%f,paiddate=%d,paiddetails=%s where id=%d;', array (
					$this->paid['by'],
					floatval($this->paid['value']),
					intval($this->paid['date']),
					$this->paid['details'],
					$this->ID,
					)));
			}

		}
	
	public function save () {
		global $wpdb;

		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'new_invoices` (sid,bid,did,iid,buyer,series,number,stamp,value,vat,flags) values (%d,%d,%d,%d,%s,%s,%d,%d,%f,%f,%d);',
		array (
			$this->seller->get(),
			$this->buyer->get(),
			is_object($this->delegate) ? $this->delegate->get() : 0,
			$this->parent ? $this->parent : 0,
			$this->buyer->get('type'),
			$this->get('invoice_series'),
			$this->get('invoice_number'),
			$this->date,
			$this->value,
			$this->vat,
			($this->discount ? 4 : 0) | ($this->storno ? 2 : 0) | ($this->real ? 1 : 0)));

		$wpdb->query ($sql);
		$this->ID = $wpdb->get_var ($wpdb->prepare ('select last_insert_id();'));
	
		parent::save ($this->ID);
		}
	
	public function view ($echo = TRUE) {
		global $wpdb, $current_user;

		if ($this->discount) $this->discount();

		if (!empty($this->receipts)) { $INVOICE_BOTTOM = 158; $INVOICE_ROWS = 11; }
		else { $INVOICE_BOTTOM = 220; $INVOICE_ROWS = 21; }

		$pdf = new PDF();

		$pdf->style ('h1');
		$pdf->Cell (0, 10, 'FACTURA' . ($this->real ? ' FISCALA' : ' PROFORMA'));
		$pdf->style ();
		$pdf->Ln ();
		$pdf->Cell (0, 5, 'Seria si numarul facturii '.($this->real ? '' : '(a se mentiona in momentul platii)').': ');
		$pdf->Ln ();
		$pdf->style ('h2');
		$pdf->style ('color: red');
		$pdf->Cell (0, 7, $this->get('invoice_series').' '.$this->get('invoice_number'));
		$pdf->style ();
		$pdf->Ln ();
		$pdf->Cell (0, 5, 'Data emiterii: '.date('d-m-Y', $this->date));
		$pdf->Ln ();
		$pdf->Line (11, 40, 199, 40);
		$pdf->Ln ();

		$pdf->Image (dirname(dirname(__FILE__)).'/images/companies/1.png', 140, 10, 60, 30);

		$pdf->columns (5, array ('Vanzator:', 'Cumparator:'));
		$pdf->style ('h3');
		$pdf->columns (8, array ($this->seller->get('name'), $this->buyer->get('name')));
		$pdf->style ();

		$data = array (
			'rc' => 'Nr. ord. reg. com. / an:',
			'uin' => 'CIF / CNP:',
			'default_vat' => 'Cota TVA (%):',
			'address' => 'Adresa:',
			'capital' => 'Capital social (lei):',
			'phone' => 'Telefon:',
			'email' => 'Email:',
			'bank' => 'Banca:',
			'account' => 'Cont:'
			);

		$seller_keys = array_keys ($data);
		$buyer_keys = array_keys ($data);

		while (($seller_key = current($seller_keys)) !== FALSE) {
			$buyer_key = current($buyer_keys);
			while (($buyer_key !== FALSE) && (!$this->buyer->get($buyer_key))) $buyer_key = next($buyer_keys);
			$pdf->columns (4, array ($data[$seller_key].' '.$this->seller->get($seller_key), $buyer_key ? $data[$buyer_key].' '.$this->buyer->get($buyer_key) : ''));
			next ($seller_keys);
			next ($buyer_keys);
			}

		$pdf->Line (11, 93, 199, 93);
		$pdf->Ln ();

		$table_head = array (
			array (
				'Nr.' => 6,
				'Denumirea produselor' => 70,
				'U.M.' => 10,
				'Cant.' => 10,
				'Pret unitar' => 20,
				'Valoare' => 15,
				'Cota' => 15,
				'Valoare TVA' => 20,
				'Valoare totala' => 0 ),
			array (
				'Crt.',
				'',
				'',
				'',
				'(fara TVA)',
				'(fara TVA)',
				'TVA',
				'',
				'(incl. TVA)'
				),
			array (
				'',
				'',
				'',
				'',
				'- LEI -',
				'- LEI -',
				'%',
				'- LEI -',
				'- LEI -'
				)
			);

		$table_rows = array (
			);
		$table_totals = array (
			'value' => 0.0,
			'vat' => 0.0,
			'vat value' => 0.0,
			'total value' => 0.0
			);

		$c = 1;

		$vat_singularity = FALSE;

		$sign = $this->storno ? -1 : 1;

		foreach (($this->get('products')) as $product) {
			if (!$product['product']->get('vat', $this)) $vat_singularity = TRUE;
			$ump = $sign * $product['product']->get('price', $this);
			$val = $sign * $product['product']->get('price', $this) * $product['quantity'];
			$vat = floatval($product['product']->get('vat', $this)) . ($product['product']->get('vat', $this) ? '' : '*');
			$vvl = $sign * $product['product']->get('vat value', $this) * $product['quantity'];
			$tvl = $sign * ($product['product']->get('price', $this) + $product['product']->get('vat value', $this)) * $product['quantity'];
			
			//if (abs($vvl - 19.36) < 0.01) { $vvl = 19.35; $tvl = 100.00; }


			$table_rows[] = array (
				$c++,
				($this->storno ? 'STORNO ' : '').str_replace (array ('&#8211;'), array ('-'), $product['product']->get('name', $this)),
				'-',
				$product['quantity'],
				$ump,
				$val,
				$vat,
				$vvl,
				$tvl
				);

			$table_totals['value'] += $val;
			$table_totals['vat'] = $table_totals['vat'] < $product['product']->get('vat', $this) ? $product['product']->get('vat', $this) : $table_totals['vat'];
			$table_totals['vat value'] += $vvl;
			$table_totals['total value'] += $tvl;
			}

		if ($this->ID) {
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set value=%f,vat=%f where id=%d;', $table_totals['total value'], $table_totals['vat'], $this->ID));
			}

		if ((!$this->storno) && $this->advance && (($this->paid['value'] > 0) && (abs($table_totals['total value'] - $this->paid['value']) > WP_CRM_E_Payment))) {
			$ratio = $this->paid['value'] / $table_totals['total value'];

			foreach ($table_rows as $key => $table_row) {
				$table_rows[$key][1] = 'Avans '.$table_row[1];
				$table_rows[$key][4] = round($ratio * $table_row[4],2);
				$table_rows[$key][5] = round($ratio * $table_row[5],2);
				$table_rows[$key][7] = round($ratio * $table_row[7],2);
				$table_rows[$key][8] = round($ratio * $table_row[8],2);
				}
			
			$table_totals['value'] = round($ratio * $table_totals['value'],2);
			$table_totals['vat value'] = round($ratio * $table_totals['vat value'],2);
			$table_totals['total value'] = round($ratio * $table_totals['total value'],2);
			}

		if (abs($table_totals['total value'] - $this->paid['value']) < WP_CRM_E_Payment && abs($table_totals['total value'] - $this->paid['value']) > 0.001) {
			$ratio = $this->paid['value'] / $table_totals['total value'];

			foreach ($table_rows as $key => $table_row) {
				$table_rows[$key][1] = $table_row[1];
				$table_rows[$key][4] = round($ratio * $table_row[4],2);
				$table_rows[$key][5] = round($ratio * $table_row[5],2);
				$table_rows[$key][7] = round($ratio * $table_row[7],2);
				$table_rows[$key][8] = round($ratio * $table_row[8],2);
				if (abs($table_rows[$key][4] - 1100) < 0.1) $table_rows[$key][4] = 1100;
				if (abs($table_rows[$key][5] - 1100) < 0.1) $table_rows[$key][5] = 1100;
				if (abs($table_rows[$key][7] - 1100) < 0.1) $table_rows[$key][7] = 1100;
				if (abs($table_rows[$key][8] - 1100) < 0.1) $table_rows[$key][8] = 1100;
				}
			
			$table_totals['value'] = round($ratio * $table_totals['value'],2);
			$table_totals['vat value'] = round($ratio * $table_totals['vat value'],2);
			$table_totals['total value'] = round($ratio * $table_totals['total value'],2);
			}

		$c = $pdf->table ($table_head, $table_rows, 4);

		for ( ; $c < $INVOICE_ROWS; $c++) { // 21, 11
			$pdf->table ( array (
				' ' => 6,
				'  ' => 70,
				'   ' => 10,
				'    ' => 10,
				'     ' => 20,
				'      ' => 15,
				'       ' => 15,
				'        ' => 20,
				'         ' => 0 ), array (), 5, 'LR');
			}

		$table_head = array (
			'Subtotaluri - LEI -' => 116,
			$table_totals['value'] . '  ' => 15,
			$table_totals['vat'] . '     ' => 15,
			$table_totals['vat value'] . '   ' => 20,
			$table_totals['total value'] . ' ' => 0
			);
		$pdf->table ($table_head, array(), 4);

		$table_head = array (
			'Valoare totala de plata factura curenta (inclusiv TVA) - LEI -' => 116,
			'h2;color: red;align: center;'.$table_totals['total value']. ' lei' => 0
			);
		$pdf->table ($table_head, array(), 6);

		$pdf->style ('small');
		if ($vat_singularity) {
			$pdf->Cell (0, 3.5, '* Scutit de TVA Cf. ART. 141 alin. (1) lit. (f), Codul Fiscal 2010/ TITLUL VI/ CAP. IX');
			}
		$pdf->style ();
		$pdf->Ln ();

		$pdf->Line (11, $INVOICE_BOTTOM, 199, $INVOICE_BOTTOM); // 220 / 160
		$pdf->SetY ($INVOICE_BOTTOM);
		$pdf->Ln ();

		$paidby = array (
			'none' => '-',
			'bank' => 'Ordin de Plata',
			'card' => 'Card Bancar',
			'cash' => 'Chitanta',
			'taxr' => 'Bon fiscal',
			);

		$pdf->columns (4, array ('Date privind expeditia:', $this->real ? ('Modalitate de plata: '.$paidby[$this->paid['by']]) : ''));
		$pdf->columns (4, array ('Numele delegatului: '.(is_object($this->delegate) ? $this->delegate->get('name') : ''), ''));
		$pdf->columns (4, array ('Act de identitate (BI/CI): '.(is_object($this->delegate) ? $this->delegate->get('id_type') : ''), $this->paid['by'] != 'none' ? ('Data platii: '.date('d-m-Y', $this->paid['date'])) : ''));////
		$pdf->columns (4, array ('Seria: ' .(is_object($this->delegate) ? $this->delegate->get('id_series') : ''). ' numarul: ' . (is_object($this->delegate) ? $this->delegate->get('id_number') : '') . ' eliberat de: ' . (is_object($this->delegate) ? $this->delegate->get('id_issuer') : ''), $this->paid['value'] ? ('Suma platita: '.$this->paid['value'].' lei'.($this->is('partial paid') ? (' / Rest de plata: '.($this->get('value') - $this->paid['value']).'lei') : '')) : '' ));
		$pdf->columns (4, array ('Mijlocul de transport: POSTA ELECTRONICA', $this->paid['details'] ? ('Detalii plata: '.$this->paid['details']) : ''));

		if (!empty($this->receipts)) {
			$tmp = array();
			foreach ($this->receipts as $receipt) $tmp[] = $receipt->get('code').' ('.date('d-m-Y', $receipt->get('date')).')';
			$pdf->columns (4, array ('Data: '.date('d-m-Y', $this->date).', Ora: '.date('H:i'), 'Chitante atasate: '.implode('; ', $tmp)));
			}
		else
			$pdf->columns (4, array ('Data: '.date('d-m-Y', $this->date).', Ora: '.date('H:i'), ''));
		$pdf->Cell (0,4, $this->real ? 'Semnatura de primire:' : '');
		$pdf->Ln ();
		$pdf->Ln ();

		get_currentuserinfo();
		$wp_user = get_userdata($current_user->ID);
		if ($wp_user->ID) {
			$wp_user_data = explode("\n", $wp_user->user_description);
			$pdf->style ('em');
			$pdf->Cell (0,5, 'Intocmit de: '.$wp_user->first_name.' '.$wp_user->last_name.', CNP '.trim($wp_user_data[0]).', C.I. '.trim($wp_user_data[1]));
			$pdf->style ();
			$pdf->Ln ();
			}

		if (!empty($this->receipts)) { ## the receipt
			$receipt = $this->receipts[count($this->receipts)-1];

			$pdf->Line (11, 200, 199, 200);
			$pdf->SetY (200);
			$pdf->Ln ();
			
			$pdf->style ('h1');
			$pdf->Cell (0, 12, 'CHITANTA');
			$pdf->style ();
			$pdf->Ln ();
			
			$pdf->Image (dirname(dirname(__FILE__)).'/images/companies/1.png', 160, 200, 40, 20);

			$pdf->columns (4, array ('Seria si numarul: ' . $receipt->get('code'), ''));
			$pdf->columns (4, array ('Data (zi-luna-an): ' . date ('d-m-Y', $receipt->get('date')), 'Am primit de la'));

			$pdf->style ('h3');
			$pdf->columns (8, array ($this->seller->get('name'), $this->buyer->get('name')));
			$pdf->style ();

			$pdf->columns (4, array ('Nr. ord. reg. com. / an: '.$this->seller->get('rc'), 'Nr. ord. reg. com. / an: '.$this->buyer->get('rc')));
			$pdf->columns (4, array ('CIF/CNP: '.$this->seller->get('uin'), 'CIF/CNP: '.$this->buyer->get('uin')));
			$pdf->columns (4, array ('Cota TVA (%): '.$this->seller->get('default_vat'), 'Adresa: '.$this->buyer->get('address')));
			$pdf->columns (4, array ('Adresa: '.$this->seller->get('address'), ''));
			#$pdf->columns (4, array ('Banca: '.$this->seller->get('bank'), 'Suma de '.$table_totals['total value'].' lei, (' . $this->num2wrd($table_totals['total value']).')'));
			$pdf->columns (4, array ('Banca: '.$this->seller->get('bank'), 'Suma de '.$receipt->get('value').' lei, (' . $this->num2wrd($receipt->get('value').')'));
			$pdf->columns (4, array ('Cont: '.$this->seller->get('account'), 'reprezentand contravaloare factura seria '.$this->get('invoice_series').' numarul '.$this->get('invoice_number')));

			$pdf->Ln ();
			$pdf->Cell (0, 4, 'Casier: '.$wp_user->first_name . ' ' . $wp_user->last_name);
			$pdf->Ln ();
			$pdf->Cell (0, 4, 'Act de identitate seria si numarul: '.$wp_user_data[1].', CNP: '.$wp_user_data[0]);
			}

		if (!$echo)
			$pdf->out (dirname(dirname(__FILE__)).'/cache/invoices/'.$this->get('invoice_series').$this->get('invoice_number').'.pdf', 'F');
		else
			$pdf->out ();
		}
		
	public function set ($key, $value) {
		global $wpdb;
		if ($key == 'date') {
			$this->date = is_numeric($value) ? $value : strtotime($value);
			if ($this->ID) {
				$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set stamp=%d where id=%d;', $this->date, $this->ID);
				if (WP_CRM_Debug) echo "WP_CRM_Invoice::set::(date) sql( $sql )\n";
				$wpdb->query ($sql);
				}
			}
		if ($key == 'paid date') {
			if (empty($this->paid)) return FALSE;
			$this->paid['date'] = is_numeric($value) ? $value : strtotime($value);
			if ($this->ID) {
				$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set paiddate=%d where id=%d;', $this->paid['date'], $this->ID);
				if (WP_CRM_Debug) echo "WP_CRM_Invoice::set::(date) sql( $sql )\n";
				$wpdb->query ($sql);
				}
			}
		if ($key == 'series') {
			$value = trim(strtoupper($value));
			if ($this->ID) {
				if ($this->series) {
					if ($this->series != $value) {
						$this->number = 0;
						$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set series=%s,number=0 where id=%d;', $value, $this->ID));
						}
					}
				else
					$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set series=%s where id=%d;', $value, $this->ID));
				}
			$this->series = $value;
			}

		if ($key == 'buyer') {
			if (!is_object($value)) {
				return FALSE;
				}

			$value_class = get_class($value);

			if ($value_class == 'WP_CRM_Person' || $value_class == 'WP_CRM_Company')
				$buyer = new WP_CRM_Buyer ($value);
			else {
				if ($value_class != 'WP_CRM_Buyer') {
					return FALSE;
					}
				$buyer = $value;
				}

			$this->buyer = $buyer;
			if ($this->ID) {
				$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set buyer=%s,bid=%d where id=%d;', $this->buyer->get('type'), $this->buyer->get(), $this->ID );
				if (WP_CRM_Debug) echo $sql."\n";
				$wpdb->query ($sql);
				}
			}

		if ($key == 'mentions' && $this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set mentions=%s where id=%d;', $value, $this->ID));
		if ($key == 'cookie' && $this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set cookie=%d where id=%d;', $value, $this->ID));
		if ($key == 'ip' && $this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set ip=%s where id=%d;', $value, $this->ID));
		if ($key == 'source' && $this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set source=%s where id=%d;', $value, $this->ID));

		if ($key == 'real') { # flags & 1
			$this->real = $value ? TRUE : FALSE;
			if ($this->ID) {
				if ($this->real) {
					$this->set ('series', $this->seller->get('invoice_series'));
					}

				$flags = intval($wpdb->get_var ($wpdb->prepare ('select flags from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)));
				$flags = $this->real ? ($flags | 1) : ($flags & ~1);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set flags=%d where id=%d;', $flags, $this->ID));
				}
			}
		if ($key == 'storno') { # flags & 2
			$this->storno = $value ? TRUE : FALSE;
			if ($this->ID) {
				$flags = intval($wpdb->get_var ($wpdb->prepare ('select flags from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)));
				$flags = $this->storno ? ($flags | 2) : ($flags & ~2);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set flags=%d where id=%d;', $flags, $this->ID));
				}
			}
		if ($key == 'discount') { # flags & 4
			$this->discount = $value ? TRUE : FALSE;
			if ($this->discount) $this->discount ();
			if ($this->ID) {
				$flags = intval($wpdb->get_var ($wpdb->prepare ('select flags from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)));
				$flags = $this->discount ? ($flags | 4) : ($flags & ~4);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set flags=%d where id=%d;', $flags, $this->ID));
				}
			}
		if ($key == 'advance') { # flags & 8
			$this->advance = $value ? TRUE : FALSE;
			if ($this->ID) {
				$flags = intval($wpdb->get_var ($wpdb->prepare ('select flags from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)));
				$flags = $this->advance ? ($flags | 8) : ($flags & ~8);
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set flags=%d where id=%d;', $flags, $this->ID));
				}
			}

#		if ($key == 'receipt') $this->receipt = $value ? array ('receipt' => TRUE) : array ();

		if (($key == 'delegate') && is_object($value)) {
			$this->delegate = $value;
			if ($this->ID)
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set did=%d where id=%d;', $value->get(), $this->ID));
			}
		if (($key == 'parent') && is_numeric($value)) {
			$this->parent = $value;
			if ($this->ID)
				$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set uid=%d where id=%d;', $value, $this->ID));
			}
		if ($key == 'basket') {
			if (!$this->ID) return FALSE;
			$wpdb->query ($wpdb->prepare ('delete from `'.$wpdb->prefix.'new_basket` where iid=%d;', $this->ID));
			parent::copy ($value);
			parent::save ($this->ID);
			}
		}

	public function get ($key = '', $value = '') {
		global $wpdb;
		if ($key == 'mentions')
			return $this->ID ? $wpdb->get_var($wpdb->prepare('select mentions from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)) : FALSE;
		if ($key == 'ip')
			return $this->ID ? $wpdb->get_var($wpdb->prepare('select ip from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)) : FALSE;
		if ($key == 'cookie')
			return $this->ID ? $wpdb->get_var($wpdb->prepare('select cookie from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)) : FALSE;
		if ($key == 'source') {
			if (!$this->ID) return FALSE;
			$source = $wpdb->get_var($wpdb->prepare('select source from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID));
			if ((strpos($source, 'http://www.google') !== FALSE) || (strpos($source, 'https://www.google') !== FALSE) || (strpos($source, 'http://www.search-results.com/') !== FALSE)) {
				if (preg_match('/q=([^&]*)&/', $source, $match)) $query = $match[1];
				return array ('src' => 'Google', 'data' => array ('word' => urldecode($query)));
				}
			if (strpos($source, 'mail.yahoo') !== FALSE) {
				return array ('src' => 'YMail', 'data' => array ());
				}
			if (strpos($source, 'http://www.traininguri.ro/') !== FALSE) {
				$url = str_replace ('http://www.traininguri.ro/', '/', $source);
				return array ('src' => 'Bookmark', 'data' => array ('url' => $url));
				}
			if (strpos($source, 'http://www.negociatorul.ro/') !== FALSE) {
				$url = str_replace ('http://www.negociatorul.ro/', '/', $source);
				return array ('src' => 'Negociatorul', 'data' => array ('url' => $url));
				}
			return array ('src' => $source, 'data' => array ());
			}


		if ($key == 'default_seller') {
			$companies = new WP_CRM_List ('companies', array ('flags=1'));
			if (!$companies->is('empty')) {
				$company = $companies->get();
				return $company[0];
				}
			return NULL;
			}
		if ($key == 'invoice_series') {
			if ($this->series) return $this->series;
			if (is_object($this->seller)) {
				$this->series = $this->seller->get('invoice_series');
				$this->series = $this->real ? $this->series : ('P'.$this->series);
				return $this->series;
				}
			return FALSE;
			}
		if ($key == 'invoice_number') {
			$cache = dirname(dirname(__FILE__)).'/cache/series';
			if (!$this->ID) return FALSE;
			if ($this->number) return str_pad($this->number, 5, 0, STR_PAD_LEFT);

			if (!$this->series) {
				if (is_object($this->seller))
					$this->series = $this->seller->get('invoice_series');
				else
					return FALSE;
				}
			
			if (file_exists($cache . '/' . $this->series.'.num'))
				$this->number = intval(file_get_contents($cache.'/'.$this->series.'.num')) + 1;
			else
				$this->number = 1;
			file_put_contents($cache.'/'.$this->series.'.num', $this->number);

			$wpdb->query ($wpdb->prepare('update `'.$wpdb->prefix.'new_invoices` set number=%d,series=%s where id=%d;', $this->number, $this->series, $this->ID));

			$this->number = str_pad($this->number, 5, 0, STR_PAD_LEFT);
			return $this->number;
			}
		if ($key == 'quick list') {
			$out = array ();
			$products = $wpdb->get_results ($wpdb->prepare('select * from `'.$wpdb->prefix.'new_basket` where iid=%d;', $this->ID));
			foreach ($products as $product) {
				if (!$product->pid) continue;
				$uin = $wpdb->get_col ($wpdb->prepare('select uin from `'.$wpdb->prefix.'clients` where iid=%d and pid=%d and series=%s and number=%d;', $this->ID, $product->pid, wp_crm_extract_series($product->code), wp_crm_extract_number($product->code)));
				$out[] = '<a href="'.get_admin_url().'/admin.php?page=wp_crm_participants&product='.$product->code.'&highlight='.implode(';',$uin).'" target="_blank">' . $product->quantity . '</a>'. ' X ' . '<a href="'.get_admin_url().'/admin.php?page=wp_crm_participants&product='.$product->code.'" target="_blank" title="'.$product->product.'">'.$product->code.'</a>';
				}
			return implode (', ', $out);
			}
		if ($key == 'payment') {
			return $this->paid;
			}
		if ($key == 'date') {
			return $this->date;
			}
		if ($key == 'value') {
			$value = 0;
			if ($this->discount) $this->discount();
			foreach (($this->get('products')) as $product) {
				$quantity = $product['quantity'];
				$product = $product['product'];

				$value += $quantity * $product->get ('value', $this);
				}
			return $value;
			}
		if ($key == 'paid value') {
			return $this->paid['value'];
			}
		if ($key == 'paid details') {
			return $this->paid['details'];
			}
		if ($key == 'paid date') {
			return $this->paid['date'];
			}
		if ($key == 'paid total') {
			$paid = 0;
			$invoice = $this;
			while (($invoice->get('parent')) !== FALSE) {
				$paid += $invoice->get('paid value');
				$invoice = $invoice->get('parent');
				}
			return $paid;
			}
		if ($key == 'paid by') {
			return $this->paid['by'];
			}
		if ($key == 'vat') {
			$vat = 0;
			if ($this->discount) $this->discount();
			foreach (($this->get('products')) as $product) {
				$quantity = $product['quantity'];
				$product = $product['product'];

				$vat += $quantity * $product->get ('vat value', $this);
				}
			return $vat;
			}
		if ($key == 'status') {
			return '';
			}
		if ($key == 'id') {
			return $this->ID;
			}
		if ($key == 'real') {
			return $this->real;
			}
		if ($key == 'parent') {
			return $this->parent ? new WP_CRM_Invoice ($this->parent) : FALSE;
			}
		if ($key == 'children') {
			$invoices = array ();
			$children = $wpdb->get_col ($wpdb->prepare ('select id from `'.$wpdb->prefix.'new_invoices` where iid=%d;', $this->ID));
			if (!empty($children)) foreach ($children as $child) $invoices[] = new WP_CRM_Invoice (intval($child));
			return $invoices;
			}
		if ($key == 'participants') {
			$participants = array ();
			if (is_object($value))
				$parts = $wpdb->get_col ($wpdb->prepare ('select uin from `'.$wpdb->prefix.'clients` where iid=%d and pid=%d;', array (
					$this->ID,
					$value->get()
					)));
			else
				$parts = $wpdb->get_col ($wpdb->prepare ('select uin from `'.$wpdb->prefix.'clients` where iid=%d;', $this->ID));
			if (!empty($parts)) foreach ($parts as $part) $participants[] = new WP_CRM_Person (intval($part));
			return $participants;
			}

		if ($key == 'keys') return $this->keys;
		return parent::get($key);
		}

	public function is ($key = 'paid') {
		if ($key == 'real')
			return $this->real ? TRUE : FALSE;
		if ($key == 'partial paid')
			return (($this->paid['by'] != 'none') && ($this->get('paid value') > WP_CRM_E_Payment) && ($this->get('value') - $this->get('paid value') > WP_CRM_E_Payment)) ? TRUE : FALSE;
		if ($key == 'paid' || $key == 'fully paid')
			return $this->paid['by'] != 'none' ? TRUE : FALSE;
		if ($key == 'discounted')
			return $this->discount ? TRUE : FALSE;
		}

	public function num2wrd ($number) {
		$words = array (
			1 => array ('unu', 'doi', 'trei', 'patru', 'cinci', 'sase', 'sapte', 'opt', 'noua'),
			10 => array ('zece', 'douazeci', 'treizeci', 'patruzeci', 'cincizeci', 'saizeci', 'saptezeci', 'optzeci', 'nouazeci'),
			100 => array ('o suta', 'doua sute', 'trei sute', 'patru sute', 'cinci sute', 'sase sute', 'sapte sute', 'opt sute', 'noua sute'),
			1000 => array ('o mie', 'doua mii', 'trei mii', 'patru mii', 'cinci mii', 'sase mii', 'sapte mii', 'opt mii', 'noua mii')
			);

		$integer = intval($number);
		$decimal = intval(100 * ($number - $integer));

		$out = '';

		$value = $integer%100;

		if ($value) {
			if ($value < 10) $out = $words[1][$value - 1] . ' ' . $out;
			else
			if ($value == 10) $out = $words[10][0] . $out;
			else
			if ($value < 20) $out = $words[1][$value%10 - 1] . 'sprezece ' . $out;
			else {
				if ($value % 10)
					$out = $words[10][intval($value/10) - 1] . ' si ' . $words[1][$value%10 - 1] . ' ' . $out;
				else
					$out = $words[10][intval($value/10) - 1] . ' ' . $out;
				}
			}

		if ($integer) $out .= ($value > 0 || $value < 20) ? 'lei' : 'de lei';

		$integer = intval($integer/100);
		$value = $integer%10;

		if ($value) $out = $words[100][$value - 1] . ' ' . $out;
		$integer = intval ($integer/10);
		$value = $integer%10;

		if ($value) $out = $words[1000][$value - 1] . ' ' . $out;

		if ($decimal) {
			if ($decimal < 10) $out .= ' si ' . $words[1][$decimal - 1];
			else
			if ($decimal == 10) $out .= ' si ' . $words[10][0];
			else
			if ($decimal < 20) $out .= ' si ' . $words[1][$decimal%10 - 1] . 'sprezece';
			else {
				if ($decimal % 10)
					$out .= ' si ' . $words[10][intval($decimal/10) - 1] . ' si ' . $words[1][$decimal%10 - 1];
				else
					$out .= ' si ' . $words[10][intval($decimal/10) - 1];
				}
			$out .= ($decimal < 20) ? ' bani' : ' de bani';
			}

		return str_replace ('unusprezece', 'unsprezece', $out);
		}

	private function discount () {
		global $wpdb;
		$vat = 0;
		$discount_val = 0;
		$quantity = 0;
		$has_discount = FALSE;
		foreach (($this->get('products')) as $product) {
			if ($product['product']->get('name') == 'Discount') $has_discount = TRUE;
			if ($product['product']->get('price') > 0) {
				$quantity += $product['quantity'];
				$vat = $vat < $product['product']->get('vat', $this) ? $product['product']->get('vat', $this) : $vat;
				}
			}

		if ($has_discount) return FALSE;

		$discount_val = $quantity < 2 ? 0 : ($quantity * 100);

		if (!$discount_val) {
			$is_paying = FALSE;
			$participants = $this->get('participants');
			
			if (!empty($participants)) foreach ($participants as $participant) if ($participant->is('paying customer', $this->ID)) $is_paying = TRUE;
			
			if ($is_paying) $discount_val = 100;

			if (!$discount_val && is_object($this->buyer)) {
				if ($this->buyer->get('type') == 'company') {
					$sql = $wpdb->prepare ('select paidby from `'.$wpdb->prefix.'new_invoices` where id!=%d and bid=%d and buyer=\'company\';', $this->ID, $this->buyer->get());
					$paying = $wpdb->get_col ($sql);
					foreach ($paying as $paid)
						if ($paid != 'none') $is_paying = TRUE;
					}
				if ($is_paying) $discount_val = 100;
				}
			if (!$discount_val) return FALSE;
			}

		$discount_vat = round($vat * $discount_val / (100 + $vat), 2);

		$discount = new WP_CRM_Product (array (
			'name' => 'Discount',
			'price' => $discount_vat - $discount_val, # minus
			'vat' => $vat
			));

		parent::add ($discount, 1);
		}
	
	public function delete () {
		global $wpdb;
		if (!$this->ID) return FALSE;
		if ($this->real) return FALSE;
		$sql = $wpdb->prepare ('delete from `'.$wpdb->prefix.'clients` where iid=%d;', $this->ID);
		if (WP_CRM_Debug) echo "WP_CRM_Invoice::delete::sql( $sql )\n";
		$wpdb->query ($sql);
		$sql = $wpdb->prepare ('delete from `'.$wpdb->prefix.'new_basket` where iid=%d;', $this->ID);
		if (WP_CRM_Debug) echo "WP_CRM_Invoice::delete::sql( $sql )\n";
		$wpdb->query ($sql);
		$sql = $wpdb->prepare ('delete from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID);
		if (WP_CRM_Debug) echo "WP_CRM_Invoice::delete::sql( $sql )\n";
		$wpdb->query ($sql);
		return TRUE;
		}

	public function display ($echo = FALSE) {
		$out = '';

		if ($echo) echo $out;
		else return $out;
		}
	
	public function __destruct () {
		}
	};

class WP_CRM_List {
	private $list;
	private $type;
	private $filter;
	public function __construct ($type, $filter = array()) {
		global $wpdb;
		if (empty($filter)) $filter = array(1);

		$this->type = $type;
		$this->filter = $filter;
		$this->list = array();

		if ($this->type == 'persons') {
			if (isset($filter['text'])) {
				$filter[] = $wpdb->prepare ('match (first_name,last_name,name,email) against (%s)', $filter['text']);
				unset ($filter['text']);
				}
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where '.implode($filter).' group by uin;');
			$persons = $wpdb->get_results($sql);
			foreach ($persons as $person) $this->list[] = new WP_CRM_Person ($person);
			}
		if ($this->type == 'products') {
			if (in_array('active', $filter)) {
				if (in_array('mine', $filter)) {
					$current_user = wp_get_current_user();
					$sql = $wpdb->prepare ('select series,number from `'.$wpdb->prefix.'products` where uid=%d and state=1;', $current_user->ID);
					}
				else
					$sql = $wpdb->prepare ('select series,number from `'.$wpdb->prefix.'products` where state=1;');

				$products = $wpdb->get_results($sql);
				foreach ($products as $product) {
					$this->list[] = new WP_CRM_Product (array ('series' => $product->series, 'number' => $product->number));
					}
				}
			else {
				$sql = $wpdb->prepare ('select pid from `'.$wpdb->prefix.'products` group by pid;');
				$products = $wpdb->get_col($sql);
				foreach ($products as $product) {
					$this->list[] = new WP_CRM_Product ($product);
					}
				}
			}
		if ($this->type == 'participants') {
			if (is_object($filter))
				$product = $filter;
			else
				$product = new WP_CRM_Product ($filter);
			$interval = $product->get('interval');

			$sql = $wpdb->prepare ('select uin from `'.$wpdb->prefix.'clients` where series=%s and number=%d;', $product->get('current series'), $product->get('current number'));
			echo $sql;
			$participants = $wpdb->get_col($sql);

			foreach ($participants as $participant)
				$this->list[] = new WP_CRM_Person ($participant);
			}
		if ($this->type == 'invoices') {
			$order = 'id';
#			$where = array ('iid=0');	# don't want virtual invoices to show!
			$where = array (1);
			if (!empty($filter)) {
				if (isset($filter['when'])) {
					$where[] = 'stamp > '.strtotime($filter['when']);
					}

				if (isset($filter['sort'])) {
					$sort = explode(' ', $filter['sort']);
					if ($sort[0] == 'time') $order = 'stamp '.$sort[1];
					}
				if (in_array('real', $filter))
					$where[] = 'flags&1=1';
				}
			if ($filter['sort']) {
				$filter['sort'] = explode(' ', $filter['sort']);
				if ($filter['sort'][0] == 'time') $order = 'stamp '.$filter['sort'][1];
				if ($filter['sort'][0] == 'series') $order = 'number '.$filter['sort'][1];
				}

			$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'new_invoices` where '.(implode(' and ', $where)).' order by '.$order.';');
			if (WP_CRM_Debug) echo "WP_CRM_List::__construct=invoices sql ($sql)\n";
			$invoices = $wpdb->get_col($sql);
			foreach ($invoices as $invoice)
				$this->list[] = new WP_CRM_Invoice ($invoice);
			}
		if ($this->type == 'companies') {
			if ($filter['text']) {
				$filter[] = $wpdb->prepare ('match (name,email) against (%s)', $filter['text']);
				unset ($filter['text']);
				}
			$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'companies` where '.(!empty($filter) ? implode(' and ', $filter) : 1).';');
			$companies = $wpdb->get_col($sql);
			foreach ($companies as $company)
				$this->list[] = new WP_CRM_Company ($company);
			}
		if ($this->type == 'resources') {
			if (is_object($filter))
				$filter = array ("series='".$filter->get('current series')."'", "number='".$filter->get('current number')."'");
			$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'product_resources` where ('.(!empty($filter) ? implode(' and ', $filter) : 1).') or (flags=1);');
			$resources = $wpdb->get_col($sql);
			foreach ($resources as $resource)
				$this->list[] = new WP_CRM_Resource ($resource);
			}
		if ($this->type == 'trainers') {
			$trainers = get_posts ('cat=664&posts_per_page=-1');
			if (!empty($trainers))
				foreach ($trainers as $trainer)
					$this->list[] = new WP_CRM_Trainer ($trainer->ID);
			}
		if ($this->type == 'locations') {
			$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'product_locations` where ('.(!empty($filter) ? implode(' and ', $filter) : 1).') or (flags=1);');
			$locations = $wpdb->get_col($sql);
			if (!empty($locations))
				foreach ($locations as $location)
					$this->list[] = new WP_CRM_Location ($location);
			}
		if ($this->type == 'responsibles') {
			$responsibles = get_users (array ('role' => 'administrator'));
			if (!empty($responsibles))
				foreach ($responsibles as $responsible)
					$this->list[] = new WP_CRM_Responsible ($responsible->ID);
			}
		if ($this->type == 'events') {
			if (is_object($this->filter['client'])) {
				$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'events_log` where client=%s;', $this->filter['client']->get('uin'));
				$events = $wpdb->get_col($sql);
				if (!empty($events))
					foreach ($events as $event)
						$this->list[] = new WP_CRM_Event ($event);
				}
			}
		if ($this->type == 'receipts') {
			if (is_numeric($this->filter['invoice'])) $this->filter['invoice'] = new WP_CRM_Invoice ($this->filter['invoice']);
			if (is_object($this->filter['invoice'])) {
				$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'receipts` where iid=%d;', $this->filter['invoice']->get());
				$receipts = $wpdb->get_col($sql);
				if (!empty($receipts))
					foreach ($receipts as $receipt)
						$this->list[] = new WP_CRM_Receipt ($receipt);
				}
			}
		}

	public function sort ($by, $direction = 'asc') {
		if ($by == 'time')
			usort ($this->list, array ('WP_CRM_List', 'time_compare'));
		if ($by == 'name')
			usort ($this->list, array ('WP_CRM_List', 'name_compare'));
		if ($direction == 'desc')
			$this->list = array_reverse ($this->list);
		}

	public function get ($key = '', $value = null) {
		if ($key == 'count') return count($this->list);
		if ($key == 'select') {
			$out = '';
			if (!empty($this->list))
				foreach ($this->list as $object)
					$out .= '<option value="'.($object->get($value['value'])).'"'.($object->get($value['value']) == $value['selected'] ? ' selected' : '').'>'.$object->get($value['text']).'</option>'."\n";
			return $out;
			}
		return $this->list;
		}

	public function is ($key) {
		if ($key == 'empty') return empty($this->list) ? TRUE : FALSE;
		return FALSE;
		}

	static private function time_compare ($a, $b) {
		if (get_class($a) == 'WP_CRM_Product') {
			if ($a->get('current stamp') < $b->get('current stamp')) return -1;
			if ($a->get('current stamp') > $b->get('current stamp')) return 1;
			}
		if (get_class($a) == 'WP_CRM_Event') {
			if ($a->get('time') < $b->get('time')) return -1;
			if ($a->get('time') > $b->get('time')) return 1;
			}
		if (get_class($a) == 'WP_CRM_Receipt') {
			if ($a->get('time') < $b->get('time')) return -1;
			if ($a->get('time') > $b->get('time')) return 1;
			}
		return 0;
		}

	static private function name_compare ($a, $b) {
		if (get_class($a) == 'WP_CRM_Person') {
			if ($a->get('name') < $b->get('name')) return -1;
			if ($a->get('name') > $b->get('name')) return 1;
			}
		return 0;
		}

	public function __destruct () {
		}
	};

class WP_CRM_Location {
	private $ID;
	private $title;
	private $address;
	private $directions;
	private $map;

	public function __construct ($data = null) {
		global $wpdb;
		if (is_numeric ($data)) {
			$this->ID = intval ($data);
			$location = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'product_locations` where id=%d;', $data));
			if ($location) {
				$this->title = $location->title;
				$this->address = $location->address;
				$this->directions = $location->directions;
				$this->map = $location->map;
				}
			}
		elseif (is_array ($data)) {
			$this->title = $data['title'];
			$this->address = $data['address'];
			$this->directions = $data['directions'];
			$this->map = $data['map'];
			}
		}

	public function get ($key = '', $value = null) {
		global $wpdb;
		if ($key == 'name' || $key == 'title') return $this->title;
		if ($key == 'address') return $this->address;
		if ($key == 'directions') return $this->directions;
		if ($key == 'map') return $this->map;
		return $this->ID;
		}

	public function set ($key = '', $value = null) {
		global $wpdb;
		if ($key == 'title' || $key == 'name') $this->title = $value;
		if ($key == 'address') $this->address = $value;
		if ($key == 'directions') $this->directions = $value;
		if ($key == 'map') $this->map = $value;
		if ($this->ID) {
			if ($key == 'title' || $key == 'name') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set title=%s;', $this->title));
			if ($key == 'address') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set address=%s;', $this->title));
			if ($key == 'directions') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set directions=%s;', $this->title));
			if ($key == 'map') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set map=%s;', $this->title));
			}
		return TRUE;
		}

	public function save () {
		global $wpdb;
		if ($this->ID) return FALSE;
		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'product_locations` (title,address,directions,map,flags) values (%s,%s,%s,%s,%d);', array (
			$this->title,
			$this->address,
			$this->directions,
			$this->map,
			0
			));
		if (WP_CRM_Debug) echo "WP_CRM_Location::save::sql( $sql )\n";
		}

	public function __destruct () {
		}
	}

class WP_CRM_Trainer {
	private $ID;
	private $name;

	public function __construct ($data = null) {
		global $wpdb;
		if (is_numeric ($data)) {
			$this->ID = $data;
			$name = get_the_title ($this->ID);
			$this->name = trim(preg_replace ('/[-&].+$/', '', $name));
			}
		}

	public function get ($key = '', $value = null) {
		global $wpdb;
		if ($key == 'name') return $this->name;
		return $this->ID;
		}

	public function __destruct () {
		}
	}

class WP_CRM_Resource {
	private $ID;

	private $product;
	private $type;
	private $title;
	private $description;
	private $value;
	private $vat;
	private $global;

	public function __construct ($data = null) {
		global $wpdb;
		if (is_array($data)) {
			$this->product = is_object($data['product']) ? $data['product'] : null;
			$this->title = $data['title'];
			$this->description = $data['description'];
			$this->value = $data['value'];
			$this->fees = $data['fees'];
			$this->type = $data['type'] == 'flat' ? 'flat' : 'volume';
			$this->global = $data['global'] ? TRUE : FALSE;
			}
		else
		if (is_numeric($data)) {
			$resource = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'product_resources` where id=%d', $data));
			$this->ID = $resource->id;
			$this->product = new WP_CRM_Product (array ('series' => $resource->series, 'number' => $resource->number));
			$this->type = $resource->type;
			$this->title = $resource->title;
			$this->description = $resource->description;
			$this->value = $resource->value;
			$this->vat = $resource->vat;
			$this->global = $resource->flags ? TRUE : FALSE;
			}
		}

	public function save () {
		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'product_resources` values (null,%d,%s,%d,%s,%s,%s,%f,%f,%d);', array (
			is_object($this->product) ? $this->product->get() : 0,
			is_object($this->product) ? $this->product->get('current series') : '',
			is_object($this->product) ? $this->product->get('current number') : 0,
			$this->type,
			$this->title,
			$this->description,
			$this->value,
			$this->fees,
			$this->global ? 1 : 0));
		if (WP_CRM_Debug) echo "WP_CRM_Resource::save::sql ($sql)\n";
		$wpdb->query ($sql);
		$this->ID = $wpdb->get_var ('select last_insert_id();');
		}

	public function delete () {
		}

	public function get ($key) {
		}

	public function __destruct () {
		}
	}

class WP_CRM_Responsible {
	private $ID;
	private $user;

	public function __construct ($data = null) {
		if (is_numeric($data)) {
			$this->ID = $data;
			$this->user = get_userdata($this->ID);
			}
		}

	public function get ($key = '') {
		if ($key == 'name') return $this->user->display_name;
		return $this->ID;
		}

	public function __destruct () {
		}
	}

class WP_CRM_Template {
	private $ID;
	private $company;
	private $subject;
	private $content;

	public function __construct ($data = null) {
		global $wpdb;
		if (is_numeric($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'mailtemplate` where id=%d;', $data);
			$data = $wpdb->get_row ($sql);
			$this->ID = $data->id;
			$this->subject = $data->subject;
			$this->content = $data->content;
			$this->company = null;
			if ($cid>0)
				$this->company = new WP_CRM_Company ($data->cid);
			}
		if (is_string($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'mailtemplate` where match(subject) against(%s);', $data);
			$data = $wpdb->get_row ($sql);
			$this->ID = $data->id;
			$this->subject = $data->subject;
			$this->content = $data->content;
			$this->company = null;
			if ($cid>0)
				$this->company = new WP_CRM_Company ($data->cid);
			
			}
		}

	public function get ($key = '') {
		if ($key == 'title' || $key == 'subject') return $this->subject;
		if ($key == 'content' || $key == 'message') return $this->content;
		if ($this->ID) return $this->ID;
		return FALSE;
		}

	public function set ($key, $value = '') {
		if ($key == 'title' || $key == 'subject') {
			$this->subject = $value;
			}
		if ($key == 'content' || $key == 'message') {
			$this->content = $value;
			}
		}

	public function save () {
		
		}

	public function parse ($role, $object) {
		if (!is_object($object)) return FALSE;
		switch (get_class($object)) {
			case 'WP_CRM_Person':
			case 'WP_CRM_Company':
			case 'WP_CRM_Buyer':
			case 'WP_CRM_Invoice':
			case 'WP_CRM_Product':
				$keys = $object->get('keys');
				if (!empty($keys))
					foreach ($keys as $key) {
						$var = '{'.$role.'.'.str_replace(' ','_',$key).'}';
						$val = $object->get($key);
						$this->subject = str_replace ($var, $val, $this->subject);
						$this->content = str_replace ($var, $val, $this->content);
						}
				break;
			}
		return TRUE;
		}

	public function __destruct () {
		}
	}

class WP_CRM_Event {
	private $ID;

	private $time;
	private $default;
	private $user;
	private $transport;
	private $client;
	private $company;
	private $initiator;
	private $product;
	private $class;
	private $title;
	private $description;
	private $fired;

	private $callbacks;

	public function __construct ($data = null) {
		global $wpdb;
		$this->callbacks = array ();
		$this->default = array (
# hook
			'Primul telefon',
			'Informatii generale despre produse (De ce nu le-a gasit pe site?)',
# lead
			'Inscriere la curs (De ce nu s-a inscris pe site?)',
# payment
			'Solicitare discount suplimentar pentru curs',
			'Modalitati alternative de plata pentru curs',
			'Dificultati de plata online (Cauza?)',
			'Confirmarea platii pentru curs (De ce nu a fost instiintat?)',
# presence 
			'Renunta la curs, urmand sa participe in viitor (De ce?, La care?)',
			'Renunta la curs fara o explicatie coerenta (De ce?)',
			'Nu s-a prezentat la curs',
# feedback
			'Feedback participare la curs',
			'Solicitare restituire taxa de participare (De ce?)',
			'Recomandare cursului persoanelor din anturaj (In ce fel?)',
# misc
			'Diverse',
			);

		if (is_numeric($data)) {
			$event = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'events_log` where id=%d;', $data));
			if ($event) {
				$this->ID = (int) $data;
				$this->time = $event->stamp;
				$this->user = get_userdata($event->uid);
				$this->transport = $event->transport;
				$this->client = $event->client ? new WP_CRM_Person ($event->client) : null;
				$this->company = $event->company ? new WP_CRM_Company ($event->company) : null;
				$this->initiator = $event->ini;
				$this->product = $event->code ? new WP_CRM_Product (array ('series' => wp_crm_extract_series($event->code), 'number' => wp_crm_extract_number($event->code))) : null;
				$this->title = $event->title;
				$this->class = $event->cls;
				$this->description = $event->description;
				$this->fired = $event->flags ? true : false;
				}
			}
		else {
			$this->time = $data['stamp'] ? $data['stamp'] : time();
			$this->user = is_numeric($data['user']) ? get_userdata($data['user']) : wp_get_current_user();
			$this->transport = $data['transport'] ? $data['transport'] : 'mail';
			$this->client = is_object ($data['client']) ? $data['client'] : null;
			$this->company = is_object ($data['company']) ? $data['company'] : null;
			$this->initiator = $data['initiator'] ? $data['initiator'] : 'auto';
			$this->product = is_object ($data['product']) ? $data['product'] : null;
			$this->title = is_numeric($data['title']) ? $this->default[$data['title']] : $data['title'];
			$this->class = is_numeric($data['title']) ? $data['title'] : 0;
			$this->description = $data['description'];
			$this->fired = false;
			}
		}

	public function save () {
		global $wpdb;
		
		$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'events_log` where stamp=%d and client=%s and transport=%s;', array (
			$this->time,
			is_object($this->client) ? $this->client->get('uin') : 0,
			$this->transport
			));
		if ($wpdb->get_var($sql)) return FALSE;

		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'events_log` values (null,%d,%s,%d,%s,%s,%d,%d,%s,%d,%s,%s,%d);', array (
			$this->time,
			$this->initiator,
			is_object($this->user) ? $this->user->ID : 0,
			$this->transport,
			is_object($this->client) ? $this->client->get('uin') : 0,
			is_object($this->company) ? $this->company->get() : 0,
			is_object($this->product) ? $this->product->get() : 0,
			is_object($this->product) ? $this->product->get('current code') : '',
			$this->class,
			$this->title,
			$this->description,
			$this->fired ? 1 : 0
			));
		//if (WP_CRM_Debug)
		echo "WP_CRM_Event::save::sql( $sql )\n";
		$wpdb->query ($sql);
		$this->ID = $wpdb->get_var ('select last_insert_id();');
		}

	public function get ($key) {
		if ($key == 'keys') return $this->default;
		if ($key == 'time') return $this->time;
		if ($key == 'user') return $this->user;
		if ($key == 'transport') return $this->transport;
		if ($key == 'client') return $this->client;
		if ($key == 'company') return $this->client;
		if ($key == 'initiator') return $this->initiator;
		if ($key == 'product') return $this->product;
		if ($key == 'title' || $key == 'subject') return $this->title;
		if ($key == 'class') return $this->class;
		if ($key == 'class name') return $this->default[$this->class];
		if ($key == 'description' || $key == 'content') return $this->description;
		if ($key == 'fired') return $this->fired;
		return $this->ID;
		}

	public function fire () {
		if ($this->fired) return;
		if ($this->ID) {
			$this->fired = true;
			$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'events_log` set flags=1 where id=%d;', $this->ID);
			if (WP_CRM_Debug) echo "WP_CRM_Event::fire::sql( $sql )\n";
			$wpdb->query ($sql);
			}
		}

	public function __destruct () {
		}
	}
?>
