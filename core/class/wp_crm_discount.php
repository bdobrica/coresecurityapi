<?php
class WP_CRM_Discount extends WP_CRM_Model {
	public static $T = 'discounts';
	public static $K = array (
		'name',
		'description',
		'oid',
		'cid',
		'pid',
		'minp', /* min price */
		'maxp',
		'minq', /* min quantity */
		'maxq',
		'mins', /* min stamp */
		'maxs',
		'type',
		'value'
		);
	public static $F = array (
		'new' => array (
			),
		'view' => array (
			),
		'edit' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`name` varchar(64) NOT NULL DEFAULT \'\' UNIQUE',
		'`description` text NOT NULL',
		'`oid` int NOT NULL DEFAULT 0',
		'`cid` int NOT NULL DEFAULT 0',
		'`pid` int NOT NULL DEFAULT 0',
		'`minp` float(9,2) NOT NULL DEFAULT 0.00',
		'`maxp` float(9,2) NOT NULL DEFAULT 0.00',
		'`minq` int NOT NULL DEFAULT 0',
		'`maxq` int NOT NULL DEFAULT 0',
		'`mins` int NOT NULL DEFAULT 0',
		'`maxs` int NOT NULL DEFAULT 0',
		'`type` enum(\'fixed\',\'percent\') NOT NULL DEFAULT \'fixed\'',
		'`value` float(9,2) NOT NULL DEFAULT 0.00',
		);

	private $discount;

	public function __construct ($data = null, $strict = FALSE) {
		global $wpdb;

		$this->discount = array ();

		parent::__construct ($data);

		if ($this->ID) {
			$sql = $wpdb->prepare ('select * from `'. $wpdb->prefix . static::$L . '` where cid=%d;', $this->ID);
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

	public function discount ($office, $company, $product, $quantity, $time = null) {
		$time = is_null ($time) ? time () : ((int) $time);

		if ($office instanceof WP_CRM_Office)
			$oid = $office->get();
		else
		if (is_numeric($office))
			$oid = (int) $office;
		else {
			try {
				$office = new WP_CRM_Office ($office);
				$oid = $office->get();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$oid = null;
				}
			}

		if ($company instanceof WP_CRM_Company)
			$cid = $company->get();
		else
		if (is_numeric($company))
			$cid = (int) $company;
		else {
			try {
				$company = new WP_CRM_Company ($company);
				$cid = $company->get();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$cid = null;
				}
			}

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


		if (empty($this->discount[$oid])) return 0;
		if (empty($this->discount[$oid][$cid])) return 0;
		if (empty($this->discount[$oid][$cid][$pid])) return 0;

		foreach ($this->discount[$oid][$cid][$pid] as $discount) {
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

	}
?>
