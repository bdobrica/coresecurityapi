<?php
/**
 * The WP_CRM_Requirement class adds requirements to each product in order
 * to complete the buy-execute-deliver process. For example,
 * it doesn't allow delivery, until a contract is signed and uploaded.
 */
class WP_CRM_Requirement extends WP_CRM_Model {
	private static $TYPES = array (
		'string'	=> 'Valoare',
		'file'		=> 'Fisier',
		);
	/**
	 * The attached database table, no prefix
	 * @var string
	 */
	public static $T = 'requirements';
	/**
	 * The attached database table structure. The ID column is added by default.
	 * @var array
	 */
	protected static $K = array (			/** still wondering if this should be applied on instances or objects?	*/
		'rid',					/** the object that has this requirements attached			*/
		'class',				/** the class of the object that has this requirements attached		*/
		'event',				/** the WP_CRM_Event slug that triggered the action			*/
		'title',				/** WP_CRM_Requirement title						*/
		'description',				/** a short description of this requirement				*/
		'key',					/** the property of the object passed inside the WP_CRM_Event's context	*/
		'filter',				/** an array of filters to be applied on the object(key) value		*/
//		'manual',				/** manual overide of this requirement					*/
		'type',					/** the field type 							*/
		'defaults',				/** field defaults to							*/
		'stamp'					/** creation timestamp							*/
		);
	/**
	 * The attached database meta table structure. No ID column is added by default.
	 * The meta table is self::$T suffixed with '_meta'. Only if !empty ($M_K) then
	 * the class contains a meta table. Meta keys can be used together with normal keys.
	 * Meta keys have to indexes: oid (object id) and gid (group id)
	 * @var array
	 */
	protected static $M_K = array ();
	/**
	 * List of unique identifiers. If a database object matches them, then it will be updated.
	 * @var array
	 */
	protected static $U = array ();
	/**
	 * Additional table to handle instances of this object, relative to object types listed in
	 * this array.
	 * @var array (list of WP_CRM_Model decendants that provide a unique pair {instance_key, instance_value})
	 */
	protected static $I = array (
		'WP_CRM_User',
		'WP_CRM_Product',
		'WP_CRM_Company'
		);
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
			'parent:object'			=> 'Produs',
			'title'				=> 'Denumire',
			'description:textarea'		=> 'Descriere',
			'type:array;type_list'		=> 'Tip Camp',
			'defaults:file?type=file'	=> 'Valoare Implicita',
			),
		'edit' => array (
			'parent:object'			=> 'Produs',
			'title'				=> 'Denumire',
			'description:textarea'		=> 'Descriere',
			'type:array;type_list'		=> 'Tip Camp',
			'defaults:file?type=file'	=> 'Valoare Implicita',
			),
		'view' => array (
			'title'			=> 'Denumire',
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
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`class` varchar(64) NOT NULL DEFAULT \'\'',
		'`event` varchar(32) NOT NULL DEFAULT \'\'',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`key` varchar(64) NOT NULL DEFAULT \'\'',
		'`type` varchar(32) NOT NULL DEFAULT \'string\'',
		'`filter` text NOT NULL',
		'`defaults` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	private $instance;
	private $context;
	private $object;

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'parent':
					return (isset($this->data['class']) ? $this->data['class'] : 'null') . '-' . ((int) $this->data['rid']);
					break;
				case 'type_list':
					return self::$TYPES;
					break;
				case 'key':
					if (!$this->data['key']) {
						$key = parent::slug (remove_accents ($this->data['title']));
						if (strlen ($key) > 64) $key = substr ($key, 0, 64);
						$this->set ('key', $key);
						}
					return $this->data['key'];
				case 'field':
					switch ($this->data['type']) {
						case 'file':
							$field = array (
								'type' => 'file',
								'label' => $this->data['title'],
								'help' => $this->data['description'] ? : NULL,
								'default' => $this->data['defaults'] ? : NULL
								);
							break;
						case 'string':
							$field = array (
								'label' => $this->data['title'],
								'help' => $this->data['description'] ? : NULL,
								'default' => $this->data['defaults'] ? : NULL
								);
							break;
						}
					return $field;
				case 'form_structure':
					$fields = array ();
					if (!empty ($this->context))
					foreach ($this->context as $key => $default)
						$fields[$key] = array (
							'type' => 'hidden',
							'default' => $default
							);
					$fields['self'] = array (
							'type' => 'hidden',
							'default' => $this->get ('self'));
					$fields['instance'] = $this->get ('field');
					$out = array (
						array (
							'class' => 'instance',
							'fields' => $fields,
							),
						array (
							'class' => 'buttons',
							'fields' => array (
								'close' => array (
									'type' => 'close',
									'label' => 'Anuleaza &raquo;',
									),
								'next' => array (
									'type' => 'submit',
									'label' => 'Actualizeaza &raquo;',
									'method' => 'post',
									'action' => '',
									'callback' => 'WP_CRM_Requirement::process',
									)
								)
							),
						);
					return $out;
					break;
				}
			}
		return parent::get ($key, $opts);
		}

	public function set ($key = null, $value = null) {
		global $wpdb;
		if (is_string ($key)) {
			switch ($key) {
				case 'parent':
					list ($class, $rid) = explode ('-', stripslashes( trim (implode( '', explode ('\\', $value)))));
					$rid = (int) $rid;
					if (strtolower ($class) == 'null') return FALSE;
					if (!class_exists ($class)) return FALSE;
					if (!$rid) return FALSE;

					return parent::set (array (
						'class' => $class,
						'rid' => $rid
						));
					break;
				case 'value':
					if (empty ($this->context)) return FALSE;
					$user = new WP_CRM_User (FALSE);
					$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '_instance` (rid, wp_crm_user_id,wp_crm_product_id,wp_crm_company_id,value) values (%d,%d,%d,%d,%s);', array (
						$this->ID,
						$user->get (),
						$this->context['wp_crm_product_id'],
						$this->context['wp_crm_company_id'],
						$value
						));
					echo $sql;
					return TRUE;
					break;
				}
			}
		if (is_array ($key)) {
			if (isset ($key['parent'])) {
				list ($class, $rid) = explode ('-', stripslashes( trim (implode( '', explode ('\\', $key['parent'])))));
				$rid = (int) $rid;
				if ((strtolower ($class) != 'null') && class_exists ($class) && $rid) {
					$key['rid'] = $rid;
					$key['class'] = $class;
					}
				unset ($key['parent']);
				}
			}

		return parent::set ($key, $value);
		}

	public function is ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'met':
				$this->object = is_object ($this->object) ? $this->object : (is_object ($opts) ? $opts : new $this->data['class'] ((int) $this->data['rid']));

				if (!is_object ($this->object))
					return TRUE;
				if (($this->object->get () != $this->data['rid']) || (get_class ($this->object) != $this->data['class']))
					return TRUE;

				$filters = self::_unserialize ($this->data['filter']);
				if (is_string ($filters)) $filters = array ($filters);

				$errors = FALSE;
				if (!empty ($filters))
					foreach ($filters as $filter)
						$errors |= WP_CRM_Form::filter ($this->data['key'], $this->object->get ($this->data['key']), null, $filter);

				return $errors ? FALSE : TRUE;
				break;
			}
		}

	public function select ($key = null, $context = null) {
		if (!is_array ($context)) return FALSE;
		if (empty ($context)) return FALSE;
		$this->context = array ();
		foreach ($context as $item) {
			$class = get_class ($item);
			if (in_array ($class, static::$I))
				$this->context[strtolower($class) . '_id'] = $item->get ();
			}
		return TRUE;
		}

	public static function process ($data = null) {
		list ($object, $id) = explode ('-', $data['self']);
		if ($object != 'WP_CRM_Requirement') return FALSE;
		if (!((int) $id)) return FALSE;
		
		$requirement = new $object ((int) $id);
		$requirement->select ('instance', array (
			new WP_CRM_Product ((int) $data['wp_crm_product_id']),
			new WP_CRM_Company ((int) $data['wp_crm_company_id'])
			));
		$requirement->set ('value', $data['instance']->get ('url'));
		return TRUE;
		}
	}
?>
