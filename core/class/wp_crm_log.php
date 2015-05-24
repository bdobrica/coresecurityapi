<?php
class WP_CRM_Log extends WP_CRM_Model {
	public static $T = 'logs';
	protected static $K = array (
		'uid',
		'action',
		'object',
		'details',
		'get',
		'post',
		'server',
		'session',
		'cookie',
		'stamp'
		);
	public static $F = array (
		'new' => array (
			'uid:user'		=> 'User',
			'action'		=> 'Action',
			'object'		=> 'Object',
			'details'		=> 'Details',
			'stamp:datetime'	=> 'Date',
			),
		'edit' => array (
			'uid:user'		=> 'User',
			'action'		=> 'Action',
			'object'		=> 'Object',
			'details'		=> 'Details',
			'stamp:datetime'	=> 'Date',
			),
		'view' => array (
			'uid:user'		=> 'User',
			'action'		=> 'Action',
			'object'		=> 'Object',
			'details'		=> 'Details',
			'stamp:datetime'	=> 'Date',
			),
		'public' => array (
			),
		'extended' => array (
			),
		'private' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`action` varchar(64) NOT NULL DEFAULT \'\'',
		'`object` TEXT NOT NULL',
		'`details` TEXT NOT NULL',
		'`get` text NOT NULL',
		'`post` text NOT NULL',
		'`server` text NOT NULL',
		'`session` text NOT NULL',
		'`cookie` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	public function __construct ($data = null) {
		global $current_user;

		if (is_null ($data)) {
			$data = array (
				'uid' => is_object ($current_user) ? $current_user->ID : 0,
				'action' => 'debug',
				'object' => 'NULL',
				'details' => '',
				'get' => serialize ($_GET),
				'post' => serialize ($_POST),
				'server' => serialize ($_SERVER),
				'session' => serialize ($_SESSION),
				'cookie' => serialize ($_COOKIE),
				'stamp' => time ()
				);
			}
		elseif (is_array ($data)) {
			$data = array (
				'uid' => $data['uid'] ? : (is_object ($current_user->ID) ? $current_user->ID : 0),
				'action' => $data['action'] ? : 'debug',
				'object' => is_string ($data['object']) ? $data['object'] : (is_object ($data['object']) ? $data['object']->get ('self') : null),
				'details' => $data['details'] ? : '',
				'get' => serialize ($_GET),
				'post' => serialize ($_POST),
				'server' => serialize ($_SERVER),
				'session' => serialize ($_SESSION),
				'cookie' => serialize ($_COOKIE),
				'stamp' => time ()
				);
			}

		parent::__construct ($data);
		}
	}
?>
