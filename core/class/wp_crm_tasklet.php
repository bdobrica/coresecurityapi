<?php
class WP_CRM_Tasklet extends WP_CRM_Model {
	public static $T = 'tasklets';
	protected static $K = array (
		'tid',
		'uid',
		'begin',
		'end',
		'details',
		'ip'
		);
	public static $F = array (
		'new' => array (
			),
		'view' => array (
			),
		'edit' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`tid` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`begin` int(11) NOT NULL DEFAULT 0',
		'`end` int(11) NOT NULL DEFAULT 0',
		'`details` text NOT NULL',
		'`ip` varchar(15) NOT NULL DEFAULT \'127.0.0.1\''
		);
	}
