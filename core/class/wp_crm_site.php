<?php
class WP_CRM_Site extends WP_CRM_Model {
	const Before	= 0;
	const After	= 1;
	const Mail	= 2;


	public static $T = 'sites';
	protected static $K = array (
		'domain',
		'aliases',
		'step1u',
		'step1d',
		'step2u',
		'step2d',
		'step3',
		'register',
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`domain` varchar(64) NOT NULL DEFAULT \'\' UNIQUE',
		'`aliases` text NOT NULL',
		'`step1u` text NOT NULL', 
		'`step1d` text NOT NULL', 
		'`step2u` text NOT NULL', 
		'`step2d` text NOT NULL', 
		'`step3` text NOT NULL', 
		'`register` text NOT NULL', 
		'fulltext (domain,aliases)'
		);
	public static $F = array (
		'new' => array (
			'domain' => 'Domeniu',
			'aliases' => 'Alias',
			'cart:shopcart' => 'Cos de Cumparaturi'
			),
		'view' => array (
			'domain' => 'Domeniu',
			'aliases' => 'Alias',
			'register:templates' => 'Inscriere'
			),
		);

	public static $S = array (
		'cart' => array (
			WP_CRM_State::AddToCart => array (
				'title' => 'Adauga in cos',
				'sections' => array (
					'step1u' => 'Adauga in cos (sus)',
					'step1d' => 'Adauga in cos (jos)'
					)
				),
			WP_CRM_State::Participants => array (
				'title' => 'Detalii participanti',
				'sections' => array (
					'step2u' => 'Detalii participanti (sus)',
					'step2d' => 'Detalii participanti (jos)'
					),
				),
			WP_CRM_State::Payment => array (
				'title' => 'Finalizare inscriere',
				'sections' => array (
					'step3' => 'Finalizare inscriere'
					)
				)
			)
		);

	public function __construct ($data = null) {
		global $wpdb;
		$this->register = array ();

		if (!is_numeric($data) && (($url = parse_url ($data)) !== FALSE)) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where match(domain,aliases) against (%s) limit 0,1;', str_replace ('www.', '', $url['host']));
			$data = $wpdb->get_row ($sql, ARRAY_A);
			}
		parent::__construct ($data);
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'cart':
				return array ();
				break;
			case 'templates':
			case 'register':
				return unserialize($this->data['register']);
				break;
			}
		return parent::get ($key, $opts);
		}
	}
?>
