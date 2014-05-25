<?php
/**
 * WP_CRM_Office is a group of companies that share
 * owner and employees.
 * @see WP_CRM_Company
 */
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
			'type' => 'Tip',
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
	/**
	 * Mapping roles to maximum companies held in one office
	 * @var array
	 */
	private static $ALLOWED_COMPANIES = array (
		'wp_crm_admin'		=> -1,
		'wp_crm_acountant'	=> 0,
		);
	/**
	 * Mapping roles to maximum offices held by one user
	 * The user offices' array is stored in _wp_crm_offices
	 * meta property.
	 * @var array
	 */
	public static $ALLOWED_OFFICES = array (
		'wp_crm_admin'		=> -1,
		'wp_crm_acountant'	=> 0,
		);
	}
?>
