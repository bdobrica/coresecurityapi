<?php
/**
 * The WP_CRM_Requirement class adds requirements to each product in order
 * to complete the buy-execute-deliver process. For example,
 * it doesn't allow delivery, until a contract is signed and uploaded.
 */
class WP_CRM_Requirement extends WP_CRM_Model {
	const Buyer	= 1;
	const Seller	= 2;
	/**
	 * The attached database table, no prefix
	 * @var string
	 */
	public static $T = 'requirements';
	/**
	 * The attached database table structure. The ID column is added by default.
	 * @var array
	 */
	protected static $K = array (
		'pid',					// WP_CRM_Product ID, WP_CRM_Requirements are only for Products
							// WP_CRM_Requirements are only for: buyers (if person or company) and sellers (inventory, resources and tasks)
		'title',				// WP_CRM_Requirement title
		'entity',				// buyer || seller
		'type',					// person, company || inventory, resource, task
		'key',					// entity key that is observed
		'filter',				// entity filter applied on the entity key
		'stamp'					// timestamp
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
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`entity` int(1) NOT NULL DEFAULT 0',
		'`type` varchar(64) NOT NULL DEFAULT \'\'',
		'`key` varchar(64) NOT NULL DEFAULT \'\'',
		'`filter` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	}
?>
