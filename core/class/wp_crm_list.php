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
	/**
	 * DEBUG
	 */
	private $debug;

	public function __construct ($class, $filter = null) {
		$this->class = $class;
		$this->filter = $filter;
		$this->list = null;
		$this->debug = false;
		}

	public function flat ($data = null) {
		/*
		 * TODO: should test if $data is an array of the form array ( id => new $this->class ($id), ... )
		 */
		if (is_array ($data))
			$this->list = $data;
		else
		if (is_object ($data) && ($data instanceof $this->class))
			$this->list = array ($data);
		}

	public function grow () {
		$grow = array ();
		}

	private function load () {
		global $wpdb;
		$class = $this->class;
		$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . $class::$T . '` where ' . (empty($this->filter) ? 1 : implode (' and ', $this->filter)), null);
		if ($this->debug) WP_CRM::debug ($sql);
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
	private static function name_compare ($a, $b) {
		$r = strcmp (
				strtolower ($a->get ('last_name') . ' ' . $a->get ('first_name')),
				strtolower ($b->get ('last_name') . ' ' . $b->get ('first_name'))
				);
		return $r < 0 ? -1 : ($r > 0 ? 1 : 0);
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
						case 'lesser':
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
					'class' => $this->class,
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

	public function set ($key = null, $value = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'debug':
					$this->debug = is_null ($value) ? TRUE : $value;
					return TRUE;
					break;
				}
			}
		}

	public function sort ($by = 'stamp', $opts = 'asc') {
		switch ((string) $by) {
			case 'name':
				if (!empty($this->list))
					usort ($this->list, array ('WP_CRM_List', 'name_compare'));
				break;
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
			$this->list = array_reverse ($this->list);
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
?>
