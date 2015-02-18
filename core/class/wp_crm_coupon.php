<?php
class WP_CRM_Coupon extends WP_CRM_Model {
	const Coupon_Max_Length = 12;

	public static $T = 'coupons';
	private static $C = 'coupon_data';
	protected static $K = array (
		'cid',
		'code',
		'alias'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`code` varchar(12) NOT NULL DEFAULT \'\'',
		'`alias` text'
		);
	public static $F = array (
		'view' => array (
			'code' => 'Cod',
			'alias' => 'Alias',
			),
		);

	private $discount;

	public function __construct ($data = null, $strict = FALSE) {
		global $wpdb;

		$this->discount = array ();

		if (is_string($data) && strlen($data) <= self::Coupon_Max_Length) {
			$sql = $strict ?
				$wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where code=%s limit 0,1;', strtoupper($data)):
				$wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where match(code,alias) against (%s) limit 0,1;', strtoupper($data));
			$data = $wpdb->get_row ($sql, ARRAY_A);
			if (is_null ($data)) {
				throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
				}
			}

		parent::__construct ($data);

		if ($this->ID) {
			$sql = $wpdb->prepare ('select * from `'. $wpdb->prefix . static::$C . '` where cid=%d;', $this->ID);
			$rows = $wpdb->get_results ($sql);
			if (!empty($rows))
				foreach ($rows as $row)
					$this->discount[$row->pid][] = array (
						'min_quantity'	=> $row->minq,
						'max_quantity'	=> $row->maxq,
						'min_stamp'	=> $row->mins,
						'max_stamp'	=> $row->maxs,
						'type'		=> $row->type,
						'value'		=> $row->value
						);
			}
		}

	public function encode ($str = null) {
		$aliases = explode (',', $this->data['alias']);
		$aliases[] = $this->data['code'];
		$out = array ();
		if (is_null ($str)) {
			foreach ($aliases as $alias)
				$out[] = '/affiliate/?coupon=' . $this->data['coupon'] . '&code=' . md5 ($strtoupper (trim ($alias)));
			return $out;
			}
		foreach ($aliases as $alias)
			if (strtoupper (trim ($str)) == strtoupper (trim ($alias))) return md5 (strtoupper (trim ($alias)));
		}

	public function decode ($str) {
		$aliases = explode (',', $this->data['alias']);
		foreach ($aliases as $alias) {
			if (md5 (strtoupper (trim ($alias))) == $str) return strtoupper (trim ($alias));
			}
		throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_Coupon);
		}

	public function discount ($product, $quantity, $time = null) {
		$time = is_null ($time) ? time () : ((int) $time);
		if ($product instanceof WP_CRM_Product)
			$pid = $product->get();
		else
		if (is_numeric($product))
			$pid = (int) $product;
		else {
			try {
				$product = new WP_CRM_Product ($product);
				$pid = $product->get ();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$pid = null;
				}
			}

		if (empty($this->discount[$pid])) return 0;

		foreach ($this->discount[$pid] as $discount) {
			if (
				((!$discount['min_quantity']) || ($discount['min_quantity'] && ($discount['min_quantity'] <= $quantity))) &&
				((!$discount['max_quantity']) || ($discount['max_quantity'] && ($quantity <= $discount['max_quantity']))) &&
				((!$discount['min_stamp']) || ($discount['min_stamp'] && ($discount['min_stamp'] <= $time))) &&
				((!$discount['max_stamp']) || ($discount['max_stamp'] && ($time <= $discount['max_stamp'])))
				) 
			$out = $discount['value'] . ($discount['type'] == 'percent' ? '%' : '');
			}

		return $out;
		}

	public function __toString () {
		$out = array ();

		$time = is_null ($time) ? time () : ((int) $time);

		if (empty ($this->discount)) return json_encode ($this->discount);
		foreach ($this->discount as $product => $discounts) {
			if (empty ($discounts)) continue;
			foreach ($discounts as $discount) {
				if (
					((!$discount['min_stamp']) || ($discount['min_stamp'] && ($discount['min_stamp'] <= $time))) &&
					((!$discount['max_stamp']) || ($discount['max_stamp'] && ($time <= $discount['max_stamp'])))
					) 
				$out[$product][] = $discount;
				} 
			}
		return json_encode ($out);
		}

	public static function install ($uninstall = FALSE) {
		global $wpdb;

		
		$sql = $uninstall ?
			('DROP TABLE `' . $wpdb->prefix . static::$C . '`;') :
			('CREATE TABLE `' . $wpdb->prefix . static::$C . '` (
				`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`cid` int(11) NOT NULL DEFAULT 0,
				`pid` int(11) NOT NULL DEFAULT 0,
				`minq` int(11) NOT NULL DEFAULT 0,
				`maxq` int(11) NOT NULL DEFAULT 0,
				`mins` int(11) NOT NULL DEFAULT 0,
				`maxs` int(11) NOT NULL DEFAULT 0,
				`type` enum(\'fixed\',\'percent\') NOT NULL DEFAULT \'fixed\',
				`value` float(9,2) NOT NULL DEFAULT 0.00
				) engine=MyISAM default charset=utf8;');

		if ($wpdb->get_var ('show tables like \'' . $wpdb->prefix . static::$C . '\';') != ($wpdb->prefix . static::$C)) {
			$wpdb->query ($sql);
			}

		parent::install ($uninstall);
		}
	}
?>
