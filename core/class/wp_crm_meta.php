<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Meta Object, keeping track of additional keys for meta tables
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Meta extends WP_CRM_Model {
	private static $TYPES = array (
		'text' => 'Sir de caractere',
		'textarea' => 'Text multilinie',
		'file' => 'Fisier'
		);

	public static $T = 'meta';
	protected static $K = array (
		'oid',
		'uid',
		'object',
		'meta_key',
		'meta_type',
		'meta_description',
		'meta_filter'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		'oid',
		'object',
		'meta_key'
		);
	public static $F = array (
		'new' => array (
			'object:array;object_list' => 'Obiect',
			'meta_key' => 'Camp',
			'meta_type:array;type_list' => 'Tip',
			'meta_description:textarea' => 'Descriere'
			),
		'edit' => array (
			'object:array;object_list' => 'Obiect',
			'meta_key' => 'Camp',
			'meta_type:array;type_list' => 'Tip',
			'meta_description:textarea' => 'Descriere'
			),
		'view' => array (
			'meta_key' => 'Camp',
			'meta_type:array;type_list' => 'Tip',
			'meta_description' => 'Descriere',
			'object:array;object_list' => 'Obiect'
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
		'`oid` varchar(13) NOT NULL DEFAULT \'\'',
		'`uid` varchar(13) NOT NULL DEFAULT \'\'',
		'`object` varchar(64) NOT NULL DEFAULT \'\'',
		'`meta_key` varchar(64) NOT NULL DEFAULT \'\'',
		'`meta_type` varchar(64) NOT NULL DEFAULT \'\'',
		'`meta_description` text NOT NULL',
		'`meta_filter` text NOT NULL'	
		);

	public function get ($key = null, $opts = null) {
		if (is_string ($key))
		switch ($key) {
			case 'object_list':
				$out = array ();
				if ($d = opendir (dirname (__FILE__))) {
					while (($f = readdir ($d)) !== FALSE) {
						if (($h = fopen (dirname (__FILE__) . DIRECTORY_SEPARATOR . $f, 'r')) !== FALSE) {
							while (($l = fgets ($h)) !== FALSE) {
								if (($a = strpos ($l, 'class WP_CRM_')) === 0) {
									if (($b = strpos ($l, ' extends WP_CRM_Model')) !== FALSE) {
										$out[] = substr ($l, $a + 6, $b - $a - 6);
										}
									break;
									}
								}
							fclose ($h);
							}
						}
					closedir ($d);
					}
				return $out;
				break;
			case 'type_list':
				return self::$TYPES;
				break;
			}
		return parent::get ($key, $opts);
		}
	};
?>
