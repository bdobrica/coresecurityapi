<?php
/**
 * Class describing the WP_CRM_Product object.
 */
class WP_CRM_Product extends WP_CRM_Model {
	const Pad_Number	= 3;				// the number from the SKU is padded to this length

	public static $T = 'products';
	protected static $K = array (
		'oid',
		'cid',
		'series',					// series & number constructs the SKU
		'number',
		'color',					// color to display the SKU
		'url',						// if the product has a remote description
		'title',					// the name of the product
		//'parent',					// parent product id (if exists)
		'pid',						// process id
		'stamp',
		'state',					// the product is active
		'flags'
		);

	protected static $M_K = array (				// it actually doesn't matter what meta_keys are passed here. for now.
		'struct',					// binary field containing the days of the course (lectures only)
		'hours',					// the number of hours the lecture supposed to take
		'theory',					// the number of hours the theoretical background was taught
		'corno',					// COR number
		'ancauth',					// ANC authorisation number
		'ancname',					// ANC authorized name
		'rnffpa',					// RNFFPA code
		'competences',					// the resulting competences
		'studies',					// required studies
		'uid',						// the owner id
		'rid',						// location id
		'tid',						// trainer id
		);

	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',		// office id
		'`cid` int(11) NOT NULL DEFAULT 0',		// company id
		'`series` varchar(6) NOT NULL DEFAULT \'\'',
 		'`number` int(11) NOT NULL DEFAULT 0',
		'`color` varchar(6) NOT NULL DEFAULT \'FFFFFF\'',
		'`url` text NOT NULL',
		'`title` mediumtext NOT NULL',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0.00',
		'`state` int(1) NOT NULL DEFAULT 0',
		'`flags` int(11) NOT NULL DEFAULT 0',
		'UNIQUE KEY `series` (`series`,`number`)'
		);
	public static $F = array (
		'new' => array (
			'code:code' => 'Cod',
			'title' => 'Denumire',
			'cid:array;sellers_list' => 'Companie',
			'pid:array;processes_list' => 'Proces',
			'pricematrix:matrix' => 'Pret',
			),
		'view' => array (
			'code' => 'Cod',
			'title' => 'Produs',
			'cid:company' => 'Companie'
			),
		'edit' => array (
			'code' => 'Cod',
			'title' => 'Denumire',
			'cid:array;sellers_list' => 'Companie',
			'pid:array;processes_list' => 'Proces',
			'pricematrix:matrix' => 'Pret',
			),
		);
	
	public function __construct ($data = null) {
		global $wpdb;
		
		if (is_string($data)) $data = array (
			'series' => parent::parse ('series', $data),
			'number' => parent::parse ('number', $data)
			);
		if (is_array($data) && isset($data['series']) && isset($data['number'])) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . self::$T . '` where series=%s and number=%d;', array (
				$data['series'],
				$data['number']));
			$data = $wpdb->get_row ($sql, ARRAY_A);
			if (is_null ($data)) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
			}
		if (is_array($data) && isset($data['code'])) {
			$data['series'] = parent::parse ('series', $data['code']);
			$data['number'] = parent::parse ('number', $data['code']);
			/**
			 * Should set a default value for series/number.
			 */
			}

		parent::__construct ($data);
		}

	public function is ($key = null, $opts = null) {
		global
			$wp_crm_buyer,
			$wpdb;
		/*
		DIFF: $key == 'trash' no longer needed
		*/
		switch ((string) $key) {
			case 'active':
				return $this->data['state'] ? TRUE : FALSE;
				break;
			case 'owned':
				$buyer = is_null ($opts) ? (is_object ($wp_crm_buyer) ? $wp_crm_buyer : null) : $opts;

				$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where pid=%d and bid=%d and buyer=%s;', array (
						$this->ID,
						$buyer->get (),
						$buyer->get ('type')
						));
				if ($wpdb->get_var ($sql)) return TRUE;
				break;
			case 'series owned':
				$buyer = is_null ($opts) ? (is_object ($wp_crm_buyer) ? $wp_crm_buyer : null) : $opts;

				$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where code like %s and bid=%d and buyer=%s;', array (
						$this->get ('series') . '%',
						$buyer->get (),
						$buyer->get ('type')
						));
				if ($wpdb->get_var ($sql)) return TRUE;
				break;
			}
		return FALSE;
		}

	public function buy ($buyer = null) {
		global
			$wp_crm_buyer,
			$wpdb;

		if (is_object ($wp_crm_buyer) && !is_object ($buyer))
			$buyer = $wp_crm_buyer;

		if (is_object ($buyer)) {
			if ($this->is ('series owned', $buyer)) throw new WP_CRM_Exception (0);

			$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . WP_CRM_Basket::$T . '` (bid, pid, buyer, code, stamp) values (%d, %d, %s, %s, %ld);', array (
					$buyer->get (),
					$this->ID,
					$buyer->get ('type'),
					$this->get ('code'),
					time ()
					));
			$wpdb->query ($sql);
			return TRUE;
			}
		}

	public function set ($key = null, $value = null) {
		global
			$wp_crm_buyer,
			$wpdb;

		if (is_string ($key)) {
			if (strpos ($key, 'buyer_') === 0) {
				$key = substr ($key, 6);
				if (is_object ($wp_crm_buyer))
					return $wp_crm_buyer->set ($key, $value);
				}
			else {
				}
			$key = str_replace (array (
					'location',
					'user',
					'trainer',
					),
					array (
					'rid',
					'uid',
					'tid',
					), $key);

			if ($key == 'code') {
				$key = array (
					'series' => parent::parse ('series', $value),
					'number' => parent::parse ('number', $value)
					);
				$value = null;
				}
			}

		if (is_array ($key)) {
			$keys = array_keys ($key);
			if (in_array ('code', $keys)) {
				$key['series'] = parent::parse ('series', $key['code']);
				$key['number'] = parent::parse ('number', $key['code']);
				$key['code'] = null;
				}
			$buyer = array ();
			if (!empty ($keys))
				foreach ($keys as $local_key)
					if (strpos ($local_key, 'buyer_') === 0)
						$buyer[$local_key] = $key[$local_key];

			if (!empty ($buyer) && $wp_crm_buyer)
				$wp_crm_buyer->set ($buyer);

			return parent::set ($key);
			}

		if (is_object ($value)) $value = $value->get ();
		return parent::set ($key, $value);
		}

	public function field ($key, $context = 'edit') {
		global $wp_crm_buyer;

		if (strpos ($key, 'buyer_') === 0) {
			$key = substr ($key, 6);
			if (!is_object ($wp_crm_buyer))
				return array ('info' => '', 'label' => '');
			$field = $wp_crm_buyer->field ($key, $context);
			$field['info'] = 'buyer_' . $field['info'];
			return $field;
			}
		return parent::field ($key, $context);
		}

	public function get ($key = null, $opts = null) {
		global
			$wp_crm_buyer,
			$wpdb;

		if (is_string ($key)) {
			if (strpos ($key, 'buyer_') === 0) {
				$key = substr ($key, 6);
				if (is_object ($wp_crm_buyer)) {
					return $wp_crm_buyer->get ($key);
					}
				}
			else {
				}
			}

		if (is_object ($opts) && ($opts instanceof WP_CRM_Invoice))
			switch ((string) $key) {
				case 'name':
				case 'short name':
					$sql = $wpdb->query ('select product from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where pid=%d and iid=%d;', array (
						$this->ID,
						$opts->get ()
						));
					return $wpdb->get_var ($sql);
					break;
				}

		switch ((string) $key) {
			case 'code':
				return $this->get('series') . str_pad($this->get('number'), self::Pad_Number, '0', STR_PAD_LEFT);
				break;
			case 'name':
			case 'short name':
				return $this->get ('title');
				break;
			case 'price':
				return new WP_CRM_Price ($this, isset($opts['quantity']) ? $opts['quantity'] : null, isset($opts['date']) ? $opts['date'] : null);
				break;
			case 'clients':
				$sql = $wpdb->prepare ('select sum(quantity) from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where pid=%d;', $this->ID);
				return (int) $wpdb->get_var ($sql);
				break;
			case 'confirmed clients':
				$sql = $wpdb->prepare ('select sum(quantity) from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where pid=%d and iid in (select id from `' . $wpdb->prefix . WP_CRM_Invoice::$T . '` where flags&' . WP_CRM_Invoice::Real_Invoice . '=' . WP_CRM_Invoice::Real_Invoice .');', $this->ID);
				return (int) $wpdb->get_var ($sql);
				break;
			case 'pricematrix':
				$wp_crm_price = new WP_CRM_Price ($this);
				$matrix = $wp_crm_price->get ('matrix');
				return $matrix;
				break;
			case 'sellers_list':
				global $current_user;
				$out = array ();

				$current_user = wp_get_current_user ();
				$wp_crm_offices = get_user_meta ($current_user->ID, '_wp_crm_offices', TRUE);
				$wp_crm_office_query = is_numeric ($wp_crm_offices) ? sprintf ('oid=%d', $wp_crm_offices) : (!empty($wp_crm_offices) ? sprintf ('oid in (%s)', implode (',', $wp_crm_offices)) : '1');

				$sql = $wpdb->prepare ('select id,name from `' . $wpdb->prefix . WP_CRM_Company::$T . '` where ' . $wp_crm_office_query, null);
				$rows = $wpdb->get_results ($sql);
				if ($rows)
					foreach ($rows as $row)
						$out[$row->id] = $row->name;

				return $out;
				break;
			case 'processes_list':
				global $current_user;
				$out = array ();

				$current_user = wp_get_current_user ();
				$wp_crm_offices = get_user_meta ($current_user->ID, '_wp_crm_offices', TRUE);
				$wp_crm_office_query = is_numeric ($wp_crm_offices) ? sprintf ('oid=%d', $wp_crm_offices) : (!empty($wp_crm_offices) ? sprintf ('oid in (%s)', implode (',', $wp_crm_offices)) : '1');

				$sql = $wpdb->prepare ('select id,title from `' . $wpdb->prefix . WP_CRM_Process::$T . '` where ' . $wp_crm_office_query, null);
				$rows = $wpdb->get_results ($sql);
				if ($rows)
					foreach ($rows as $row)
						$out[$row->id] = $row->title;

				return $out;
				break;
			}
		return parent::get ($key, $opts);

		/*
		HIST: to remember the properties
		*/
		global $wpdb;
		if (is_string ($value) && $this->ID) {
			if ($key == 'name') {
				$name = $wpdb->get_var ($wpdb->prepare ('select title from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				return is_null($name) ? $this->name : $name;
				}
			if ($key == 'short name') {
				$out = $wpdb->get_var ($wpdb->prepare ('select title from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				$out = trim(preg_replace ('/\(.+\).*$/', '', $out));
				$out = wp_crm_product_types ($out, 'remove');
				$out = wp_crm_product_cities ($out, 'remove');
				$out = trim(preg_replace ('/-.*$/', '', $out));
				return $out;
				//return preg_replace (array ('/^[^-]+- /', '/,.*$/', '/\s*Weekend\s*/'), '', str_replace(array ('&#8211;'), array('-'), trim(preg_replace ('/\(.+\)/', '', $name))));
				}
			if ($key == 'location') {
				$location = $wpdb->get_var ($wpdb->prepare ('select rid from `'.$wpdb->prefix.'products` where pid=%d and series=%s and number=%d;', $this->ID, wp_crm_extract_series ($value), wp_crm_extract_number($value)));
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
			if ($key == 'missing participants') {
				$all_participants = $wpdb->get_var($wpdb->prepare ('select sum(quantity) from `'.$wpdb->prefix.'new_basket` where code=%s;', strtoupper($value)));
				$found_participants = $wpdb->get_var($wpdb->prepare('select count(1) from `'.$wpdb->prefix.'clients` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number($value)));
				return $all_participants - $found_participants;
				}
			if ($key == 'missing situation') {
				$out = array ();
				$baskets = $wpdb->get_results ($wpdb->prepare ('select * from `'.$wpdb->prefix.'new_basket` where code=%s;', strtoupper($value)));
				foreach ($baskets as $basket) {
					$found = $wpdb->get_var($wpdb->prepare('select count(1) from `'.$wpdb->prefix.'clients` where series=%s and number=%d and iid=%d;', array (
						wp_crm_extract_series ($value),
						wp_crm_extract_number($value),
						$basket->iid)));
					if ($found != $basket->quantity) {
						$invoice = new WP_CRM_Invoice ((int) $basket->iid);
						$out[] = '<strong>'.$invoice->get('invoice_series').$invoice->get('invoice_number').'</strong> [ID: '.$basket->iid.'] has '.$basket->quantity.' participants, but only '.$found.' were found!';
						}
					}
				return '<ul><li>'.implode('</li><li>', $out).'</li></ul>';
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
			if ($key == 'corno') {
				return (int) $wpdb->get_var ($wpdb->prepare ('select corno from `'.$wpdb->prefix.'products` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number ($value)));
				}
			if ($key == 'company') {
				return (int) $wpdb->get_var ($wpdb->prepare ('select cid from `'.$wpdb->prefix.'products` where series=%s and number=%d;', wp_crm_extract_series ($value), wp_crm_extract_number ($value)));
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
		if ((get_class($value) == 'WP_CRM_Person') && $key == 'invoice') {
			if (!$this->ID) return 0;
			return $wpdb->get_var ($wpdb->prepare('select iid from `'.$wpdb->prefix.'clients` where series=%s and number=%d and uin=%d;', array (
				$this->current['series'],
				$this->current['number'],
				$value->get('uin')
				)));
			}

		if ($key == 'keys') return $this->keys;
		if ($key == 'name') return $this->name;
		if ($key == 'nice name') return str_replace(array ('&#8211;'), array('-'), trim(preg_replace ('/\(.+\)/', '', $this->name)));
		if ($key == 'short name') {
			$out = $this->name;
#			$out = trim(preg_replace ('/\(.+\).*$/', '', $out));
#			$out = wp_crm_product_types ($out, 'remove');
#			$out = wp_crm_product_cities ($out, 'remove');
#			$out = trim(preg_replace ('/-.*$/', '', $out));
			return $out;
			}
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

		if ($key == 'current begin') {
			if (!is_array($this->current)) return FALSE;
			$structure = decbin($this->get('structure', $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT)));
			$structure = str_pad ($structure, 21, '0', STR_PAD_LEFT);
			$day = 20 - (int) strrpos ($structure, '1');
			return $this->current['stamp'] + $day * 86400;
			}

		if ($key == 'current cnfpa begin') {
			if (!is_array($this->current)) return FALSE;
			$structure = decbin($this->get('structure', $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT)));
			$structure = str_pad ($structure, 21, '0', STR_PAD_LEFT);
			$dayb = 20 - (int) strrpos ($structure, '1');
			$daye = 20 - (int) strpos ($structure, '1');

			if ( 8*($daye - $dayb + 1) < $this->current['hours'] )	
				$dayb = $daye - ceil($this->current['hours']/8) + 1;

			return $this->current['stamp'] + $dayb * 86400;
			}

		if ($key == 'current end' || $key == 'current cnfpa end') {
			if (!is_array($this->current)) return FALSE;
			$structure = decbin($this->get('structure', $this->current['series'].str_pad($this->current['number'], 3, 0, STR_PAD_LEFT)));
			$structure = str_pad ($structure, 21, '0', STR_PAD_LEFT);
			$day = 20 - (int) strpos ($structure, '1');
			return $this->current['stamp'] + $day * 86400;
			}

		if ($key == 'current corno' || $key == 'current cor') return is_array($this->current) ? $this->current['corno'] : FALSE;

		if ($key == 'current hours') return is_array($this->current) ? (int) $this->current['hours'] : FALSE;

		if ($key == 'current theory') return is_array($this->current) ? (int) $this->current['theory'] : FALSE;

		if ($key == 'current anc auth') return is_array($this->current) ? $this->current['ancauth'] : FALSE;

		if ($key == 'current anc name' || $key == 'current ancname') return is_array($this->current) ? $this->current['ancname'] : FALSE;
		
		if ($key == 'current rnffpa') return is_array($this->current) ? $this->current['rnffpa'] : FALSE;

		if ($key == 'current anc representative' || $key == 'current ancrep') return is_array($this->current) ? $this->current['ancrep'] : FALSE;

		if ($key == 'current studies') return is_array($this->current) ? $this->current['studies'] : FALSE;
		
		if ($key == 'current competences') return is_array($this->current) ? $this->current['competences'] : FALSE;
		
		if ($key == 'current company' || $key == 'company') return is_array($this->current) ? $this->current['company'] : FALSE;

		if ($key == 'current color' || $key == 'color') return is_array($this->current) ? ('#'.$this->current['color']) : '#FFFFFF';


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
		if ($key == 'featured image' || $key == 'image')
			return has_post_thumbnail($this->ID) ? wp_get_attachment_image_src (get_post_thumbnail_id ($this->ID ), 'single-post-thumbnail') : FALSE;
		return $this->ID;
		}

	public function plan ($series, $date) {
		global $wpdb;
		$series = strtoupper($series);
		$number = 1 + $wpdb->get_var($wpdb->prepare('select max(number) from `'.$wpdb->prefix.'products` where pid=%d;', $this->ID));
		$data = $wpdb->get_row($wpdb->prepare('select color,title,price,rcost,vat,struct,corno,ancname,rnffpa,uid,rid,tid,cid from `'.$wpdb->prefix.'products` where pid=%d and uid>0 order by id desc limit 0,1;', $this->ID));
		$stamp = is_numeric($date) ? ((int) $date) : strtotime($date);
		$sql = $wpdb->prepare('insert into `'.$wpdb->prefix.'products` (series,number,pid,stamp,color,title,price,rcost,vat,struct,corno,ancname,rnffpa,uid,rid,tid,cid) values (%s,%d,%d,%d,%s,%s,%f,%f,%f,%d,%s,%s,%s,%d,%d,%d,%d);', array (
			$series,
			$number,
			$this->ID,
			$stamp,
			$data->color,
			$data->title,
			(float) $data->price,
			(float) $data->rcost,
			(float) $data->vat,
			(int) $data->struct,
			$data->corno,
			$data->ancname,
			$data->rnffpa,
			(int) $data->uid,
			(int) $data->rid,
			(int) $data->tid,
			(int) $data->cid));
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
?>
