<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Abstract class for defining connections between objects and database tables.
 *
 * @category Abstract
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
abstract class WP_CRM_Model {
	/**
	 * The attached database table, no prefix
	 * @var string
	 */
	public static $T;
	/**
	 * The attached database table structure. The ID column is added by default.
	 * @var array
	 */
	protected static $K = array ();
	/**
	 * The attached database meta table structure. No ID column is added by default.
	 * The meta table is self::$T suffixed with '_meta'. Only if !empty ($M_K) then
	 * the class contains a meta table. Meta keys can be used together with normal keys.
	 * Meta keys have to indexes: oid (object id) and gid (group id)
	 * Meta keys are only represented in lowercase!
	 * @var array
	 */
	protected static $M_K = array ();
	/**
	 * List of unique identifiers. If a database object matches them, then it will be updated.
	 * @var array
	 */
	protected static $U = array ();
	/**
	 * Pair of (actions, form elements).
	 * Form elements are defined as name[:type] => label, where
	 * 	name is a string containing the database key,
	 *	type is a string containing the type prefixed by :
	 *	label is a string containing the displayed label
	 *	Common types are: text (default), basket, checkbox, radio, select, button, submit, close, textarea, email, password, hidden, label, tos, date, seller, buyer, product, spread, matrix, file
	 * @see WP_CRM_Form::_render()
	 * @var array
	 */
	public static $F = array (
		'new' => array (
			),
		'edit' => array (
			),
		'view' => array (
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	/**
	 * List of column declarations for the table structure. The ID column is not added by default.
	 * @var array
	 */
	protected static $Q;

	/**
	 * The object's database ID
	 * @var int
	 */
	protected $ID;
	/**
	 * The object's data. Represented as a hash table.
	 * @var array
	 */
	protected $data;
	protected $group;

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
				throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
			}
		else
		if (is_string($data) && in_array ('series', static::$K) && in_array ('number', static::$K) && preg_match('/^[A-z]+[0-9]+$/', $data)) {
			$row = $wpdb->get_row ($wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where series=%s and number=%d;', array (
				self::parse ('series', $data),
				self::parse ('number', $data)
				)), ARRAY_A);
			if (!empty($row)) {
				$this->ID = (int) $row->id;
				$this->data = $row;
				}
			else
				throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
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

	public static function _unserialize ($data) {
		if (!is_string ($data)) return $data;
		if (preg_match ('/^a:\d+:{.*?}$/', $data)) {
			$out = unserialize ($data);
			return is_array ($out) ? $out : $data;
			}
		if (preg_match ('/^o:\d+:"[a-z0-9_]+":\d+:{.*?}$/', $data)) {
			$out = unserialize ($data);
			return is_object ($out) ? $out : $data;
			}
		return $data;
		}

	private function _meta_get ($key = null, $opts = null) {
		global $wpdb;

		$slug = static::slug ($key);
		if (empty (static::$M_K) || (!in_array ($slug, static::$M_K)))
			return FALSE;
		if (isset($this->data[$slug]))
			return $this->data[$slug];
		
		$sql = $wpdb->prepare ('select meta_value from `' . $wpdb->prefix . static::$T . '_meta` where oid=%d and meta_key=%s;', array (
				$this->ID,
				$slug
				));
		$values = $wpdb->get_col ($sql);

		if (empty ($values))
			return null;

		if (count ($values) == 1)
			return $this->data[$slug] = self::_unserialize($values[0]);

		$this->data[$slug] = array ();
		foreach ($values as $value)
			$this->data[$slug][] = self::_unserialize($value);

		return $this->data[$slug];
		}

	public function get ($key = null, $opts = null) {
		if (is_null($key)) return $this->ID;
		$slug = static::slug ($key);
		if ($slug == 'keys')
			return static::$K;
		if (isset($this->data[$slug]))
			return $this->data[$slug];
		#if (in_array ($slug, static::$K))
		#	return (string) $this->data[$slug];
		$value = $this->_meta_get ($key, $opts);
		return $value !== FALSE ? $value : $this->ID;

		return $this->ID;
		}

	private function _meta_set ($key = null, $value = null, $single = TRUE) {
		global $wpdb;
		if (is_null($key)) return FALSE;

		$slug = static::slug ($key);
		if (empty (static::$M_K) || (!in_array ($slug, static::$M_K)))
			return FALSE;

		if ($single) {
			$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . static::$T . '_meta` where oid=%d and meta_key=%s;', array (
					$this->ID,
					$slug
					));
			$ids = $wpdb->get_col ($sql);
			if (empty ($ids)) {
				$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '_meta` (oid, meta_key, meta_value) values (%d, %s, %s);', array (
						$this->ID,
						$slug,
						(is_array ($value) || is_object ($value)) ? serialize ($value) : $value
						));
				$wpdb->query ($sql);
				return TRUE;
				}
			if (count ($ids) == 1) {
				$sql = $wpdb->prepare ('update `' . $wpdb->prefix . static::$T . '_meta` set meta_value=%s where oid=%d and meta_key=%s;', array (
						(is_array ($value) || is_object ($value)) ? serialize ($value) : $value,
						$this->ID,
						$slug
						));
				$wpdb->query ($sql);
				return TRUE;
				}
			reset ($ids);

			$count = 0;
			foreach ($ids as $id) {
				if ($count < 1) {
					$sql = $wpdb->prepare ('update `' . $wpdb->prefix . static::$T . '_meta` set meta_value=%s where id=%d;', array (
							(is_array ($value) || is_object ($value)) ? serialize ($value) : $value,
							$id
							));
					$wpdb->query ($sql);
					}
				else {
					$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . static::$T . '_meta` where id=%d;', $id);
					$wpdb->query ($sql);
					}
				$count ++;
				}
			}
		else {
			$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '_meta` (oid, meta_key, meta_value) values (%d, %s, %s);', array (
					$this->ID,
					$slug,
					(is_array ($value) || is_object ($value)) ? serialize ($value) : $value
					));
			$wpdb->query ($sql);
			return TRUE;
			}
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
					if (in_array($slug, static::$M_K)) {
						$this->_meta_set ($key, $value);
						continue;
						}
					if (!in_array($slug, static::$K))
						continue;
						//throw new WP_CRM_Exception (__CLASS__ . ' :: Invalid Assignment', WP_CRM_Exception::Invalid_Assignment);
					$update[] = $slug . '=%s';
					$values[] = $value;
					$this->data[$slug] = $value;
					}

				$values[] = $this->ID;
				$sql = $wpdb->prepare (
					'update `' . $wpdb->prefix . static::$T . '` set ' . implode (',', $update) . ' where id=%d;',
					$values
					);
				$wpdb->query ($sql);
				}
			}
		else {
			$slug = static::slug ($key);
			if (in_array ($slug, static::$M_K)) {
				$this->_meta_set ($key, $value);
				return;
				}
			if (!in_array($slug, static::$K)) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_Assignment, __CLASS__ . ' (' . $key . ') :: Invalid Assignment');
			$this->data[$slug] = $value;

			if ($this->ID) {
				if (is_object ($value) && ($value instanceof WP_CRM_Model))
					$value = $value->get();
				$sql = $wpdb->prepare ('update `' . $wpdb->prefix . static::$T . '` set '.$slug.'=%s where id=%d;', $value, $this->ID);
				$wpdb->query ($sql);
				}
			}
		}

	public function field ($key, $context = 'edit') {
		$out = array (
			'info' => '',
			'label' => ''
			);

		if (empty (static::$F[$context])) return $out;

		foreach (static::$F[$context] as $info => $label) {
			if (strpos ($info, $key) === 0) {
				$seps = array ();
				if (($sep = strpos ($info, '?')) !== FALSE) $seps[] = $sep;
				if (($sep = strpos ($info, ':')) !== FALSE) $seps[] = $sep;
				if (($sep = strpos ($info, ';')) !== FALSE) $seps[] = $sep;
				$sep = !empty ($seps) ? min ($seps) : 0;
				if (($sep == 0) || (($sep > 0) && (substr ($info, 0, $sep) == $key))) {
					$out['info'] = $info;
					$out['label'] = $label;
					break;
					}
				}
			}
		return $out;
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

	/**
	 * Save handles the database INSERT / UPDATE for current object.
	 */
	public function save () {
		global $wpdb;
		if ($this->ID) throw new WP_CRM_Exception (WP_CRM_Exception::Object_Exists);

		if (in_array ('uid', static::$K) && !$this->data['uid']) {
			$current_user = wp_get_current_user ();
			if ($current_user->ID)
				$this->data['uid'] = $current_user->ID;
			}

		if (!empty(static::$U)) {
			$pieces = array ();
			$values = array ();
			foreach (static::$U as $key) {
				$pieces[] = $key . '=%s';
				$values[] = $this->data[$key];
				}

			$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . static::$T . '` where ' . implode (' and ', $pieces) . ';', $values);
			$id = $wpdb->get_var ($sql);

			if (!is_null($id)) {
				$this->ID = (int) $id;
				$pieces = array ();
				$values = array ();
				foreach (static::$K as $key) {
					$pieces[] = $key . '=%s';
					$values[] = $this->data[$key];
					}
				$values[] = $this->ID;
				$sql = $wpdb->prepare ('update `' . $wpdb->prefix . static::$T . '` set ' . implode (',', $pieces) . ' where id=%d;', $values);
				if ($wpdb->query ($sql) !== FALSE) return;
				}
			}

		if (!empty(static::$K)) {
			$formats = array ();
			$values = array ();
			foreach (static::$K as $key) {
				$formats[] = '%s';
				$values[] = $this->data[$key];
				}
			$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '` (' . implode(',', static::$K) . ') values (' . implode (',', $formats) . ');', $values);
			$wpdb->query ($sql);
			if (!($this->ID = $wpdb->insert_id)) throw new WP_CRM_Exception (WP_CRM_Exception::Saving_Failure, __CLASS__ . ' :: Saving Failure SQL: ' . "\n" . $sql . "\n");
			}
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

		if (empty (static::$Q)) return;

		$sql = $uninstall ?
			'drop table `' . $wpdb->prefix . static::$T . '`;' :
			'create table `' . $wpdb->prefix . static::$T . '` (' . implode (',', static::$Q) . ') engine=MyISAM default charset=utf8;';

		if ($wpdb->get_var ('show tables like \'' . $wpdb->prefix . static::$T . '\';') != ($wpdb->prefix . static::$T))
			$wpdb->query ($sql);

		/**
		 * Create META table if needed.
		 */
		if (empty (static::$M_K)) return;
		$sql = $uninstall ?
			'drop table `' . $wpdb->prefix . static::$T . '_meta`;' :
			'create table `' . $wpdb->prefix . static::$T . '_meta` (
				`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				`oid` int(11) NOT NULL DEFAULT 0,
				`gid` int(11) NOT NULL DEFAULT 0,
				`meta_key` varchar(64) NOT NULL DEFAULT \'\',
				`meta_value` text NOT NULL,
				KEY `oid` (`oid`),
				KEY `gid` (`gid`),
				KEY `meta_key` (`meta_key`)
				) engine MyISAM default charset=utf8;';
		if ($wpdb->get_var ('show tables like \'' . $wpdb->prefix . static::$T . '_meta\';') != ($wpdb->prefix . static::$T . '_meta'))
			$wpdb->query ($sql);
		}

	public function delete () {
		global $wpdb;
		if (!$this->ID) throw new WP_CRM_Exception (WP_CRM_Exception::Forgettable_Object);
		$wpdb->query ($wpdb->prepare ('delete from `' . $wpdb->prefix . static::$T . '` where id=%d;', (int) $this->ID));
		}

	public function __clone () {
		}

	public function __destruct () {
		}
	};
?>
