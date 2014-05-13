<?php
/**
 * Basket is an important class as it emulates a shopping basket and is an ancestor of Invoice.
 * Basket can hold predefined Products or temporary (meta) one-use products. That's why it has the
 * const MetaSeries attached - to providy meta products with a unique SKU.
 */
class WP_CRM_Basket extends WP_CRM_Model {
	const MetaSeries	= 'METAPR';

	public static $T = 'basket';
	protected static $K = array (
		'bid',
		'pid',
		'cid',
		'code',
		'product',
		'iid',
		'price',
		'vat',
		'quantity',
		'stamp',
		'flags'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`bid` int(11) NOT NULL DEFAULT 0',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`code` varchar(10) NOT NULL DEFAULT \'\'',
		'`product` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL',
		'`iid` int(11) NOT NULL DEFAULT 0',
		'`price` float(9,2) NOT NULL DEFAULT 0.00',
		'`vat` float(4,2) NOT NULL DEFAULT 0.00',
		'`quantity` int(11) NOT NULL DEFAULT 1',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'`flags` int(1) NOT NULL DEFAULT 0'
		);

	protected $products;

	protected $meta_id;
	protected $meta_products;
	
	public function __construct ($data = null) {
		global
			$wpdb,
			$wp_crm_buyer;

		$this->meta_id = 1;

		if (is_null ($data)) {
			$this->products = array ();

			$sql = $wpdb->prepare ('select code,quantity from `' . $wpdb->prefix . self::$T . '` where iid=0 and bid=%d;', $wp_crm_buyer->get ());

			$rows = $wpdb->get_results ($sql);
			if (!empty($rows))
			foreach ($rows as $row)
				if ($row->quantity)
					$this->products [$row->code] = (int) $row->quantity;
			}
		else
		if (is_numeric ($data)) {
			$this->products = array ();

			$sql = $wpdb->prepare ('select code,quantity from `' . $wpdb->prefix . self::$T . '` where iid=%d;', (int) $data);
			$rows = $wpdb->get_results ($sql);
			if (!empty($rows))
			foreach ($rows as $row)
				if ($row->quantity)
					$this->products [$row->code] = (int) $row->quantity;
			}
		else
		if (is_string ($data)) {
			$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . WP_CRM_Invoice::$T . '` where series=%s and number=%d;', array (
				parent::parse ('series', $data),
				parent::parse ('number', $data)
				));
			$data = (int) $wpdb->get_var ($sql);

			if ($data) {
				$sql = $wpdb->prepare ('select code,quantity from `' . $wpdb->prefix . self::$T . '` where iid=%d;', (int) $data);
				$rows = $wpdb->get_results ($sql);
				if (!empty($rows))
				foreach ($rows as $row)
					if ($row->quantity)
						$this->products [$row->code] = (int) $row->quantity;
				}
			}

		parent::__construct ($data);
		if ($this->ID) {
			/*
			TODO: put products from database in $products
			*/
			}
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'products':
				return $this->products;
				break;
			}
		return parent::get ($key, $opts);
		}

	public function set ($key = null, $value = null) {
		switch ((string) $key) {
			case 'products':
				if (is_array ($value)) {
					if (isset ($value['old']) && isset ($value['new'])) {
						$this->products = array ();
						if (!empty ($value['old']))
						foreach ($value['old'] as $product) {
							$product_id = $product['id'];
							$quantity = $product['quantity'];
							try {
								$product = new WP_CRM_Product ((int) $product_id);
								}
							catch (WP_CRM_Exception $wp_crm_exception) {
								continue;
								}
							$this->products[$product->get ('code')] = (int) $quantity;
							unset ($product);
							}
						if (!empty ($value['new']))
						foreach ($value['new'] as $meta_product)
							$this->meta_products[self::MetaSeries . str_pad($this->meta_id ++, WP_CRM_Product::Pad_Number, '0', STR_PAD_LEFT)] = $meta_product;
						}
					else
						$this->products = $value;
					}
				else
				if ($value instanceof WP_CRM_Basket) $this->products = $value->get ('products');
				return TRUE;
				break;
			}

		if (is_array ($key) && is_null ($value)) {
			if (isset ($key['products'])) {
				if (is_array ($key['products'])) {
					if (isset ($key['products']['old']) && isset ($key['products']['new'])) {
						$this->products = array ();
						if (!empty ($key['products']['old']))
						foreach ($key['products']['old'] as $product) {
							$product_id = $product['id'];
							$quantity = $product['quantity'];
							try {
								$product = new WP_CRM_Product ((int) $product_id);
								}
							catch (WP_CRM_Exception $wp_crm_exception) {
								continue;
								}
							$this->products[$product->get ('code')] = (int) $quantity;
							unset ($product);
							}
						if (!empty ($key['products']['new']))
						foreach ($key['products']['new'] as $meta_product)
							$this->meta_products[self::MetaSeries . str_pad($this->meta_id ++, WP_CRM_Product::Pad_Number, '0', STR_PAD_LEFT)] = $meta_product;
						}
					else
						$this->products = $value;
					}
				else
				if ($key['products'] instanceof WP_CRM_Basket) $this->products = $value->get ('products');
				$key['products'] = null;
				unset ($key['products']);
				}
			}

		parent::set ($key, $value);
		}

	public function add ($product, $quantity = null, $dontadd = FALSE) {
		global
			$wpdb,
			$wp_crm_buyer;

		if (is_string ($product))
			$product = new WP_CRM_Product ($product);

		if (!($product instanceof WP_CRM_Product))
			throw new WP_CRM_Exception (__CLASS__ . '::Unknown Object. Expected WP_CRM_Product', WP_CRM_Exception::Unknown_Object);

		if (!isset($this->products[$product->get('code')]))
			$this->products[$product->get('code')] = is_null ($quantity) ? 1 : (int) $quantity;
		else
			$this->products[$product->get('code')] = is_null ($quantity) ? ($dontadd ? $this->products[$product->get('code')] : 1) : ($dontadd ? ((int) $quantity) : ($this->products[$product->get('code')] + ((int) $quantity)));

		if ($this->products[$product->get('code')] == 0) {
			unset($this->products[$product->get('code')]);
			
			$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . self::$T . '` where code=%s and bid=%d;', array (
				$product->get ('code'),
				$wp_crm_buyer->get ()
				));
			$wpdb->query ($sql);
			}
		else {
			$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . self::$T . '` where code=%s and bid=%d;', array (
				$product->get ('code'),
				$wp_crm_buyer->get ()
				));
			if ($wpdb->get_var ($sql)) {
				$sql = $wpdb->prepare ('update `' . $wpdb->prefix . self::$T . '` set quantity=%d where code=%s and bid=%d;', array (
					$this->products[$product->get('code')],
					$product->get ('code'),
        	                        $wp_crm_buyer->get ()
					));
				$wpdb->query ($sql);
				}
			}
		}

	public function is ($key = null) {
		switch ((string) $key) {
			case 'empty':
				return empty ($this->products) ? TRUE : FALSE;
				break;
			}

		return FALSE;
		}

	public function save () {
		global
			$wp_crm_buyer;


		if (get_called_class () == __CLASS__) {
			if (!empty($this->products))
				foreach ($this->products as $product => $quantity) {
					$wp_crm_product = new WP_CRM_Product ($product);
					$wp_crm_price = $wp_crm_product->get ('price', $quantity);

					$this->data = array (
						'bid' => $wp_crm_buyer->get (),
						'cid' => $wp_crm_product->get ('cid'),
						'pid' => $wp_crm_product->get (),
						'product' => $wp_crm_product->get ('title'),
						'code' => $wp_crm_product->get ('code'),
						'iid' => 0,
						'price' => $wp_crm_price->get ('price'),
						'vat' => $wp_crm_price->get ('taxes'),
						'quantity' => (int) $quantity,
						'stamp' => time ()
						);

					parent::save ();
					/*
					HINT: drop the ID. we don't need it as basket is not a DB object, but a collection
					*/
					$this->ID = null;
					}
			}
		else
			parent::save ();
		}

	public function delete () {
		global $wpdb;

		$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . self::$T . '` where bid=%d and iid=0;', $wp_crm_buyer->get ());
		$wpdb->query ($sql);
		}

	public function __toString () {
		if (empty($this->products)) return '';

		$out = array ();
		foreach ($this->products as $product => $quantity)
			$out[] = $product . '-' . $quantity;

		return implode ('+', $out);
		}

	public function __destruct () {
		}
	};
?>
