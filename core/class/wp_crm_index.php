<?php
class WP_CRM_Index {
	/*
	* INFO: can't be a WP_CRM_Model extension as 
	* I'd like to introduce it's save() method into
	* the save() method of the WP_CRM_Model
	* A new key for the WP_CRM_Model::$F should
	* be defined to act as a reference for the
	* indexed ->$data keys
	*/
	/*
	* NOTE: remember to add a rule in the 
	* WP_CRM_List class so this class is "listable"
	*/
	public static $T = 'indexes';
	protected static $K = array (
		'oid',
		'class',
		'object',
		'content'
		);
	protected static $F = array (
		'new' => array (
			),
		'view' => array (
			),
		'edit' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY auto_increment',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`class` varchar(32) NOT NULL DEFAULT \'\'',
		'`object` varchar(44) NOT NULL DEFAULT \'\'',
		'`content` text NOT NULL',
		'INDEX (oid)',
		'INDEX (class)',
		'FULLTEXT (content)'
		);

	protected $ID;
	protected $data;
	/*
	* NOTE: while the class is a copy of WP_CRM_Model,
	* it should have only a constructor and a getter,
	*/
	/*
	* NOTE: try to minimize the memory by not selecting
	* the content. Or should we? It would be easy to
	* highlight.
	*/

	public function __construct ($data = null) {
		global $wpdb;

		if (is_null($data)) {
			}
		else
		if (is_numeric($data)) {
			$row = $wpdb->get_row ($wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where id=%d;', (int) $data), ARRAY_A);
			if (!empty($row)) {
				$this->ID = (int) $data;
				$this->data = $row;
				}
			else
				throw new WP_CRM_Exception (__CLASS__ . ' :: Invalid ID', WP_CRM_Exception::Invalid_ID);
			}
		else
		if (is_string($data) && in_array ('series', static::$K) && in_array ('number', static::$K) && preg_match('/^[A-z]+[0-9]+$/', $data)) {
			$row = $wpdb->get_row ($wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where series=%s and number=%d;', array (
				self::parse ('series', $data),
				self::parse ('number', $data)
				)), ARRAY_A);
			if (!empty($row)) {
				$this->ID = (int) $row->ID;
				$this->data = $row;
				}
			else
				throw new WP_CRM_Exception (__CLASS__ . ' :: Invalid ID', WP_CRM_Exception::Invalid_ID);
			}
		else
		if (is_array($data)) {
			foreach (static::$K as $key)
				if (isset($data[$key]))
					$this->data[$key] = $data[$key];

			if (isset($data['id'])) $this->ID = (int) $data['id'];
			}
		}

	public static function slug ($key) {
		return str_replace(array(' ', '-'), '_', strtolower(trim($key)));
		}

	public function get ($key = null, $opts = null) {
		if (is_null($key)) return $this->ID;
		$slug = static::slug ($key);
		if ($slug == 'keys') return static::$K;
		if (isset($this->data[$slug]))
			return $this->data[$slug];
		#if (in_array ($slug, static::$K))
		#	return (string) $this->data[$slug];
		return $this->ID;
		}

	public function set ($key = null, $value = null) {
		global $wpdb;

		if (is_array ($key)) {
			if (!empty ($key)) {
				$keys = $key;
				$update = array ();
				$values = array ();
			
				foreach ($keys as $key => $value) {
					$slug = static::slug ($key);
					if (!in_array($slug, static::$K))
						continue;
						//throw new WP_CRM_Exception (__CLASS__ . ' :: Invalid Assignment', WP_CRM_Exception::Invalid_Assignment);
					$update[] = $slug . '=%s';
					$values[] = $value;
					$this->data[$slug] = $value;
					}

				$values[] = $this->ID;
				$wpdb->query ($wpdb->prepare (
					'update `' . $wpdb->prefix . static::$T . '` set ' . implode (',', $update) . ' where id=%d;',
					$values
					));
				}
			}
		else {
			$slug = static::slug ($key);
			if (!in_array($slug, static::$K)) throw new WP_CRM_Exception (__CLASS__ . ' :: Invalid Assignment', WP_CRM_Exception::Invalid_Assignment);
			$this->data[$slug] = $value;

			if ($this->ID) {
				if (is_object ($value) && ($value instanceof WP_CRM_Model))
					$value = $value->get();
				$wpdb->query ($wpdb->prepare ('update `' . $wpdb->prefix . static::$T . '` set '.$slug.'=%s where id=%d;', $value, $this->ID));
				}
			}
		}

	public function json ($data = FALSE) {
		$out = array (
			'type' => 'object',
			'class' => get_class ($this),
			'id' => $this->ID
			);

		if ($data)
			$out['data'] = $this->data;

		return json_encode ($out);
		}

	public function save () {
		global $wpdb;
		if ($this->ID) throw new WP_CRM_Exception (__CLASS__ . ' :: Object Exists', WP_CRM_Exception::Object_Exists);
		$formats = array ();
		$values = array ();
		foreach (static::$K as $key) {
			$formats[] = '%s';
			$values[] = $this->data[$key];
			}
		$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '` (' . implode(',', static::$K) . ') values (' . implode (',', $formats) . ');', $values);
		$wpdb->query ($sql);
		if (!($this->ID = $wpdb->insert_id)) throw new WP_CRM_Exception (__CLASS__ . ' :: Saving Failure SQL: ' . "\n" . $sql . "\n", WP_CRM_Exception::Saving_Failure);
		}

	public static function parse ($key = null, $from = null) {
		switch ($key) {
			case 'series':
				return trim(preg_replace('/[^A-Z]+/','',strtoupper($from)));
				break;
			case 'number':
				return intval(preg_replace('/[^0-9]+/','',$from));
				break;
			case 'spell number':
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
				break;
			default:
				return null;
			}
		}

	public static function install ($uninstall = FALSE) {
		global $wpdb;

		$sql = $uninstall ?
			'drop table `' . $wpdb->prefix . static::$T . '`;' :
			'create table `' . $wpdb->prefix . static::$T . '` (' . implode (',', static::$Q) . ') engine=MyISAM default charset=utf8;';

		if ($wpdb->get_var ('show tables like \'' . $wpdb->prefix . static::$T . '\';') != ($wpdb->prefix . static::$T)) {
			#echo $sql;
			$wpdb->query ($sql);
			}
		}

	public function delete () {
		global $wpdb;
		if (!$this->ID) throw new WP_CRM_Exception (__CLASS__ . ' :: Forgettable Object', WP_CRM_Exception::Forgettable_Object);
		$wpdb->query ($wpdb->prepare ('delete from `' . $wpdb->prefix . static::$T . '` where id=%d;', (int) $this->ID));
		}

	public function __clone () {
		}

	public function __destruct () {
		}
	}
?>
