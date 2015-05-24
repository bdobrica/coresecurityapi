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

	public function __construct ($data = null) {
		global $wp_crm_user;

		if (is_object ($data)) {
			$data = array (
				'uid'		=> $wp_crm_user->get(),
				'rid'		=> $data->get (),
				'type'		=> get_class ($data),
				'instance'	=> $data->get ('instance')
				);
			}

		parent::__construct ($data);
		}

	public static function hash ($hash, $inverse = FALSE) {
		$char = '0123456789abcdefghijklmnopqrstuvwxyz';
		$base = strlen ($char);

		$dec = 0;
		$c = 0;

		if ($inverse) {
			$hex = array ();
			while ($c < strlen ($hash)) $dec = bcadd (bcmul ($base, $dec), is_numeric($hash[$c]) ? (int) $hash[$c++] : (ord($hash[$c]) > 96 ? (ord($hash[$c++]) - 87) : (ord($hash[$c++]) - 55)));
			while ($dec > 0) {
				array_unshift ($hex, $char[bcmod ($dec, 16)]);
				$dec = bcdiv ($dec, 16, 0);
				}
			return implode ('', $hex);
			}
		else {
			$unk = array ();
			while ($c < strlen ($hash)) $dec = bcadd (bcmul (16, $dec), hexdec ($hash[$c++]));
			while ($dec > 0) {
				array_unshift ($unk, $char[bcmod ($dec, $base)]);
				$dec = bcdiv ($dec, $base, 0);
				}
			return implode ('', $unk);
			}

		}
	}
?>
