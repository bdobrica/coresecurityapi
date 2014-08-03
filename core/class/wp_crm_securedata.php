<?php
/**
 * Core of WP_CRM_Secure*
 */

/**
 * Class for keeping track of data chunks. A file can be splitted over servers.
 *
 * @category
 * @package WP_CRM_Secure
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_SecureData extends WP_CRM_Model {
	public static $T = 'securedata';
	protected static $K = array (
		'oid',			# owner id
		'pid',			# parent ID
		'seek',			# position in file
		'length',		# chunk length
		'hash',			# file hash (not piece hash)
		'stamp',		# modified time
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
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`seek` int(11) NOT NULL DEFAULT 0',
		'`length` int(11) NOT NULL DEFAULT 0',
		'`hash` char(128) NOT NULL DEFAULT \'\'',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'KEY (`pid`)',
		'UNIQUE(`pid`,`seek`,`hash`)',
		);

	public function __construct ($data = null) {
		parent::__construct ($data);
		}	
	}
?>
