<?php
class WP_CRM_Settings {
	protected static $K = array (
		'website' => array (
			'label' => 'Site Web',
			'type' => 'string',
			'default' => 'http://www.einvest.ro'
			),
		'language' => array (
			'label' => 'Limba',
			'type' => 'string',
			'default' => 'Romana'
			),
		'mail_server' => array (
			'label' => 'Adresa server de mail',
			'type' => 'string',
			'default' => 'smtp.gmail.com'
			),
		'mail_server_port' => array (
			'label' => 'Portul serverului de mail',
			'type' => 'string',
			'default' => '465'
			),
		'mail_server_security' => array (
			'label' => 'Securitatea serverului de mail',
			'type' => 'string',
			'default' => 'SSL'
			),
		);

	public function __construct () {
		}

	public function get ($key = null, $opts = null) {
		if (is_string ($key))
		switch ($key) {
			case 'fields':
				return self::$K;
				break;
			}
		}

	public function set ($key = null, $value = null) {
		}

	public function __destruct () {
		}
	}
?>
