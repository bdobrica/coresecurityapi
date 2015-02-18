<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Currency Class. Connects to BNR official exchange rates and saves then locally.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Currency extends WP_CRM_Model {
	const Source = 'http://www.bnr.ro/nbrfxrates10days.xml';
	const Reference = 'RON';

	private static $Currency = array (
		'AED' => array ( 'Dirhamul Emiratelor Arabe' ),
		'AUD' => array ( 'Dolarul Australian' ),
		'BGN' => array ( 'Leva Bulgărească' ),
		'BRL' => array ( 'Realul Brazilian' ),
		'CAD' => array ( 'Dolarul Canadian' ),
		'CHF' => array ( 'Francul Elveţian' ),
		'CNY' => array ( 'Renminbi-ul Chinezesc' ),
		'CZK' => array ( 'Coroana Cehă' ),
		'DKK' => array ( 'Coroana Daneză' ),
		'EGP' => array ( 'Lira Egipteană' ),
		'EUR' => array ( 'Euro' ),
		'GBP' => array ( 'Lira Sterlină' ),
		'HUF' => array ( '100 Forinţi Maghiari' ),
		'INR' => array ( 'Rupia Indiană' ),
		'JPY' => array ( '100 Yeni Japonezi' ),
		'KRW' => array ( '100 Woni Sud-Coreeni' ),
		'MDL' => array ( 'Leul Moldovenesc' ),
		'MXN' => array ( 'Peso-ul Mexican' ),
		'NOK' => array ( 'Coroana Norvegiană' ),
		'NZD' => array ( 'Dolarul Neo-Zeelandez' ),
		'PLN' => array ( 'Zlotul Polonez' ),
		'RSD' => array ( 'Dinarul Sârbesc' ),
		'RUB' => array ( 'Rubla Rusească' ),
		'SEK' => array ( 'Coroana Suedeză' ),
		'TRY' => array ( 'Lira Turcească' ),
		'UAH' => array ( 'Hryvna Ucraineană' ),
		'USD' => array ( 'Dolarul American' ),
		'XAU' => array ( 'Gramul De Aur' ),
		'XDR' => array ( 'DST' ),
		'ZAR' => array ( 'Randul Sud-African' ),
		);

	public static $T = 'currency';
	protected static $K = array (
		'currency',
		'multiplier',
		'rate',
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		'currency',
		'stamp'
		);

	public static $F = array (
		'new' => array (
			),
		'edit' => array (
			),
		'view' => array (
			'currency' => 'Moneda',
			'rate' => 'Rata de schimb BNR',
			'stamp:date' => 'Data'
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
		'`currency` varchar(3) NOT NULL DEFAULT 0',
		'`multiplier` int(3) NOT NULL DEFAULT 0',
		'`rate` float(7,4) NOT NULL DEFAULT 0.0000',
		'`stamp` int NOT NULL DEFAULT 0',
		'UNIQUE (`currency`,`stamp`)'
		);

	public static function scan () {
		$curl = curl_init (self::Source);
		$opts = array (
			CURLOPT_CUSTOMREQUEST	=> 'GET',
			CURLOPT_POST		=> false,
			CURLOPT_USERAGENT	=> 'WP_CRM/1.0 (Linux)',
			CURLOPT_HEADER		=> false,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_CONNECTTIMEOUT	=> 30,
			CURLOPT_TIMEOUT		=> 30,
			CURLOPT_MAXREDIRS	=> 2
			);

		curl_setopt_array ($curl, $opts);
		$data = curl_exec ($curl);

		$cube = 0;
		while ($cube = strpos ($data, '<Cube', $cube + 1)) {
			$cube_end = strpos ($data, '</Cube>', $cube);
			$cube_date = strtotime (substr ($data, $cube + 12, 10));
			$cube_data = substr ($data, $cube, $cube_end - $cube);
			
			$rate = 0;
			while ($rate = strpos ($cube_data, '<Rate', $rate + 1)) {
				$rate_end = strpos ($cube_data, '</Rate>', $rate);
				$value = strpos ($cube_data, '>', $rate + 1) + 1;
				$rate_value = (float) substr ($cube_data, $value, $rate_end - $value);
				$rate_name = substr ($cube_data, $rate + 16, 3);

				$rate_multiplier = strpos ($cube_data, 'multiplier="', $rate);
				if (($rate_multiplier !== FALSE) && ($rate_multiplier < $rate_end)) {
					$rate_multiplier += 12;
					$rate_multiplier = (int) substr ($cube_data, $rate_multiplier, strpos ($cube_data, '"', $rate_multiplier) - $rate_multiplier);
					}
				else
					$rate_multiplier = 1;

				try {
					$currency = new WP_CRM_Currency (array (
						'currency' => $rate_name,
						'multiplier' => $rate_multiplier,
						'rate' => $rate_value,
						'stamp' => $cube_date
						));
					$currency->save ();
					unset ($currency);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					}
				}
			}
		
		}

	public function get ($key = null, $opts = null) {
		if (is_string ($key))
			switch ($key) {
				case 'currency':
					return self::$Currency[$this->data['currency']][0];
					break;
				case 'unitrate':
					return $this->data['rate'] / $this->data['multiplier'];
					break;
				}
		return parent::get ($key, $opts);
		}
	};
?>
