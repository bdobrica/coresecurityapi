<?php
class WP_CRM_Invoice extends WP_CRM_Basket {
	const Real_Invoice		= 1;
	const Storno_Invoice		= 2;
	const Discounted_Invoice	= 4;
	const Advance_Invoice		= 8;

	const Temporary_Prefix		= 'P';

	const Cache			= 'cache/invoices';

	const Epsilon			= 1;
	const ID_Base			= 24;

	public static $T = 'invoices';
	protected static $K = array (
		'oid',
		'uid',
		'sid',
		'bid',
		'did',
		'iid',
		'buyer',
		'series',
		'number',
		'stamp',
		'value',
		'vat',
		'content',
		'coupon',
		'paidby',
		'paidvalue',
		'paiddate',
		'paiddetails',
		'mentions',
		'cookie',
		'ip',
		'source',
		'link',
		'affiliate',
		'heard',
		'hearddetails',
		'payload',
		'flags'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`sid` int(11) NOT NULL DEFAULT 0',
		'`bid` int(11) NOT NULL DEFAULT 0',
		'`did` int(11) NOT NULL DEFAULT 0',
		'`iid` int(11) NOT NULL DEFAULT 0',
		'`buyer` enum(\'person\',\'company\') DEFAULT \'person\'',
		'`series` varchar(8) NOT NULL DEFAULT \'\'',
		'`number` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'`value` float(9,2) NOT NULL DEFAULT 0.00',
		'`vat` float(9,2) NOT NULL DEFAULT 0.00',
		'`content` text',
		'`coupon` varchar(8) NOT NULL DEFAULT \'\'',
		'`paidby` enum(\'bank\',\'card\',\'cash\',\'taxr\',\'part\',\'none\') DEFAULT \'none\'',
		'`paidvalue` float(9,2) NOT NULL DEFAULT 0.00',
		'`paiddate` int(11) NOT NULL DEFAULT 0',
		'`paiddetails` text',
		'`mentions` text NOT NULL',
		'`cookie` int(11) NOT NULL DEFAULT 0',
		'`ip` varchar(15) NOT NULL DEFAULT \'127.0.0.1\'',
		'`source` text NOT NULL',
		'`link` int(11) NOT NULL DEFAULT 0',
		'`affiliate` int(11) NOT NULL DEFAULT 0',
		'`heard` enum(\'google\',\'facebook\',\'youtube\',\'radio\',\'tv\',\'print\',\'friend\',\'newsletter\',\'site\') DEFAULT \'google\'',
		'`hearddetails` text NOT NULL',
		'`payload` text NOT NULL',
		'`flags` int(11) NOT NULL DEFAULT 0',
		);
	public static $F = array (
		'new' => array (
			'stamp:date' => 'Data emiterii',
			'sid:seller' => 'Emitent',
			'buyer:buyer' => 'Cumparator',
			'products:product' => 'Produse',
			'mentions' => 'Mentiuni',
			'coupon' => 'Cupon',
			'paid:bool' => 'Plateste'
			),
		'edit' => array (
			'stamp:date' => 'Data emiterii',
			'sid:seller' => 'Emitent',
			'buyer:buyer' => 'Cumparator',
			'products:product' => 'Produse',
			'mentions' => 'Mentiuni',
			'coupon' => 'Cupon',
			'paid:bool' => 'Plateste'
			),
		'view' => array (
			'series:series' => 'Factura',
			'paid%:%' => 'Platita',
			'paiddate:date' => 'In data',
			'stamp:date' => 'Data Factura',
			'bid:buyer' => 'Cumparator',
			'buyer:entity' => 'Tip',
			'coupon' => 'Cupon',
			'products:products' => 'Produse',
			'value:float' => 'Valoare',
			'vat:float' => 'TVA',
			'seats:seat' => 'Locuri',
			'source:referer' => 'Sursa'
			),
		'excerpt' => array (
			'series:series' => '',
			'paid%:%' => '',
			'paiddate:date' => 'Din data',
			'stamp:date' => 'Inscriere',
			'bid:buyer' => 'Cumparator',
			'buyer:entity' => 'Tip',
			'coupon' => 'Cupon',
			'products:products' => 'Produse',
			'value:float' => 'Valoare',
			'vat:float' => 'TVA',
			'seats:seat' => 'Locuri',
			'source:referer' => 'Sursa'
			),
		'safe' => array (
			'stamp:date' => 'Data inscriere',
			'bid:safebuyer' => 'Cumparator',
			'series' => 'Serie factura',
			'value:float' => 'Valoare',
			'products:safeproducts' => 'Produse',
			'paid:bool' => 'Platita'
			),
		'public' => array (
			'stamp:date' => 'Timp',
			'bid:buyer' => 'Cumparator',
			'buyer' => 'Tip',
			'series' => 'Serie',
			'value:float' => 'Valoare',
			'vat:float' => 'TVA',
			'products:products' => 'Produse',
			'coupon' => 'Cupon',
			'source:referer' => 'Sursa',
			'paid:bool' => 'Platita'
			),
		'extended' => array (
			),
		'private' => array (
			),
		'group' => array (
			'series:series' => 'Factura',
			'paid%:%' => 'Platita',
			'stamp:date' => 'Inscriere',
			'buyer:entity' => 'Tip',
			'coupon' => 'Cupon',
			'products:products' => 'Produse',
			'value:float' => 'Valoare',
			'source:referer' => 'Sursa'
			)
		);

	private $real;		# proforma?	flags & 1
	private $paid;		# array

	private $parent;	# iid
	private $storno;	# is a storno	flags & 2
	private $discount;	# has discount	flags & 4
	private $advance;	# advance	flags & 8
	private $receipts;	# array

	public $seller;
	public $delegate;
	public $buyer;

	private $flags;

	public function __construct ($data = null) {
		global $wpdb;

		if (! (is_numeric ($data) || is_array ($data) || is_object ($data))) {
			
			}

		parent::__construct ($data);

		try {
			$this->buyer = $this->data['bid'] ?
				($this->data['buyer'] == 'person' ?
						new WP_CRM_Person ((int) $this->data['bid']) :
						new WP_CRM_Company ((int) $this->data['bid'])) :
					null;
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			$this->buyer = null;
			}
		$this->seller = $this->data['sid'] ?
			new WP_CRM_Company ((int) $this->data['sid']) :
			null;

		$this->flags = (int) $this->data['flags'];
		}

	public function pay ($stamp = 0) {
		global $wpdb;

		$payments = new WP_CRM_List ('WP_CRM_Payment', array ('iid=' . $this->ID));
		$paidvalue = 0;
		/*
		* INFO: should be tested. commented is the previous version
		*/
		$paiddate = $stamp;
		foreach (($payments->get()) as $payment) {
			$paidvalue += $payment->get ('amount');
			#$paiddate = $payment < $payment->get ('stamp') ? $payment->get ('stamp') : $paiddate;
			$paiddate = $payment->get ('stamp') < $paiddate ? $payment->get ('stamp') : $paiddate;
			}
		
		$series = $this->seller->get ('invoice_series');
		if ($this->data['series'] != $series) {
			$this->data['series'] = $series;

			$sql = $wpdb->prepare ('select 1+max(coalesce(number,0)) from `' . $wpdb->prefix . static::$T . '` where series=%s', $this->data['series']);
			$this->data['number'] = (int) $wpdb->get_var ($sql);

			$sql = $wpdb->prepare ('update `'. $wpdb->prefix . static::$T .'` set series=%s,number=%d,paidvalue=%f,paiddate=%d,stamp=%d,flags=%d where id=%d;', array (
				$this->data['series'],
				$this->data['number'],
				$paidvalue,
				$paiddate,
				#$stamp ? $stamp : $this->data['stamp'],
				$this->data['stamp'],
				((int) $this->data['flags']) | self::Real_Invoice,
				$this->ID
				));
			$wpdb->query ($sql);
			}
		else {
			$sql = $wpdb->prepare ('update `'. $wpdb->prefix . static::$T .'` set paidvalue=%s,paiddate=%d,stamp=%d,flags=%d where id=%d;', array (
				$paidvalue,
				$paiddate,
				#$stamp ? $stamp : $this->data['stamp'],
				$this->data['stamp'],
				((int) $this->data['flags']) | self::Real_Invoice,
				$this->ID
				));
			$wpdb->query ($sql);
			}

		return TRUE;
		/*
		HIST:
		*/
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

					### bellow step is no longer needed, since the introduction of WP_CRM_Receipt object.
					# copy the invoice!
					#$invoice = $this->copy();
					#$invoice->set ('parent', $this->ID);
					#$invoice->save ();
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
	
	public function set ($key = null, $value = null) {
		global $wpdb;

		if (is_string ($key)) {
			switch ((string) $key) {
				case 'buyer':
					if (!is_object ($value)) return FALSE;
					if (!(($value instanceof WP_CRM_Person) || ($value instanceof WP_CRM_Company))) return FALSE;
					$this->buyer = $value;
					parent::set ('bid', $value->get());
					parent::set ('buyer', $value instanceof WP_CRM_Person ? 'person' : 'company');
					return TRUE;
					break;
				case 'sid':
					$this->seller = $value instanceof WP_CRM_Company ? $value : new WP_CRM_Company ((int) $value);
					return WP_CRM_Model::set ($key, $this->seller->get());
					break;
				case 'products':
					return parent::set ($key, $value);
					break;
				}
		
			if (in_array ($key, self::$K)) {
				WP_CRM_Model::set ($key, $value);
				}
			if (in_array ($key, parent::$K)) {
				parent::set ($key, $value);
				}
			}

		if (is_array ($key) && is_null ($value)) {
			echo "\n\$key=";
			var_dump ($key);
			if (isset ($key['buyer']) && is_object ($key['buyer'])) {
				$this->buyer = $key['buyer'];
				$key['bid'] = $key['buyer']->get ();
				$key['buyer'] = $key['buyer'] instanceof WP_CRM_Company ? 'company' : 'person';
				}
			if (isset ($key['sid']) && is_object ($key['sid'])) {
				$this->seller = $key['sid'];
				$key['sid'] = $key['sid']->get();
				}
			if (isset ($key['products'])) {
				parent::set ('products', $key['products']);
				$key['products'] = null;
				unset ($key['products']);
				}

			$keys = array_keys ($key);
			
			$self = array_intersect ($keys, self::$K);
			if (!empty ($self)) {
				echo "\n\$self=";
				var_dump ($self);
				$set = array ();
				foreach ($self as $_k) $set[$_k] = $key[$_k];
				echo "\n\$set=";
				var_dump ($set);
				
				WP_CRM_Model::set ($set);
				}
			$parent = array_intersect ($keys, parent::$K);
			if (!empty ($parent)) {
				echo "\n\$parent=";
				var_dump ($parent);
				$set = array ();
				foreach ($parent as $_k) $set[$_k] = $key[$_k];
				echo "\n\$set=";
				var_dump ($set);

				WP_CRM_Model::set ($set);
				}
			}

		$return = parent::set ($key, $value);
		/**
		 * TODO: update products. If (this->ID) .. then
		 */

		return $return;

		/*
		HIST:
		*/
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
		if ($key == 'affiliate' && $this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set affiliate=%d where id=%d;', $value, $this->ID));

		if ($key == 'heard' && $this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set heard=%s,hearddetails=%s where id=%d;', $value['from'], $value['details'], $this->ID));

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
		if ($key == 'payload') {
			if (!$this->ID) return FALSE;
			$this->payload = $value;
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set payload=%s where id=%d;', serialize($this->payload), $this->ID));
		}
	}

	public function get ($key = null, $opts = null) {
		global $wpdb;

		switch ((string) $key) {
			case 'series':
				return $this->data['series'] . str_pad ($this->data['number'], 5, 0, STR_PAD_LEFT);
				break;
			case 'value':
				$out = (float) parent::get ('value', $opts);
				if ($out) return $out;

				$total = 0;
				$vat = 0;

				try {
					$wp_crm_coupon = new WP_CRM_Coupon ($this->data['coupon']);
				}
				catch (WP_CRM_Exception $wp_crm_exception) {
					$wp_crm_coupon = null;
				}
				$coupon_discount = 0;

				if (!empty($this->products))
					foreach ($this->products as $product => $quantity) {
						try {
							$wp_crm_product = new WP_CRM_Product ($product);
							$wp_crm_price = $wp_crm_product->get ('price', array ('quantity' => $quantity));
							$total += $quantity * $wp_crm_price->get ('full price');
							$tmp_vat = $wp_crm_price->get ('vat');
							$vat = $vat < $tmp_vat ? $tmp_vat : $vat;
						}
						catch (WP_CRM_Exception $wp_crm_exception) {
							$sql = $wpdb->prepare ('select product,price,vat from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where iid=%d and code=%s and quantity=%d;', array (
										$this->ID,
										$product,
										$quantity
										));
							$product = $wpdb->get_row ($sql);
							if (is_null ($product)) continue;
							$wp_crm_price = new WP_CRM_Price (array (
										'price' => $product->price,
										'vat' => $product->vat
										));
							$wp_crm_product = $product->product;

							$total += $quantity * $wp_crm_price->get ('full price');
							$tmp_vat = $wp_crm_price->get ('vat');
							$vat = $vat < $tmp_vat ? $tmp_vat : $vat;
						}

						if (is_object($wp_crm_coupon) && ($discount = $wp_crm_coupon->discount ($wp_crm_product, $quantity, $this->data['stamp']))) {
							$coupon_discount += (strpos ($discount, '%') !== FALSE) ?
								0.01 * $quantity * $wp_crm_price->get ('full price') * ((float) str_replace ('%', '', $discount)) :
								(float) $discount;
						}
					}

				$total -= $coupon_discount;

				$this->set ('value', $total);
				$this->set ('vat', $vat);

				return $total;
				break;
			case 'vat':
				/*
INFO: force updating the values
				 */
				$out = $this->get ('value');
				return parent::get ('vat', $opts);
				break;
			case 'payments':
				$list = new WP_CRM_List ('WP_CRM_Payment', array ('iid=' . $this->ID));
				return $list->get ('amount');
				break;
			case 'paid':
				$list = new WP_CRM_List ('WP_CRM_Payment', array ('iid=' . $this->ID));
				$amount = $list->get ('amount');
				$value = $this->get ('value');
				return (($value > self::Epsilon) && (abs($amount - $value) < self::Epsilon)) ? true : false;
				break;
			case 'paid%':
				$list = new WP_CRM_List ('WP_CRM_Payment', array ('iid=' . $this->ID));
				$amount = $list->get ('amount');
				$value = $this->get ('value');
				$amount = $amount > $value ? $value : $amount;
				return ($this->is ('real') ? '+' : '') . ($value ? sprintf ('%d%%', 100*$amount/$value) : '0%');
				break;
			case 'seats':
				$out = array ('registered' => 0, 'all' => 0, 'sent' => 0, 'checked' => 0, 'present' => 0);
				$list = new WP_CRM_List ('WP_CRM_Client', array ('iid=' . $this->ID));
				$out['registered'] = $list->get ('size');
				$out['all'] = array_sum ($this->products);
				$out['all'] = $out['all'] ? $out['all'] : $out['registered'];
				$out['sent'] = $list->get ('sizeif', array ('key' => 'card', 'operator' => '>', 'value' => 0));
				$out['checked'] = $list->get ('sizeif', array ('key' => 'flags', 'operator' => '>', 'value' => 2));
				return $out;
				break;
			case 'product':
				try {
					$wp_crm_product = new WP_CRM_Product ($opts['product']);
					return $wp_crm_product->get ('title');
				}
				catch (WP_CRM_Exception $wp_crm_exception) {
					$sql = $wpdb->prepare ('select product from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where iid=%d and code=%s and quantity=%d;', array (
								$this->ID,
								$opts['product'],
								$opts['quantity']
								));
					return $wpdb->get_var ($sql);
				}
				break;
			case 'clients':
				$sql = $wpdb->prepare ('select pid,product,code,quantity from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where iid=%d;', array (
							$this->ID
							));
				$clients = array ();
				$products = $wpdb->get_results ($sql);
				if ($products) {
					foreach ($products as $product) {
						$list = new WP_CRM_List ('WP_CRM_Client', array (
									'iid=' . $this->ID,
									'pid=' . $product->pid
									));
						$clients[$product->code] = array (
								'product' => $product->product,
								'quantity' => $product->quantity,
								'clients' => $list->get ()
								);
					}
				}
				return $clients;
				break;
		}

		return parent::get ($key, $opts);

		if ($key == 'mentions')
			return $this->ID ? $wpdb->get_var($wpdb->prepare('select mentions from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)) : FALSE;
		if ($key == 'ip')
			return $this->ID ? $wpdb->get_var($wpdb->prepare('select ip from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)) : FALSE;
		if ($key == 'cookie')
			return $this->ID ? $wpdb->get_var($wpdb->prepare('select cookie from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID)) : FALSE;
		if ($key == 'source') {
			if (!$this->ID) return FALSE;
			$source = $wpdb->get_var($wpdb->prepare('select source from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID));
			$remote = $wpdb->get_var($wpdb->prepare('select ip from `'.$wpdb->prefix.'new_invoices` where id=%d;', $this->ID));
			if ((strpos($source, 'http://www.google') !== FALSE) || (strpos($source, 'https://www.google') !== FALSE) || (strpos($source, 'http://www.search-results.com/') !== FALSE) || (strpos($source, 'http://search.conduit.com') !== FALSE) || (strpos($source, 'http://search.sweetim.com') !== FALSE)) {
				if (preg_match('/q=([^&]*)&/', $source, $match)) $query = $match[1];
			if (preg_match('/cd=([^&]*)&/', $source, $match)) $position = (int) $match[1];
			return array ('src' => 'Google', 'data' => array ('word' => urldecode($query), 'position' => $position));
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
		if (strpos($source, 'http://www.colegiultrainerilor.ro/') !== FALSE) {
			$url = str_replace ('http://www.colegiultrainerilor.ro/', '/', $source);
		return array ('src' => 'ColegiulTrainerilor', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.damaideparte.ro/') !== FALSE) {
			$url = str_replace ('http://www.damaideparte.ro/', '/', $source);
		return array ('src' => 'DaMaiDeparte', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.maimultesanse.pub.ro/') !== FALSE) {
			$url = str_replace ('http://www.maimultesanse.pub.ro/', '/', $source);
		return array ('src' => 'MaiMulteSanse', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.facebook.com/') !== FALSE) {
			$url = str_replace ('http://www.facebook.com/', '/', $source);
		return array ('src' => 'FB', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'https://www.facebook.com/') !== FALSE) {
			$url = str_replace ('https://www.facebook.com/', '/', $source);
		return array ('src' => 'FB', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.linguaprof.com/') !== FALSE) {
			$url = str_replace ('http://www.linguaprof.com/', '/', $source);
		return array ('src' => 'LinguaProof', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.loredanalatis.ro/') !== FALSE) {
			$url = str_replace ('http://www.loredanalatis.ro/', '/', $source);
		return array ('src' => 'LoredanaLatis', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.ejobs.ro/') !== FALSE) {
			$url = str_replace ('http://www.ejobs.ro/', '/', $source);
		return array ('src' => 'eJobs', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://suport.mobilpay.ro/') !== FALSE) {
			$url = str_replace ('http://suport.mobilpay.ro/', '/', $source);
		return array ('src' => 'MobilPay', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://rw.mailagent.ro/') !== FALSE) {
			$url = str_replace ('http://rw.mailagent.ro/', '/', $source);
		return array ('src' => 'MailAgent', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://vinul.ro/') !== FALSE) {
			$url = str_replace ('http://vinul.ro/', '/', $source);
		return array ('src' => 'Vinul', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.e-scoala.ro/') !== FALSE) {
			$url = str_replace ('http://www.e-scoala.ro/', '/', $source);
		return array ('src' => 'eScoala', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://search.yahoo.com/') !== FALSE) {
			$url = str_replace ('http://search.yahoo.com/', '/', $source);
		return array ('src' => 'Yahoo', 'data' => array ('url' => $url));
		}
		if (strpos($source, 'http://www.ask.com/') !== FALSE) {
			$url = str_replace ('http://www.ask.com/', '/', $source);
		return array ('src' => 'Ask', 'data' => array ('url' => $url));
		}
		if ($source == '' && ($remote == '86.120.250.28' || $remote == '127.0.0.1')) return array ('src' => 'ByHand', 'data' => array ());
		return array ('src' => $source, 'data' => array ());
		}
		if ($key == 'affiliate') {
			return $this->affiliate;
		}

		if ($key == 'heard from') return $this->heard['from'];
		if ($key == 'heard details') return $this->heard['details'];

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
		if ($key == 'affiliate products') {
			$out = array ();
			$products = $wpdb->get_results ($wpdb->prepare('select * from `'.$wpdb->prefix.'new_basket` where iid=%d;', $this->ID));
			foreach ($products as $product) {
				if (!$product->pid) continue;
				$out[] = $product->quantity . ' X ' . '<a href="'.get_permalink($product->pid).'" target="_blank" title="'.$product->product.'">'.substr($product->code, 0, 3).'</a>';
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
		if ($key == 'payload') return $value ? $this->payload[$value] : $this->payload;
		return parent::get($key);
		}

	public function is ($key = 'paid') {
		if ($key == 'real')
			return (($this->flags & self::Real_Invoice) == self::Real_Invoice) ? TRUE : FALSE;
		if ($key == 'storno')
			return (($this->flags & self::Storno_Invoice) == self::Storno_Invoice) ? TRUE : FALSE;
		if ($key == 'discount')
			return (($this->flags & self::Discount_Invoice) == self::Discount_Invoice) ? TRUE : FALSE;
		if ($key == 'advance')
			return (($this->flags & self::Advance_Invoice) == self::Advance_Invoice) ? TRUE : FALSE;

		if ($key == 'partial paid')
			return (($this->paid['by'] != 'none') && ($this->get('paid value') > WP_CRM_E_Payment) && ($this->get('value') - $this->get('paid value') > WP_CRM_E_Payment)) ? TRUE : FALSE;
		if ($key == 'paid' || $key == 'fully paid')
			return $this->paid['by'] != 'none' ? TRUE : FALSE;
		if ($key == 'discounted')
			return $this->discount ? TRUE : FALSE;
		if ($key == 'empty')
			return parent::is('empty');
		}

	public function save () {
		global
			$wpdb,
			$wp_crm_state,
			$wp_crm_buyer;

		$companies = array ();

		if (!empty($this->products))
			foreach ($this->products as $product => $quantity) {
				$wp_crm_product = new WP_CRM_Product ($product);
				$company_id = $wp_crm_product->get ('cid');
				if (!empty($companies) && in_array ($company_id, $companies)) continue;
				$companies[] = $company_id;
				}

		print_r ($this->data);

		if (empty($companies) && isset($this->data['sid'])) $companies[] = $this->data['sid'];
		if (is_object ($this->buyer)) $wp_crm_buyer = new WP_CRM_Buyer ($this->buyer);

		$ids = array ();

		/*
		TODO: exception on fail to save
		*/

		if (count ($companies) > 1) {
			foreach ($companies as $company_id) {
				$wp_crm_company = new WP_CRM_Company ($company_id);
			
				$this->data['sid'] = $company_id;
				$this->data['oid'] = $wp_crm_company->get ('oid');
				$this->data['uid'] = $wp_crm_company->get ('uid');
				$this->data['series'] = 
					($this->is ('real') ? '' : WP_CRM_Invoice::Temporary_Prefix) .
					$wp_crm_company->get ('invoice_series');

				parent::save ();

				$sql = $wpdb->prepare ('select 1+coalesce(max(number),0) from `' . $wpdb->prefix . static::$T . '` where series=%s', $this->data['series']);
				$this->data['number'] = (int) $wpdb->get_var ($sql);
				$sql = $wpdb->prepare ('update `'. $wpdb->prefix . static::$T .'` set number=%d where id=%d;', array (
					$this->data['number'],
					$this->ID
					));
				$wpdb->query ($sql);

				$sql = $wpdb->prepare ('update `' . $wpdb->prefix . parent::$T . '` set iid=%d where iid=0 and cid=%d and bid=%d;', array (
						$this->ID,
						$company_id,
						$wp_crm_buyer->get ()
						));
				$wpdb->query ($sql);

				$ids[] = $this->ID;
				$this->ID = null;
				}
			}
		else {
			/**
			 * HINT: don't reset this->ID because we have only one company
			 */
			if (!empty($companies)) {
				$wp_crm_company = new WP_CRM_Company ($companies[0]);
				$this->data['sid'] = $companies[0];
				$this->data['uid'] = $wp_crm_company->get ('uid');
				$this->data['series'] = 
					($this->is ('real') ? '' : WP_CRM_Invoice::Temporary_Prefix) .
					$wp_crm_company->get ('invoice_series');
				}
			else {
				$this->data['sid'] = 0;
				$this->data['uid'] = 0;
				}

			parent::save ();

			/**
			 * Get the next invoice number for $this->data['series'] and update the records
			 */
			$sql = $wpdb->prepare ('select 1+coalesce(max(number),0) from `' . $wpdb->prefix . static::$T . '` where series=%s', $this->data['series']);
			$this->data['number'] = (int) $wpdb->get_var ($sql);
			$sql = $wpdb->prepare ('update `'. $wpdb->prefix . static::$T .'` set number=%d where id=%d;', array (
				$this->data['number'],
				$this->ID
				));
			$wpdb->query ($sql);


			$ids[] = $this->ID;

			if (sizeof ($companies)) {
				if (is_object ($wp_crm_buyer) && ($wp_crm_buyer instanceof WP_CRM_Buyer)) {
					$sql = $wpdb->prepare ('update `' . $wpdb->prefix . parent::$T . '` set iid=%d where iid=0 and bid=%d;', array (
							$this->ID,
							$wp_crm_buyer->get ()
							));
					$wpdb->query ($sql);
					$sql = $wpdb->prepare ('select count(1) from `' . $wpdb->prefix . parent::$T . '` where iid=%d;', $this->ID);
					if (!((int) $wpdb->get_var ($sql))) {
						if (!empty ($this->products))
						foreach ($this->products as $code => $quantity) {
							$product = new WP_CRM_Product ($code);
							$price = $product->get ('price', $quantity);
							$data = array (
								'bid' => $this->data['bid'],
								'cid' => $this->data['cid'],
								'pid' => $product->get (),
								'product' => $product->get ('title'),
								'code' => $code,
								'iid' => $this->ID,
								'price' => $price->get ('price'),
								'vat' => $price->get ('taxes'),
								'quantity' => $quantity,
								'stamp' => time ()
								);
							$formats = array ();
							$values = array ();
							foreach (parent::$K as $key) {
								$formats[] = '%s';
								$values[] = $data[$key];
								}
							$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . parent::$T . '` (' . implode(',', parent::$K) . ') values (' . implode (',', $formats) . ');', $values);
							echo "\nxxxx\n$sql\n";
							$wpdb->query ($sql);
							if (!($this->ID = $wpdb->insert_id))
								throw new WP_CRM_Exception (__CLASS__ . ' :: Saving Failure SQL: ' . "\n" . $sql . "\n", WP_CRM_Exception::Saving_Failure);
							}
						if (!empty ($this->meta_products))
						foreach ($this->meta_products as $code => $meta_product) {
							$data = array (
								'bid' => $this->data['bid'],
								'cid' => $this->data['cid'],
								'pid' => 0,
								'product' => $meta_product['name'],
								'code' => $code,
								'iid' => $this->ID,
								'price' => $meta_product['price'],
								'vat' => $meta_product['vat'],
								'quantity' => $meta_product['quantity'],
								'stamp' => time ()
								);
							$formats = array ();
							$values = array ();
							foreach (parent::$K as $key) {
								$formats[] = '%s';
								$values[] = $data[$key];
								}
							$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . parent::$T . '` (' . implode(',', parent::$K) . ') values (' . implode (',', $formats) . ');', $values);
							echo "\nxxxx\n$sql\n";
							$wpdb->query ($sql);
							if (!($this->ID = $wpdb->insert_id))
								throw new WP_CRM_Exception (__CLASS__ . ' :: Saving Failure SQL: ' . "\n" . $sql . "\n", WP_CRM_Exception::Saving_Failure);
							}
						}
#					else {
#						throw new WP_CRM_Exception (WP_CRM_Exception::Missing_Products);
#						}
					}
#				else {
#					throw new WP_CRM_Exception (WP_CRM_Exception::Missing_Buyer);
#					}
				}
#			else {
#				throw new WP_CRM_Exception (WP_CRM_Exception::Missing_Seller);
#				}
			}

		/*
		HINT: return the invoices
		*/
		
		return $ids;
		}

	public function delete () {
		global $wpdb;

		$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . self::$T . '` where id=%d;', $this->ID);
		$wpdb->query ($sql);

		$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where bid=%d and iid=0;', $this->buyer->get ());
		$wpdb->query ($sql);
		}

	/*
	INFO: view -> create PDF
	*/
	public function view ($echo = TRUE, $append = null, $back = FALSE) {
		global
			$wpdb,
			$current_user;

		$receipts = new WP_CRM_List ('WP_CRM_Payment', array ('iid=' . $this->ID, 'type=' . WP_CRM_Payment::Receipt));

		if (!$receipts->is ('empty')) {
			$INVOICE_BOTTOM = 158;
			$INVOICE_ROWS	= 11;
			}
		else {
			$INVOICE_BOTTOM = 220;
			$INVOICE_ROWS	= 21;
			}

		if (is_null($append))
			$pdf = new PDF();
		else
			$pdf = $append;

		$pdf->style ('h1');
		$pdf->Cell (0, 10, 'FACTURA' . ($this->is ('real') ? ' FISCALA' : ' PROFORMA'));
		$pdf->style ();
		$pdf->Ln ();
		$pdf->Cell (0, 5, 'Seria si numarul facturii ' . ($this->is ('real') ? '' : '(a se mentiona in momentul platii)') . ': ');
		$pdf->Ln ();
		$pdf->style ('h2');
		$pdf->style ('color: red');
		$pdf->Cell (0, 7, $this->data['series'] . ' ' . str_pad($this->data['number'], 5, 0, STR_PAD_LEFT));
		$pdf->style ();
		$pdf->Ln ();
		$pdf->Cell (0, 5, 'Data emiterii: ' . date('d-m-Y', $this->data['paiddate'] ? $this->data['paiddate'] : $this->data['stamp']) . ' / ID Client: ' . strtoupper(base_convert(2 * ($this->buyer->crypto()) + ($this->data['buyer'] == 'person' ? 0 : 1), 10, self::ID_Base)));
		/**
		 * HIST:
		 */
		#$pdf->Cell (0, 5, 'Data emiterii: ' . date('d-m-Y', $this->data['paiddate'] ? $this->data['paiddate'] : $this->data['stamp']) . ' / ID Client: ' . strtoupper(base_convert(2 * (($this->data['buyer'] == 'person' ? WP_CRM_Person::Padding : WP_CRM_Company::Padding) + $this->buyer->get()) + ($this->data['buyer'] == 'person' ? 0 : 1), 10, self::ID_Base)));
		$pdf->Ln ();
		$pdf->Line (11, 40, 199, 40);
		$pdf->Ln ();

		$logo = $this->seller->get ('logo path');
		if (file_exists ($logo)) {
			list ($width, $height) = getimagesize ($logo);
			
			if ($width * $height > 0) {
				$y_offset = 30;

				$x_offset = $y_offset * $width / $height;
				if ($x_offset > 95) {
					$x_offset = 95;
					$y_offset = $x_offset * $height / $width;
					}

				$pdf->Image ($logo, 200 - $x_offset,  10, $x_offset, $y_offset);
				}
			}

		$pdf->columns (5, array ('Vanzator:', 'Cumparator:'));
		$pdf->style ('h3');

		$data = array (
			$pdf->fix($this->seller->get ('name')),
			$pdf->fix($this->buyer->get('name'))
			);
		while (implode($data)) {
			$data =	$pdf->columns (6, $data);
			}

		$pdf->style ();

		$rows = array ();

		$data = array (
			'rc' => 'Nr. ord. reg. com. / an:',
			'uin' => 'CIF / CNP:',
			'default_vat' => 'Cota TVA (%):',
			'address' => 'Adresa:',
			'capital' => 'Capital social (lei):',
			'phone' => 'Telefon:',
			'email' => 'Email:',
			'bank' => 'Banca:',
			'account' => 'Cont:',
			'treasury' => 'Trezorerie:',
			'treasury_account' => 'Cont trezorerie:'
			);

		foreach ($data as $key => $label) {
			$value = $this->seller->get ($key);
			if ($value == '' || $value == $this->seller->get ()) continue;
			$rows[] = array ($pdf->fix ($label . ' ' . $value), '');
			}

		$data = array (
			'rc' => 'Nr. ord. reg. com. / an:',
			'uin' => 'CIF / CNP:',
#			'default_vat' => 'Cota TVA (%):',
			'address' => 'Adresa:',
#			'capital' => 'Capital social (lei):',
			'phone' => 'Telefon:',
			'email' => 'Email:',
			'bank' => 'Banca:',
			'account' => 'Cont:'
			);
		$c = 0;
		foreach ($data as $key => $label) {
			$value = $this->buyer->get ($key);
			if ($value == '' || $value == $this->buyer->get ()) continue;
			$rows[$c++][1] = $pdf->fix ($label . ' ' . $value);
			}

		$data = array (
			'mentions' => 'Nota:',
			);
		foreach ($data as $key => $label) {
			$value = $this->data[$key];
			if ($value == '' || $value == $this->ID) continue;
			$rows[$c++][1] = $pdf->fix ($label . ' ' . $value);
			}

		foreach ($rows as $row) {
			$pdf->columns (4, $row);
			}

#		$pdf->Line (11, 103, 199, 103);
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
		
		try {
			$wp_crm_coupon = new WP_CRM_Coupon ($this->data['coupon']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
		 	$wp_crm_coupon = null;
			}

		$volume_discount = 0;
		$coupon_discount = 0;

		foreach ($this->products as $product => $quantity) {
			ini_set ('display_errors', 1);
			try {
				$wp_crm_product = new WP_CRM_Product ($product);
				$wp_crm_price = $wp_crm_product->get ('price', array ('quantity' => $quantity));
				$wp_crm_reference = $wp_crm_product->get ('price', array ('quantity' => 1));
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$sql = $wpdb->prepare ('select product,price,vat from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where iid=%d and code=%s and quantity=%d;', array (
					$this->ID,
					$product,
					$quantity
					));
				$product = $wpdb->get_row ($sql);
				if (is_null ($product)) continue;
				$wp_crm_price = $wp_crm_reference = new WP_CRM_Price (array (
					'price' => $product->price,
					'vat' => $product->vat
					));
				$wp_crm_product = $product->product;
				}

			if ($wp_crm_reference->get ('full price') > $wp_crm_price->get ('full price'))
				$volume_discount += $quantity * ($wp_crm_reference->get ('full price') - $wp_crm_price->get ('full price'));

			if (is_object($wp_crm_coupon) && ($discount = $wp_crm_coupon->discount ($wp_crm_product, $quantity, $this->data['stamp']))) {
				$coupon_discount += (strpos ($discount, '%') !== FALSE) ?
					0.01 * $quantity * $wp_crm_price->get ('full price') * ((float) str_replace ('%', '', $discount)) :
					(float) $discount;
				}

			/*if (!$wp_crm_price->get('taxes', $quantity)) $vat_singularity = TRUE;
			$ump = $sign * $wp_crm_price->get('price');
			$val = $sign * $wp_crm_price->get('price') * $quantity;
			$vat = $wp_crm_price->get('vat', $this);
			$vat .= $vat ? '' : '*';
			$vvl = $sign * $wp_crm_price->get('taxes', $quantity) * $quantity;
			$tvl = $val + $vvl;*/
			if (!$wp_crm_reference->get('taxes', $quantity)) $vat_singularity = TRUE;
			$ump = $sign * $wp_crm_reference->get('price');
			$val = $sign * $wp_crm_reference->get('price') * $quantity;
			$vat = $wp_crm_reference->get('vat', $this);
			$vat .= $vat ? '' : '*';
			$vvl = $sign * $wp_crm_reference->get('taxes', $quantity) * $quantity;
			$tvl = $val + $vvl;
			
			$table_rows[] = array (
				$c++,
				($this->is('storno') ? 'STORNO ' : '') . is_object($wp_crm_product) ? $wp_crm_product->get('title', $this) : $wp_crm_product,
				'-',
				$quantity,
				sprintf('%.2f', $ump),
				sprintf('%.2f', $val),
				sprintf('%.2f', $vat),
				sprintf('%.2f', $vvl),
				sprintf('%.2f', $tvl)
				);

			$table_totals['value'] += $val;
			$table_totals['vat'] = $table_totals['vat'] < (float) $vat ? (float) $vat : $table_totals['vat'];
			$table_totals['vat value'] += $vvl;
			$table_totals['total value'] += $tvl;
			}

		if ($volume_discount) {
			$volume_discount_vat = $table_totals['vat'] ? ( ($table_totals['vat'] / (100 + $table_totals['vat'])) * $volume_discount ) : 0;

			$table_rows[] = array (
				$c++,
				($this->is('storno') ? 'STORNO ' : '') . 'Discount de volum',
				'-',
				1,
				sprintf('-%.2f', $volume_discount - $volume_discount_vat),
				sprintf('-%.2f', $volume_discount - $volume_discount_vat),
				sprintf('%.2f', $table_totals['vat']),
				sprintf('-%.2f', $volume_discount_vat),
				sprintf('-%.2f', $volume_discount)
				);

			$table_totals['value'] -= $volume_discount - $volume_discount_vat;
			$table_totals['vat value'] -= $volume_discount_vat;
			$table_totals['total value'] -= $volume_discount;
			}

		if ($coupon_discount) {
			$coupon_discount_vat = $table_totals['vat'] ? ( ($table_totals['vat'] / (100 + $table_totals['vat'])) * $coupon_discount ) : 0;

			$table_rows[] = array (
				$c++,
				($this->is('storno') ? 'STORNO ' : '') . 'Discount cupon ' . strtoupper($this->data['coupon']),
				'-',
				1,
				sprintf('-%.2f', $coupon_discount - $coupon_discount_vat),
				sprintf('-%.2f', $coupon_discount - $coupon_discount_vat),
				sprintf('%.2f', $table_totals['vat']),
				sprintf('-%.2f', $coupon_discount_vat),
				sprintf('-%.2f', $coupon_discount)
				);

			$table_totals['value'] -= $coupon_discount - $coupon_discount_vat;
			$table_totals['vat value'] -= $coupon_discount_vat;
			$table_totals['total value'] -= $coupon_discount;
			}

#		if ($this->ID) {
#			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'new_invoices` set value=%f,vat=%f where id=%d;', $table_totals['total value'], $table_totals['vat'], $this->ID));
#			}

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
			sprintf('%.2f ', $table_totals['value']) => 15,
			sprintf('%.2f  ', $table_totals['vat']) => 15,
			sprintf('%.2f   ', $table_totals['vat value']) => 20,
			sprintf('%.2f    ', $table_totals['total value']) => 0
			);
		$pdf->table ($table_head, array(), 4);

		$table_head = array (
			'Valoare totala de plata factura curenta (inclusiv TVA) - LEI -' => 116,
			'h2;color: red;align: center;'.sprintf('%.2f', $table_totals['total value']). ' lei' => 0
			);
		$pdf->table ($table_head, array(), 6);

		if (($table_totals['vat value'] > 0) && (date('Y', $this->data['paiddate']) > 2012)) {
			$table_head = array (
				'h2;color: blue;align: center;TVA la incasare' => 0
				);
			$pdf->table ($table_head, array(), 5);
			}

		$pdf->style ('small');
		if (!$this->seller->get('default_vat')) $vat_singularity = FALSE;
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
		$pdf->columns (4, array ('Numele delegatului: '.(is_object($this->delegate) ? $pdf->fix($this->delegate->get('name')) : ''), ''));
		$pdf->columns (4, array ('Act de identitate (BI/CI): '.(is_object($this->delegate) ? $this->delegate->get('id_type') : ''), ($this->data['paidby'] && ($this->data['paidby'] != 'none')) ? ('Data platii: '.date('d-m-Y', $this->data['paiddate'])) : ''));////
		$pdf->columns (4, array ('Seria: ' .(is_object($this->delegate) ? $this->delegate->get('id_series') : ''). ' numarul: ' . (is_object($this->delegate) ? $this->delegate->get('id_number') : '') . ' eliberat de: ' . (is_object($this->delegate) ? $this->delegate->get('id_issuer') : ''), $this->paid['value'] ? ('Suma platita: '.$this->data['paidvalue'].' lei'.($this->is('partial paid') ? (' / Rest de plata: '.($this->get('value') - $this->data['paidvalue']).'lei') : '')) : '' ));
		$pdf->columns (4, array ('Mijlocul de transport: POSTA ELECTRONICA', $this->data['paiddetails'] ? ('Detalii plata: '.$this->data['paiddetails']) : ''));

		if (!empty($this->receipts)) {
			$tmp = array();
			foreach ($this->receipts as $receipt) $tmp[] = $receipt->get('code').' ('.date('d-m-Y', $receipt->get('date')).')';
			$pdf->columns (4, array ('Data: '.date('d-m-Y', $this->data['stamp']).', Ora: '.date('H:i'), 'Chitante atasate: '.implode('; ', $tmp)));
			}
		else
			$pdf->columns (4, array ('Data: '.date('d-m-Y', $this->data['stamp']).', Ora: '.date('H:i'), ''));
		$pdf->Cell (0,4, $this->real ? 'Semnatura de primire:' : '');
		$pdf->Ln ();
		$pdf->Ln ();

		get_currentuserinfo();
		$wp_user = get_userdata($current_user->ID);
		if ($wp_user->ID) {
			$wp_user_data = explode("\n", $wp_user->user_description);
			$pdf->style ('em');
			$pdf->Cell (0,5, 'Intocmit de: '.$pdf->fix($wp_user->first_name.' '.$wp_user->last_name).', CNP '.trim($wp_user_data[0]).', C.I. '.trim($wp_user_data[1]));
			$pdf->style ();
			$pdf->Ln ();
			}

		$receipts = new WP_CRM_List ('WP_CRM_Payment', array ('iid=' . $this->ID, 'type=' . WP_CRM_Payment::Receipt));

		if (!$receipts->is ('empty')) { ## the receipt
			$receipts->sort ('time');
			$receipt = $receipts->get ('last');

			$pdf->Line (11, 200, 199, 200);
			$pdf->SetY (200);
			$pdf->Ln ();
			
			$pdf->style ('h1');
			$pdf->Cell (0, 12, 'CHITANTA');
			$pdf->style ();
			$pdf->Ln ();
			
			$pdf->Image (dirname(dirname(__FILE__)).'/images/companies/' . $this->seller->get() . '.png', 160, 200, 40, 20);

			$pdf->columns (4, array ('Seria si numarul: ' . $receipt->get('series'), ''));
			$pdf->columns (4, array ('Data (zi-luna-an): ' . date ('d-m-Y', $receipt->get('stamp')), 'Am primit de la'));

			$pdf->style ('h3');
			$pdf->columns (8, array ($this->seller->get('name'), $this->buyer->get('name')));
			$pdf->style ();

			$pdf->columns (4, array ('Nr. ord. reg. com. / an: '.$this->seller->get('rc'), 'Nr. ord. reg. com. / an: '.($this->buyer instanceof WP_CRM_Person ? '-' : $this->buyer->get('rc'))));
			$pdf->columns (4, array ('CIF/CNP: '.$this->seller->get('uin'), 'CIF/CNP: '.$this->buyer->get('uin')));
			$pdf->columns (4, array ('Cota TVA (%): '.$this->seller->get('default_vat'), 'Adresa: '.$this->buyer->get('address')));
			$pdf->columns (4, array ('Adresa: '.$this->seller->get('address'), ''));
			$pdf->columns (4, array ('Banca: '.$this->seller->get('bank'), 'Suma de '.$receipt->get('amount').' lei, (' . WP_CRM_Payment::num2wrd($receipt->get('amount')).')'));
			$pdf->columns (4, array ('Cont: '.$this->seller->get('account'), 'reprezentand contravaloare facturaii '.$this->get('series')));

			$pdf->Ln ();
			$pdf->Cell (0, 4, $pdf->fix('Casier: '.$wp_user->first_name . ' ' . $wp_user->last_name));
			$pdf->Ln ();
			$pdf->Cell (0, 4, 'Act de identitate seria si numarul: '.$wp_user_data[1].', CNP: '.$wp_user_data[0]);
			}

		if ($back) {
			$pdf->AddPage ();
			$pdf = $this->back (FALSE, $pdf);
			}

		if (is_null($append)) {
			if (!$echo) {
				$path = dirname(dirname(__FILE__)) . '/' . self::Cache . '/' . $this->get ('series') . '.pdf';
				$pdf->out ($path, 'F');
				return $path;
				}
			else
				$pdf->out ($this->get('series') . '.pdf');
			}
		else
			return $pdf;
		}

	public function back ($echo = TRUE, $append = null) {
		if (is_null($append))
			$pdf = new PDF();
		else
			$pdf = $append;

		$h = 5;

		$pdf->Image (dirname(dirname(__FILE__)) . '/images/companies/' . $this->seller->get() . '.png', 140, 10, 60, 30);

		$y = $pdf->GetY();
		$pdf->SetY ($y + 30);

		$pdf->style ('h3');
		$pdf->Cell (48, $h, $pdf->fix('MODALITĂȚI DE PLATĂ'), 'B', 0);
		$pdf->Cell (70, $h, $pdf->fix('UNDE PUTEȚI PLĂTI?'), 'B', 0);
		$pdf->Cell (70, $h, $pdf->fix('OBSERVAȚII'), 'B', 1);
		$pdf->style ();
		$x = $pdf->GetX();
		$y = $pdf->GetY();

		if ($this->seller->can ('payment', WP_CRM_Payment::Bank)) {
			$pdf->style ('strong');
			$pdf->MultiCell (50, $h, $pdf->fix('Transfer bancar din contul curent'), 0, 'C');
			$pdf->Image (dirname(__FILE__).'/payment/wire.png', $x + 12, $y + $h, 24, 24);
			$pdf->style ();
			$pdf->SetXY ($x + 48, $y);
			$pdf->MultiCell (70, $h, $pdf->fix($this->seller->get('bank') . "\n" . $this->seller->get('account')), 0);
			$pdf->SetXY ($x + 118, $y);
			$pdf->MultiCell (70, $h, $pdf->fix('Atât persoanele fizice cât și persoanele juridice care au internet banking pot achita direct din contul curent în contul ' . $this->seller->get('name') . '.'), 0);
			$y = $pdf->GetY();
			$y += 3 * $h;
			$pdf->SetY ($y);
			$pdf->Cell (0, $h, '', 'T');
			}
		if ($this->seller->can ('payment', WP_CRM_Payment::Card)) {
			$pdf->style ('strong');
			$pdf->SetX ($x);
			$pdf->MultiCell (50, $h, $pdf->fix('Cu cardul'), 0, 'C');
			$pdf->Image (dirname(__FILE__).'/payment/card.png', $x + 12, $y + $h, 24, 24);
			$pdf->style ();
			$pdf->SetXY ($x + 48, $y);
			$pdf->multicell (70, $h, $pdf->fix('Sediul ' . $this->seller->get('name') . "\n" . $this->seller->get('address')), 0);
			$pdf->setxy ($x + 118, $y);
			$pdf->multicell (70, $h, $pdf->fix('Persoanele fizice pot achita cu cardul prin intermediul POS la sediul nostru din ' . $this->seller->get('address')), 0);
			$y = $pdf->GetY();
			$y += 3 * $h;
			$pdf->SetY ($y);
			$pdf->Cell (0, $h, '', 'T');
			}
		if ($this->seller->can ('payment', WP_CRM_Payment::Treasury)) {
			$pdf->style ('strong');
			$pdf->SetX ($x);
			$pdf->MultiCell (50, $h, $pdf->fix('La trezorerie'), 0, 'C');
			$pdf->Image (dirname(__FILE__).'/payment/treasury.png', $x + 12, $y + $h, 24, 24);
			$pdf->style ();
			$pdf->SetXY ($x + 48, $y);
			$pdf->MultiCell (70, $h, $pdf->fix($this->seller->get('treasury') . "\n" . $this->seller->get('treasury_account')), 0);
			$pdf->SetXY ($x + 118, $y);
			$pdf->MultiCell (70, $h, $pdf->fix("Numai pentru institutii publice.\nPrin OP depus la institutia financiară cu care lucrați, ca orice altă plată obișnuită."), 0);
			$y = $pdf->GetY();
			$y += 3 * $h;
			$pdf->SetY ($y);
			$pdf->Cell (0, $h, '', 'T');
			}
		if ($this->seller->can ('payment', WP_CRM_Payment::Bank)) {
			$pdf->style ('strong');
			$pdf->SetX ($x);
			$pdf->MultiCell (50, $h, $pdf->fix('La banca'), 0, 'C');
			$pdf->Image (dirname(__FILE__).'/payment/bank.png', $x + 12, $y + $h, 24, 24);
			$pdf->style ();
			$pdf->SetXY ($x + 48, $y);
			$pdf->MultiCell (70, $h, $pdf->fix($this->seller->get('bank') . "\n" . $this->seller->get('account')), 0);
			$pdf->SetXY ($x + 118, $y);
			$pdf->MultiCell (70, $h, $pdf->fix("Persoane fizice:\nPuteți merge la orice sucursală " . $this->seller->get('bank') . " pentru a achita această factură. Trebuie să aveți asupra dumneavoastră buletinul și o copie a acestei facturi.\nPersoane juridice:\nPrin OP depus la banca cu care lucrați ca orice plată obișnuită."), 0);
			$y = $pdf->GetY();
			$y += $h;
			$pdf->SetY ($y);
			$pdf->Cell (0, $h, '', 'T');
			}
		if ($this->seller->can ('payment', WP_CRM_Payment::Cash)) {
			$pdf->style ('strong');
			$pdf->SetX ($x);
			$pdf->MultiCell (50, $h, 'Numerar', 0, 'C');
			$pdf->Image (dirname(__FILE__).'/payment/cash.png', $x + 12, $y + $h, 24, 24);
			$pdf->style ();
			$pdf->SetXY ($x + 48, $y);
			$pdf->MultiCell (70, $h, $pdf->fix('Sediul ' . $this->seller->get('name') . "\n" . $this->seller->get('address')), 0);
			$pdf->SetXY ($x + 118, $y);
			$pdf->MultiCell (70, $h, $pdf->fix('Atât persoanele fizce cât și cele juridice pot achita în numerar la casieria ' . $this->seller->get('name') . ' din strada ' . $this->seller->get('address')), 0);

			$y = $pdf->GetY();
			$y += 4 * $h;
			$pdf->SetY ($y);
			}
		$pdf->Ln();
		$pdf->style ('h3');
		$pdf->Cell (0, $h, $pdf->fix('TERMENE DE PLATĂ'), 'B', 1);
		$pdf->Style ();
		$pdf->MultiCell (0, $h, $pdf->fix("Conform termenului menționat în pagina evenimentului/cursului la care v-ați înscris.\nNeachitarea în acest termen atrage dupa sine anularea înregistrării dumneavoastră. Vă veți putea reînscrie ulterior, însă nu putem garanta că în acel moment vor mai fi locuri disponibile. În cazul în care, din diverse motive, nu puteți achita în termenul menționat, vă rugam să solicitați o programare de plată pe un termen extins."), 0);

		$y = $pdf->GetY();
		$y += 2 * $h;
		$pdf->SetY ($y);
		$pdf->style ('h3');
		$pdf->Cell (0, $h, 'TERMENI SI CONDITII', 'B', 1);
		$pdf->Style ();
		$pdf->MultiCell (0, $h, $pdf->fix("1. Taxa de participare nu include transportul sau cazarea cursanților pe durata evenimentului\n2. Detaliile administrative ale evenimentului sunt afișate pe website\n3. Înregistrarea la curs și achitarea acestei facturi vor servi ca dovadă a acceptării termenilor și condițiilor Bilete de Succes. Termenii și condițiile de participare acceptate la înscriere sunt aici: www.biletedesucces.ro/tos"), 0);


		$pdf->SetAutoPageBreak (FALSE);

		$pdf->SetY (260);
		$pdf->style ('small;strong');
		$pdf->style ('color: red');
		$pdf->Cell (0, $h-1, $pdf->fix($this->seller->get('name')), 'T', 1, 'C');
		$pdf->style ();
		$pdf->style ('small');
		$pdf->Cell (0, $h-1, $pdf->fix ('- Excelență, Integritate și Respect - '), 0, 1, 'C');
		$pdf->Cell (0, $h-1, $pdf->fix ($this->seller->get('address')), 0, 1, 'C');
		$pdf->Cell (0, $h-1, 'CUI: ' . $this->seller->get('uin').' ; ' . $this->seller->get('rc'), 0, 1, 'C');
		$pdf->Cell (0, $h-1, 'E-mail: ' . $this->seller->get('email') . '; Tel: ' . $this->seller->get('phone') . '; Fax: ' . $this->seller->get('fax'), 0, 1, 'C');
		$pdf->Cell (0, $h-1, 'www.biletedesucces.ro / ' . $this->seller->get('url'), 0, 1, 'C');

		if (is_null($append)) {
			if (!$echo) {
				$path = dirname(dirname(__FILE__)) . '/' . self::Cache . '/' . $this->get ('series') . '.pdf';
				$pdf->out ($path, 'F');
				return $path;
				}
			else
				$pdf->out ();
			}
		else
			return $pdf;
		}
		
	};
?>
