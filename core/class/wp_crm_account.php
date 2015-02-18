<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Objects that contain references to existing accounts. Each account can be (S) Synthetic, (A) Analytical, (R) Commercial, (T) Treasury
 * to keep track of the money. Also, accounts can be (A) active, (P) passive, (B) bi-functional or (U) undefined
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Account extends WP_CRM_Model {
	public static $T = 'accounts';
	protected static $K = array (
		'reference',
		'oid',
		'cid',
		'type',				/** the type of the account SART */
		'func',				/** the function of the account APBU */
		'parent',
		'name',
		'description',
		'account',
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'reference' => 'Referinta',
			'name' => 'Nume cont',
			'description' => 'Detalii',
			'cid' => 'Companie',
			'type' => 'Tip cont',
			'func' => 'Functie cont',
			'account' => 'Sold initial'
			),
		'edit' => array (
			'name' => 'Nume cont',
			'description' => 'Detalii',
			),
		'view' => array (
			'reference' => 'Referinta',
			'name:string' => 'Nume cont',
			'type' => 'Tip cont',
			'func' => 'Functie cont',
			'account' => 'Sold'
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	protected static $Q = array (
		'`id` int NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`reference` varchar(10) NOT NULL DEFAULT \'\'',
		'`oid` int NOT NULL DEFAULT 0',
		'`cid` int NOT NULL DEFAULT 0',
		'`type` enum (\'S\',\'A\',\'R\',\'T\') DEFAULT \'R\'',
		'`func` enum (\'A\',\'P\',\'B\',\'U\') DEFAULT \'U\'',
		'`parent` int NOT NULL DEFAULT 0',
		'`name` text NOT NULL',
		'`description` text NOT NULL',
		'`account` float(11,2) NOT NULL DEFAULT 0.00'
		);
	};
?>
