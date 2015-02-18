<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Object class to describe the machines used to solve tasks inside processes. Also usefull to enforce ACLs on users and groups.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Machine extends WP_CRM_Model {
	public static $T = 'machines';
	protected static $K = array (
		'oid',				/** the office id */
		'cid',				/** the company id */
		'title',			/** the name of the machine */
		'description',			/** the description of the machine */
		'tid',				/** the current task id */
		'begin',			/** the current task begin time */
		'end'				/** the current task estimated end time */
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title' => 'Denumire',
			'description' => 'Descriere'
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
		'`cid` int NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`tid` int NOT NULL DEFAULT 0',
		'`begin` int NOT NULL DEFAULT 0',
		'`end` int NOT NULL DEFAULT 0'
		);
	};
?>
