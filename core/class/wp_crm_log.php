<?php
class WP_CRM_Log extends WP_CRM_Model {
	public static $T = 'logs';
	protected static $K = array (
		'get',
		'post',
		'server',
		'session',
		'cookie',
		'stamp'
		);
	public static $F = array (
		'new' => array (
			),
		'view' => array (
			'get:vars' => 'GET',
			'post:vars' => 'POST',
			'server:vars' => 'SERVER',
			'session:vars' => 'SESSION',
			'cookie:vars' => 'COOKIE'
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
		'`get` text NOT NULL',
		'`post` text NOT NULL',
		'`server` text NOT NULL',
		'`session` text NOT NULL',
		'`cookie` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	public function __construct ($data = null) {
		if (is_null ($data)) {
			$data = array (
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
