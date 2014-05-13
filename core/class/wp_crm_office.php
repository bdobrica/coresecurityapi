<?php
class WP_CRM_Office extends WP_CRM_Model {
	public static $T = 'offices';
	public static $K = array (
		'name',
		'description',
		'url',
		'companies'
		);
	public static $F = array (
		'new' => array (
			'name' => 'Denumire',
			'description:textarea' => 'Descriere',
			'url' => 'Link',
			#'companies' => 'Companii Membre'
			),
		'view' => array (
			'name' => 'Denumire',
			'description' => 'Descriere',
			'url' => 'Link',
			#'companies' => 'Companii Membre'
			),
		'edit' => array (
			'name' => 'Denumire',
			'description:textarea' => 'Descriere',
			'url' => 'Link',
			#'companies' => 'Companii Membre'
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`name` varchar(64) NOT NULL DEFAULT \'\' UNIQUE',
		'`description` text NOT NULL',
		'`url` text NOT NULL',
		'`companies` text NOT NULL'
		);
	}
?>
