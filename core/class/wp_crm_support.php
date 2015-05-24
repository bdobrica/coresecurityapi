<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Supports are resources for courses.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
define ('WP_CRM_SUPPORT_PATH', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '.resources');

class WP_CRM_Support extends WP_CRM_Model {
	public static $T = 'supports';
	protected static $K = array (
		'oid',
		'cid',
		'uid',
		'parent',
		'path',
		'title',
		'description',
		'hash',
		'type',
		'length',
		'atime',
		'ctime',
		'mtime',
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title'	=> 'Denumire',
			'description' => 'Descriere',
			'type' => 'Tip'
			),
		'edit' => array (
			'title'	=> 'Denumire',
			'description' => 'Descriere',
			'type' => 'Tip'
			),
		'view' => array (
			'title'	=> 'Denumire',
			'description' => 'Descriere',
			'type' => 'Tip'
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
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`path` text NOT NULL',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`hash` varchar(32) NOT NULL DEFAULT \'\'',
		'`type` varchar(8) NOT NULL DEFAULT \'\'',
		'`length` int(11) NOT NULL DEFAULT 0',
		'`atime` int(11) NOT NULL DEFAULT 0',
		'`ctime` int(11) NOT NULL DEFAULT 0',
		'`mtime` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'UNIQUE (`hash`, `type`)',
		'FULLTEXT (`title`,`description`)'
		);

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'name':
					$key = 'title';
					break;
				}
			}
		return parent::get ($key, $opts);
		}

	public static function install ($uninstall = FALSE) {
		if (!is_dir (WP_CRM_SUPPORT_PATH))
			if (!@mkdir (WP_CRM_SUPPORT_PATH))
				throw new WP_CRM_Exception (WP_CRM_Exception::FileSystem_Access_Error);
		parent::install ($uninstall);
		}
	};
?>
