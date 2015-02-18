<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Instance object. Used for activities.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Instance extends WP_CRM_Model {
	public static $T = 'instances';
	protected static $K = array (
		'uid',			/* user id */
		'oid',			/* office id */
		'rid',			/* reference id */
		'type',			/* reference class (type) */
		'instance'		/* instance data, as serialized associative array */
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
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
	protected static $Q = array (
		'`id` int NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int NOT NULL DEFAULT 0',
		'`oid` int NOT NULL DEFAULT 0',
		'`rid` int NOT NULL DEFAULT 0',
		'`type` varchar(32) NOT NULL DEFAULT \'\'',
		'`instance` text NOT NULL',
		'INDEX(`uid`)',
		'INDEX(`oid`)',
		'INDEX(`rid`)',
		'INDEX(`type`)',
		'UNIQUE(`uid`,`oid`,`rid`,`type`)'
		);
	};
?>
