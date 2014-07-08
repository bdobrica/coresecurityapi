<?php
class WP_CRM_Task extends WP_CRM_Model {
	public static $T = 'tasks';
	protected static $K = array (
		'oid',					// office id
		'cid',					// company id
		'pid',					// process id
		'parent',				// previous task id; if 0, this is the first task
		'uid',					// the user that generated this task
		'rid',					// responsible id
		'title',
		'description',
		'factor',
		'importance',
		'urgency',
		'deadline'
		);
	public static $F = array (
		'new' => array (
			'title' => 'Denumire',
			'rid' => 'Responsabil',
			'description:textarea' => 'Descriere',
			'importance' => 'Importanta',
			'urgency' => 'Urgenta',
			'deadline:date' => 'Termen Limita'
			),
		'view' => array (
			'title' => 'Denumire',
			'rid' => 'Responsabil',
			'description:textarea' => 'Descriere',
			'importance' => 'Importanta',
			'urgency' => 'Urgenta',
			'deadline:date' => 'Termen Limita'
			),
		'edit' => array (
			'title' => 'Denumire',
			'rid' => 'Responsabil',
			'description:textarea' => 'Descriere',
			'importance' => 'Importanta',
			'urgency' => 'Urgenta',
			'deadline:date' => 'Termen Limita'
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`factor` float(9,2) NOT NULL DEFAULT 0.00',
		'`importance` int(2) NOT NULL DEFAULT 0',
		'`urgency` int(2) NOT NULL DEFAULT 0',
		'`deadline` int(11) NOT NULL DEFAULT 0'
		);
	}
?>
