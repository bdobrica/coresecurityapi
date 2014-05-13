<?php
class WP_CRM_Order extends WP_CRM_Model {
	const Cash	=  1;
	const Card	=  2;
	const Bank	=  4;
	const Receipt	=  8;
	const Treasury	= 16;
	const Mobile	= 32;


	public static $T = 'orders';
	public static $K = array (
		'uid',
		'cid',
		'type',
		'description',
		'paidby',
		'paidvalue',
		'paiddate',
		'paiddetails',
		'payload',
		'flags',
		'stamp'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`type` int(11) NOT NULL DEFAULT 0',
		'`description` text NOT NULL',
		'`paidby` text NOT NULL',
		'`paidvalue` float(7,2) NOT NULL DEFAULT 0.00',
		'`paiddate` int(11) NOT NULL DEFAULT 0',
		'`paiddetails` text NOT NULL',
		'`payload` text NOT NULL',
		'`flags` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL'
		);

	public static $F = array (
		'view' => array (
			'type:array;types' => 'Tip',
			'amount' => 'Valoare',
			'description' => 'Detalii',
			'stamp:date' => 'Data'
			),
		'new' => array (
			'type:array;types' => 'Tip',
			'amount' => 'Valoare',
			'description' => 'Detalii',
			'stamp:date' => 'Data'
			),
		'edit' => array (
			'type:array;types' => 'Tip',
			'amount' => 'Valoare',
			'description' => 'Detalii',
			'stamp:date' => 'Data'
			),
		);


	private static $types = array (
		self::Cash	=> 'Numerar',
		self::Card	=> 'Card Bancar',
		self::Bank	=> 'Transfer Bancar',
		self::Receipt	=> 'Chitanta',
		self::Treasury	=> 'Trezorerie',
		self::Mobile	=> 'Telefon Mobil'
		);

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'types':
				return self::$types;
				break;
			default:
				return parent::get ($key, $opts);
			}
		}
	}
?>
