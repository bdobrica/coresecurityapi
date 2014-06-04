<?php
class WP_CRM_Trainer extends WP_CRM_Model {
	public static $T = 'trainers';
	protected static $K = array (
		'first_name',
		'last_name',
		'name',
		'description'
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
		'`first_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`last_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`name` varchar(64) NOT NULL DEFAULT \'\'',
		'`description` TEXT NOT NULL'
		);
	}
?>
