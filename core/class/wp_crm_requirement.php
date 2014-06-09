<?php
/**
 * The WP_CRM_Requirement class adds requirements to each product in order
 * to complete the buy-execute-deliver process. For example,
 * it doesn't allow delivery, until a contract is signed and uploaded.
 */
class WP_CRM_Requirement extends WP_CRM_Model {
	/**
	 * The attached database table, no prefix
	 * @var string
	 */
	public static $T = 'requirements';
	/**
	 * The attached database table structure. The ID column is added by default.
	 * @var array
	 */
	protected static $K = array (			// still wondering if this should be applied on instances or objects?
		'oid',					// the object that has this requirements attached
		'class',				// the class of the object that has this requirements attached
		'event',				// the WP_CRM_Event slug that triggered the action
		'title',				// WP_CRM_Requirement title
		'key',					// the property of the object passed inside the WP_CRM_Event's context
		'filter',				// an array of filters to be applied on the object(key) value
		'stamp'					// creation timestamp
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
			'pid:product'	=> 'Produs',
			'title'		=> 'Denumire',
			'entity'	=> 'Entitate',
			'type'		=> 'Tip entitate',
			'key'		=> 'Valoare',
			'filter'	=> 'Filtru'
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
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`class` varchar(64) NOT NULL DEFAULT \'\'',
		'`event` varchar(32) NOT NULL DEFAULT \'\'',
		'`title` text NOT NULL',
		'`key` varchar(64) NOT NULL DEFAULT \'\'',
		'`filter` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	private $object;

	public function is ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'met':
				$this->object = is_object ($this->object) ? $this->object : (is_object ($opts) ? $opts : new $this->data['class'] ((int) $this->data['oid']));

				if (!is_object ($this->object))
					return TRUE;
				if (($this->object->get () != $this->data['oid']) || (get_class ($this->object) != $this->data['class']))
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
	}
?>
