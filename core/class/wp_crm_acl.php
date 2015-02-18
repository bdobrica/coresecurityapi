<?php
/**
 * Core of WP_CRM_*
 */

/**
 * ACL object - manages Access Control Lists
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_ACL extends WP_CRM_Model {
	public static $T = 'acls';
	protected static $K = array (
		'oid',			/** office id */
		'uid',			/** user id */
		'gid',			/** group id */
		'type',			/** user / group */
		'object',		/** object to access */
		'acl_new',		/** create object options */
		'acl_edit',		/** edit object options */
		'acl_view'		/** view object options */
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'object:array;object_list' => 'Obiect',
			'type:array;type_list' => 'Tip access',
			'acl_new:multi;new_list' => 'Adauga obiect',
			'acl_new:multi;edit_list' => 'Modifica obiect',
			'acl_new:multi;view_list' => 'Vezi obiect',
			),
		'edit' => array (
			'object:array;object_list' => 'Obiect',
			'type:array;type_list' => 'Tip access',
			'acl_new:multi;new_list' => 'Adauga obiect',
			'acl_new:multi;edit_list' => 'Modifica obiect',
			'acl_new:multi;view_list' => 'Vezi obiect',
			),
		'view' => array (
			'object:array;object_list' => 'Obiect',
			'type:array;type_list' => 'Tip access',
			'acl_new:multi;new_list' => 'Adauga obiect',
			'acl_new:multi;edit_list' => 'Modifica obiect',
			'acl_new:multi;view_list' => 'Vezi obiect',
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` varchar(13) NOT NULL DEFAULT \'0\'',
		'`uid` varchar(13) NOT NULL DEFAULT \'0\'',
		'`gid` varchar(64) NOT NULL DEFAULT \'\'',		/** groups are WP_ groups, so no numeric ID */
		'`type` enum(\'user\',\'group\') DEFAULT \'group\'',
		'`object` varchar(64) NOT NULL DEFAULT \'\'',
		'`acl_new` text NOT NULL',
		'`acl_edit` text NOT NULL',
		'`acl_view` text NOT NULL'
		);
	};
?>
