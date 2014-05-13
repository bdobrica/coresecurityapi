<?php
class WP_CRM_Room_Block extends WP_CRM_Model {
	public static $T = 'room_blocks';
	protected static $K = array (
		'name',
		'description',
		'color',
		'rid',
		'priority',
		'rows',
		'cols',
		'top',
		'left',
		'numbering'
		);
	protected static $F = array (
		'new' => array (
			),
		'view' => array (
			),
		'edit' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`name` varchar(64) NOT NULL DEFAULT \'\'',
		'`description` TEXT NOT NULL',
		'`color` varchar(6) NOT NULL DEFAULT \'FFFFFF\'',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`priority` int(11) NOT NULL DEFAULT 0',
		'`rows` int(11) NOT NULL DEFAULT 0',
		'`cols` int(11) NOT NULL DEFAULT 0',
		'`top` int(11) NOT NULL DEFAULT 0',
		'`left` int(11) NOT NULL DEFAULT 0',
		'`numbering` TEXT NOT NULL'
		);
	}
?>
