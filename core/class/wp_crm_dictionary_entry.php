<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Dummy object. Shows how to create a new object.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Dictionary_Entry extends WP_CRM_Model {
	public static $T = 'dictionary_entries';
	protected static $K = array (
		'uid',
		'parent',
		'title',
		'description',
		'revision',				/** always, the term with revision 0 is searched. every time a revision is added, all other terms are pushed back with 1 */
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title'	=> 'Termen',
			'description:textarea' => 'Descriere',
			),
		'edit' => array (
			'title'	=> 'Termen',
			'description:textarea' => 'Descriere',
			),
		'view' => array (
			'title'	=> 'Termen',
			'description:textarea' => 'Descriere',
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
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`revision` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'INDEX (`parent`)',
		'INDEX (`revision`)',
		'FULLTEXT (`title`)'
		);
	};
?>
