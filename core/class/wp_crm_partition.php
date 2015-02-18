<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Implements partition of objects. Uses two tables for this.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Partition extends WP_CRM_Model {
	public static $T = 'partitions';
	protected static $K = array (
		'oid',				/** the office id				*/
		'cid',				/** the company id 				*/
		'parent',			/** parent id					*/
		'title',			/** the name of the partition			*/
		'description',			/** some description				*/
		'objects',			/** the objects to which this partition applies	*/
		'type',				/** the type of the partition: auto/user	*/
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title' => 'Nume',
			'objects' => 'Obiecte'
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
		'`oid` int NOT NULL DEFAULT 0',
		'`uid` int NOT NULL DEFAULT 0',
		'`parent` int NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`objects` text NOT NULL',
		'`type` enum(\'auto\',\'user\') NOT NULL DEFAULT \'user\'',
		'`stamp` int NOT NULL DEFAULT 0'
		);
	};
?>
