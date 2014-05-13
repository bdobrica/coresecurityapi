<?php
class WP_CRM_Task extends WP_CRM_Model {
	public static $T = 'tasks';
	protected static $K = array (
		'uid',
		'rid',
		'title',
		'description',
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
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`importance` int(2) NOT NULL DEFAULT 0',
		'`urgency` int(2) NOT NULL DEFAULT 0',
		'`deadline` int(11) NOT NULL DEFAULT 0'
		);
	}
?>
