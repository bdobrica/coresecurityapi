<?php
/**
 * WP_CRM_Purchase is a class that tells the situation of WP_CRM_Resource
 * Whenever a purchase of a Resource is done, a new object is added.
 */
class WP_CRM_Purchase extends WP_CRM_Model {
	public static $T = 'purchases';

	protected static $K = array (
		'oid',				/** the organization (office) group */
		'cid',				/** the company that is doing the purchase */
		'rid',				/** WP_CRM_Resource id that was purchased */
		'uid',				/** who acquired the WP_CRM_Resource */
		'title',			/** a reference for the purchase */
		'description',			/** a description of the purchase */
		'reference',			/** financial reference of the purchase */
		'quantity',			/** the quantity of WP_CRM_Resource purchased */
		'source',			/** the source account */
		'destination',			/** the destination account */
		'stamp'				/** when the WP_CRM_Resource was acquired */
		);

	public static $F = array (
		'new' => array (
			'rid' => 'Resursa achizitionata',
			'uid' => 'Utilizator',
			'title' => 'Denumire achizitie',
			'description' => 'Descriere achizitie',
			'reference' => 'Referinta contabila',
			'stamp' => 'Data achizitiei',
			'quantity' => 'Cantitatea',
			'source' => 'Cont sursa',
			'destination' => 'Cont destinatie'
			),
		'edit' => array (
			),
		'view' => array (
			'rid' => 'Resursa achizitionata',
			'uid' => 'Utilizator',
			'title' => 'Denumire achizitie',
			'reference' => 'Referinta contabila',
			'stamp' => 'Data achizitiei',
			'quantity' => 'Cantitatea',
			'source' => 'Cont sursa',
			'destination' => 'Cont destinatie'
			)
		);

	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`reference` text NOT NULL',
		'`quantity` float(11,2) NOT NULL DEFAULT 0.00',
		'`source` int NOT NULL DEFAULT 0',
		'`destination` int NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);
	}
