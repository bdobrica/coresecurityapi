<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Opinion is a comment object. To separate it from WP comments. It can be attached
 * to almost all objects, like the Memo object. Also, can have replys.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Opinion extends WP_CRM_Model {
	public static $T = 'opinions';
	protected static $K = array (
		'uid',
		'rid',
		'type',
		'parent',
		'description',
		'stamp',
		'flags'
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
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`type` varchar(32) NOT NULL DEFAULT 0',
		'`description` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'`flags` int(1) NOT NULL DEFAULT 0',
		'INDEX(`parent`)',
		'FULLTEXT(`description`)'
		);
	};
?>
