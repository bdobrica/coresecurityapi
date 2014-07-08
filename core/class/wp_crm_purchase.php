<?php
/**
 * WP_CRM_Purchase is a class that tells the situation of WP_CRM_Resource
 * Whenever a purchase of a Resource is done, a new object is added.
 */
class WP_CRM_Purchase extends WP_CRM_Model {
	public static $T = 'purchases';

	protected static $K = array (
		'oid',				// the organization (office) group
		'cid',				// the company that is doing the purchase
		'rid',				// WP_CRM_Resource id that was purchased
		'uid',				// who acquired the WP_CRM_Resource
		'title',			// 
		'description',			//
		'reference',			// financial reference of the purchase
		'quantity',			// the quantity of WP_CRM_Resource purchased
		'value',			// value
		'vat',				// if any vat
		'stamp'				// when the WP_CRM_Resource was acquired
		);

	public static $F = array (
		'new' => array (
			'reference' => 'Referinta contabila',
			'quantity' => 'Cantitate',
			'value' => 'Valoare totala',
			'vat' => 'TVA (%)',
			'rid:hidden' => '',
			),
		'edit' => array (
			'reference' => 'Referinta contabila',
			'quantity' => 'Cantitate',
			'value' => 'Valoare totala',
			'vat' => 'TVA (%)',
			),
		'view' => array (
			'reference' => 'Referinta contabila',
			'quantity' => 'Cantitate',
			'value' => 'Valoare totala',
			'vat' => 'TVA (%)',
			'rid:hidden' => '',
			)
		);

	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`reference` text NOT NULL',
		'`quantity` float(11,2) NOT NULL DEFAULT 0.00',
		'`value` float(11,2) NOT NULL DEFAULT 0.00',
		'`vat` float(4,2) NOT NULL DEFAULT 0.00',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);
	}
