<?php
class WP_CRM_Stock extends WP_CRM_Model {
	public static $T = 'stocks';
	protected static $K = array (
		'series',
		'number',
		'color',
		'url',
		'title',
		'pid',
		'stamp',
		'struct',
		'hours',
		'theory',
		'corno',
		'ancauth',
		'ancname',
		'rnffpa',
		'ancrep',
		'competences',
		'studies',
		'uid',
		'lid',
		'tid',
		'cid',
		'state',
		'flags'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`series` varchar(6) NOT NULL DEFAULT \'\'',
 		'`number` int(11) NOT NULL DEFAULT 0',
		'`color` varchar(6) NOT NULL DEFAULT \'FFFFFF\'',
		'`url` text NOT NULL',
		'`title` mediumtext NOT NULL',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0.00',
		'`struct` int(11) NOT NULL DEFAULT 0',
		'`hours` int(11) NOT NULL DEFAULT 0',
		'`theory` int(11) NOT NULL DEFAULT 0',
		'`corno` int(11) NOT NULL DEFAULT 0',
		'`ancauth` text NOT NULL',
		'`ancname` mediumtext NOT NULL',
		'`rnffpa` varchar(20) NOT NULL DEFAULT \'\'',
		'`ancrep` mediumtext NOT NULL',
		'`competences` mediumtext NOT NULL',
		'`studies` text NOT NULL',
		'`uid` int(11) NOT NULL DEFAULT 0',			# user id
		'`lid` int(11) NOT NULL DEFAULT 0',			# location id
		'`tid` int(11) NOT NULL DEFAULT 0',			# trainer id
		'`cid` int(11) NOT NULL DEFAULT 0',			#
		'`state` int(1) NOT NULL DEFAULT 0',
		'`flags` int(11) NOT NULL DEFAULT 0',
		'UNIQUE KEY `series` (`series`,`number`)'
		);
	public static $F = array (
		'new' => array (
			'code:code' => 'Cod',
			'title' => 'Produs',
			'cid:company' => 'Companie'
			),
		'view' => array (
			'code' => 'Cod',
			'title' => 'Produs',
			'cid:company' => 'Companie'
			),
		'public' => array (
			'code' => 'Cod',
			'title' => 'Produs',
			'cid:company' => 'Companie'
			),
		'extended' => array (
			),
		'private' => array (
			)
		);
	
	}
?>
