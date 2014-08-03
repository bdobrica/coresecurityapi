<?php
class WP_CRM_List {
	protected $list;

	public $F = array (); // for compatibility issues

	private $filter;
	/*
	TODO: use grouping
	*/
	private $group;
	private $class;

	public function __construct ($class, $filter = null) {
		$this->class = $class;
		$this->filter = $filter;
		$this->list = null;
		}

	private function load () {
		global $wpdb;
		$class = $this->class;
		$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . $class::$T . '` where ' . (empty($this->filter) ? 1 : implode (' and ', $this->filter)), null);
		$ids = $wpdb->get_col ($sql);
		if (!empty($ids))
			foreach ($ids as $id)
				if (!isset($this->list[$id])) {
					try {
						$this->list[$id] = new $this->class ((int) $id);
						}
					catch (WP_CRM_Exception $wp_crm_exception) {
						}
					}
		//$this->sort ('stamp', 'desc');
		$this->sort ('id', 'asc');
		if (!empty($this->list))
			reset ($this->list);
		}

	/*
	INFO: compare functions
	*/
	private static function id_compare ($a, $b) {
		if ($a->get () < $b->get ()) return -1;
		if ($a->get () > $b->get ()) return  1;
		return 0;
		}
	private static function stamp_compare ($a, $b) {
		if ($a->get ('stamp') < $b->get ('stamp')) return -1;
		if ($a->get ('stamp') > $b->get ('stamp')) return  1;
		return 0;
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'sizeif':
			case 'size if':
			case 'countif':
			case 'count if':
				$out = 0;
				if (is_null($this->list)) $this->load ();
				if (!empty ($this->list))
				foreach ($this->list as $object) {
					switch ((string) $opts['operator']) {
						case 'lt':
						case '<':
							if ($object->get ($opts['key']) < $opts['value']) $out++;
							break;
						case 'greater':
						case 'gt':
						case '>':
							if ($object->get ($opts['key']) > $opts['value']) $out++;
							break;
						default:
							if ($object->get ($opts['key']) == $opts['value']) $out++;
						}
					}
				return $out;
				break;
			case 'size':
			case 'count':
				if (is_null($this->list)) $this->load ();
				return count($this->list);
				break;
			case 'class':
			case 'type':
				return $this->class;
				break;
			case 'first':
				if (is_null($this->list)) $this->load ();
				if (!empty($this->list))
					reset ($this->list);
				$first = current ($this->list);
				return $first;
				break;
			case 'last':
				if (is_null($this->list)) $this->load ();
				$last = end ($this->list);
				if (!empty($this->list))
					reset ($this->list);
				return $last;
				break;
			case 'amount':
				if ($this->class != 'WP_CRM_Payment') return FALSE;
				if (is_null($this->list)) $this->load ();
				$amount = 0.00;
				if (!empty ($this->list))
					foreach ($this->list as $payment)
						$amount += (float) $payment->get ('amount');
				return $amount;
				break;
			case 'json':
				$out = array ();
				if (is_null($this->list)) $this->load ();

				foreach ($this->list as $object) $out[] = array (
					'id' => $object->get (),
					'name' => $object->get ('name')
					);

				return json_encode ($out);
				break;
			case 'filter':
				return empty ($this->filter) ? 1 : implode (' and ', $this->filter);
				break;
			}
		if (is_null($this->list)) $this->load ();
		return $this->list;
		}

	public function sort ($by = 'stamp', $opts = 'asc') {
		switch ((string) $by) {
			case 'time':
			case 'stamp':
				if (!empty($this->list))
					usort ($this->list, array ('WP_CRM_List', 'stamp_compare'));
				break;
			default:
				if (!empty($this->list))
					usort ($this->list, array ('WP_CRM_List', 'id_compare'));
				break;
			}
		if ($opts == 'desc' && (!empty($this->list)))
			array_reverse ($this->list);
		if (!empty($this->list))
			reset ($this->list);
		}

	public function reset () {
		$this->list = array ();
		}

	public function table () {
		$table = new WP_CRM_Table ();
		
		$class = $this->class;
		foreach ($class::$F['excerpt'] as $field => $label)
			$table->push ($label, WP_CRM_Table::Cols);

		if (!empty ($this->list))
		foreach ($this->list as $item) {
			$row = array ();
			foreach ($class::$F['excerpt'] as $field => $label) {
				$row[] = $item->get ($field);
				}
			$table->push ($row, WP_CRM_Table::Rows);
			}

		return $table;
		}

	public function is ($key = null) {
		switch ((string) $key) {
			case 'empty':
				if (is_null($this->list)) $this->load ();
				return empty($this->list) ? TRUE : FALSE;
				break;
			}
		}

	public function __destruct () {
		}
	}
/*

	private static function time_compare ($a, $b) {
		if ($a instanceof WP_CRM_Product) {
			if ($a->get('current stamp') < $b->get('current stamp')) return -1;
			if ($a->get('current stamp') > $b->get('current stamp')) return 1;
			}
		if ($a instanceof WP_CRM_Event) {
			if ($a->get('time') < $b->get('time')) return -1;
			if ($a->get('time') > $b->get('time')) return 1;
			}
		if ($a instanceof WP_CRM_Receipt) {
			if ($a->get('time') < $b->get('time')) return -1;
			if ($a->get('time') > $b->get('time')) return 1;
			}
		if ($a instanceof WP_CRM_Cash_Entry) {
			if ($a->get('time') < $b->get('time')) return -1;
			if ($a->get('time') > $b->get('time')) return 1;
			}
		return 0;
		}

	private static function name_compare ($a, $b) {
		if ($a instanceof WP_CRM_Person) {
			if ($a->get('name') < $b->get('name')) return -1;
			if ($a->get('name') > $b->get('name')) return 1;
			}
		return 0;
		}

	public function sort ($by, $direction = 'asc') {
		if ($by == 'time')
			usort ($this->list, array ('WP_CRM_List', 'time_compare'));
		if ($by == 'name')
			usort ($this->list, array ('WP_CRM_List', 'name_compare'));
		if ($direction == 'desc')
			$this->list = array_reverse ($this->list);
		}

	private $list;
	private $type;
	private $filter;
	private $group;

	public function __construct ($type, $filter = array()) {
		global $wpdb;
		if (empty($filter)) $filter = array(1);

		$this->type = $type;
		$this->filter = $filter;
		$this->list = array();
		$this->group = '';

		switch ($this->type) {
			case 'persons':
				$object = 'WP_CRM_Person';
				if (isset($this->filter['text'])) {
					$this->filter[] = $wpdb->prepare ('match (first_name,last_name,name,email) against (%s)', $this->filter['text']);
					unset ($this->filter['text']);
					}
				break;
			case 'products':
				$object = 'WP_CRM_Product';
				if (in_array ('active', $filter)) {
					if (in_array ('mine', $filter)) {
						$current_user = wp_get_current_user();
						$sql = $wpdb->prepare ('select series,number from `'.$wpdb->prefix.'products` where uid=%d and (state+flags)>0;', $current_user->ID);
						}
					else
						$sql = $wpdb->prepare ('select series,number from `'.$wpdb->prefix.'products` where (state+flags)>0;');

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
				break;
			case 'participants':
				$object = 'WP_CRM_Person';
				break;
			case 'invoices':
				$object = 'WP_CRM_Invoice';
				break;
			}

		$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . $object::$T . '` where ' . implode(' and ', $this->filter) . ($this->group ? $this->group : '') . ' order by id;');
		$objects = $wpdb->get_col ($sql);
		if (!empty($objects))
			foreach ($objects as $object_id)
				$this->list[] = new $object($object_id);



		if ($this->type == 'persons') {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'persons` where '.implode($filter).' group by uin;');
			$persons = $wpdb->get_results($sql);
			foreach ($persons as $person) $this->list[] = new WP_CRM_Person ($person);
			}
		if ($this->type == 'products') {
			if (in_array('active', $filter)) {
				if (in_array('mine', $filter)) {
					$current_user = wp_get_current_user();
					$sql = $wpdb->prepare ('select series,number from `'.$wpdb->prefix.'products` where uid=%d and (state+flags)>0;', $current_user->ID);
					}
				else
					$sql = $wpdb->prepare ('select series,number from `'.$wpdb->prefix.'products` where (state+flags)>0;');

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
			if (WP_CRM_Debug) echo "WP_CRM_List::construct:: sql( $sql )\n";
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
				if (isset($filter['until'])) {
					$where[] = 'stamp < '.strtotime($filter['until']);
					}

				if (isset($filter['sort'])) {
					$sort = explode(' ', $filter['sort']);
					if ($sort[0] == 'time') $order = 'stamp '.$sort[1];
					}
				if (isset($filter['between'])) {
					$filter['between'] = explode(' ', str_replace (' and ', ' ', $filter['between']));
					$where[] = $filter['between'][0].' < stamp and '.$filter['between'][1].' > stamp';
					}
				if (isset($filter['where']))
					$where[] = '('.$filter['where'].')';
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
				$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'receipts` where iid=%d;', $this->filter['invoice']->get('id'));
				$receipts = $wpdb->get_col($sql);
				if (!empty($receipts))
					foreach ($receipts as $receipt)
						$this->list[] = new WP_CRM_Receipt ($receipt);
				}
			if (isset($this->filter['month'])) {
				$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'receipts` where %d<stamp and stamp<%d;', $this->filter['month'], strtotime('next month', $this->filter['month']));
				$receipts = $wpdb->get_col($sql);
				if (!empty($receipts))
					foreach ($receipts as $receipt)
						$this->list[] = new WP_CRM_Receipt ($receipt);
				}
			}
		if ($this->type == 'cash entries') {
			if (isset($this->filter['month'])) {
				$sql = $wpdb->prepare ('select id from `'.$wpdb->prefix.'cash_registers` where %d<stamp and stamp<%d;', $this->filter['month'], strtotime('next month', $this->filter['month']));
				$entries = $wpdb->get_col($sql);
				if (!empty($entries))
					foreach ($entries as $entry)
						$this->list[] = new WP_CRM_Cash_Entry ((int) $entry);
				}
			}
		}

	public function get ($key = '', $options = null) {
		switch ($key) {
			case 'count':
			case 'size':
				return count ($this->list);
				break;
			case 'select':
				$out = '';
				if (!empty($this->list))
					foreach ($this->list as $object)
						$out .= '<option value="' .
								($object->get($options['value']))
							. '"' .
								($object->get($options['value']) == $options['selected'] ? ' selected' : '')
							. '>' .
								$object->get($options['text'])
							. '</option>' . "\n";
				break;
			default:
				$this->list;
			}
		}

	public function is ($key) {
		switch ($key) {
			case 'empty':
				return empty($this->list) ? TRUE : FALSE;
				break;
			default:
				return FALSE;
			}
		}

	public function __destruct () {
		}
	};
*/
?>
