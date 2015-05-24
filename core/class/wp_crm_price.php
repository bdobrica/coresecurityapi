<?php
class WP_CRM_Price extends WP_CRM_Model {
	public static $T = 'product_prices';
	protected static $K = array (
		'price',
		'vat',
		);
	protected static $Q = array (
		'`id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`pid` INT(11) NOT NULL DEFAULT 0',
		'`price` FLOAT(9,2) NOT NULL DEFAULT 0.00',
		'`vat` FLOAT(4,2) NOT NULL DEFAULT 0.00',
		'`min_quantity` INT(11) NOT NULL DEFAULT 1',
		'`min_date` INT(11) NOT NULL DEFAULT 0'
		);

	public static $F = array (
		'new' => array (
			'matrix:matrix' => 'Preturi',
			),
		'edit' => array (
			'matrix:matrix' => 'Preturi',
			)
		);

	protected $product;

	public function __construct ($data, $quantity = null, $date = null) {
		global $wpdb;

		if (!is_array ($data)) {
			$this->product = ($data instanceof WP_CRM_Product) ? $data->get () : (int) $data;

			if (is_object ($quantity) && ($quantity instanceof WP_CRM_Invoice)) {
				$sql = $wpdb->prepare ('select * from `'. $wpdb->prefix . WP_CRM_Basket::$T . '` where pid=%d and iid=%d;', array (
					$this->product,
					$quantity->get ()
					));

				$row = $wpdb->get_row ($sql);
				$data = array (
					'price'		=> $row->price,
					'vat'		=> $row->vat
					);
				}
			else {
				$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where pid=%d and min_quantity<=%d and min_date<=%d order by min_quantity desc, min_date desc limit 0,1;', array (
					$this->product,
					is_null ($quantity) ? 1 : (int) $quantity,
					is_null ($date) ? time() : (int) $date
					));
				$data = $wpdb->get_row ($sql, ARRAY_A);
				}
			}

		parent::__construct ($data);
		}

	public function get ($key = null, $opts = null) {
		global $wpdb;

		switch ((string) $key) {
			case 'full price':
				return sprintf ("%.2f", (1 + 0.01 * $this->get ('vat')) * $this->get ('price'));
				break;
			case 'taxes':
				return sprintf ("%.2f", 0.01 * $this->get ('vat') *  $this->get ('price'));
				break;
			case 'matrix':
				$sql = $wpdb->prepare ('select min_quantity from `' . $wpdb->prefix . static::$T . '` where pid=%d group by min_quantity order by min_quantity;', $this->product);
				$quantities = $wpdb->get_col ($sql);
				$sql = $wpdb->prepare ('select min_date from `' . $wpdb->prefix . static::$T . '` where pid=%d group by min_date order by min_date;', $this->product);
				$dates = $wpdb->get_col ($sql);

				$out = array ();

				$last_quantity = 1;
				foreach ($dates as $date) {
					foreach ($quantities as $quantity) {
						while ($last_quantity <= $quantity) {
							$sql = $wpdb->prepare ('select price from `' . $wpdb->prefix . static::$T . '` where pid=%d and min_quantity<=%d and min_date>=%d order by min_quantity desc, min_date limit 0,1;', array (
								$this->product,
								$last_quantity,
								$date
								));
							$out[date('d-m-Y', $date)][$last_quantity] = $wpdb->get_var ($sql);
							$last_quantity++;
							}
						}
					}

				if (empty ($out)) $out = array (date ('d-m-Y') => array (1 => 0));

				return $out;
//				return serialize ($out);
				break;
			}
		return parent::get ($key, $opts);
		}

	public function set ($key = null, $value = null) {
		global $wpdb;

		if (is_string ($key)) {
			switch ($key) {
				case 'matrix':
					break;
				}
			}

		if (is_array ($key)) {
			if (isset ($key['matrix'])) {
				$this->set ('matrix', $key['matrix']);
				unset ($key['matrix']);
				}
			}

		return parent::set ($key, $value);
		}

	public function save ($data = null) {
		global $wpdb;

		if (!is_null ($data) && !empty ($data)) {

			foreach ($data as $date => $prices) {
				
				}
			}
		}

	public function __toString () {
		return self::get ('full price');
		}
	}
?>
