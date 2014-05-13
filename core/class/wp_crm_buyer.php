<?php
/**
 * Buyer is a wrapper class for Invoice's buyers. It wraps around Person and Company classes,
 * which share common methods and provides a unique interface.
 */
class WP_CRM_Buyer extends WP_CRM_Model {
	const Cookie_TTL	= 31536000;
	const TLD		= 'biletedesucces.ro';

	public static $T = 'buyers';
	protected static $K = array (
		'type',
		'eid',
		'stamp'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`type` enum(\'person\',\'company\') COLLATE utf8_unicode_ci NOT NULL DEFAULT \'person\'',
		'`eid` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	protected $entity;

	public function __construct ($data = null) {
		if (is_null($data) && isset($_COOKIE[__CLASS__]) &&  is_numeric($_COOKIE[__CLASS__]))
			$data = (int) $_COOKIE[__CLASS__];
		
		parent::__construct ($data);
		if (!$this->get ()) {
			$this->set ('type', 'person');
			$this->set ('stamp', time());
			$this->save ();
			}

		setcookie (__CLASS__, $this->get (), time() + self::Cookie_TTL, '/', '.' . self::TLD);
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'entity':
				return $this->get ('type') == 'person' ?
					new WP_CRM_Person ($this->get ('eid')) :
					new WP_CRM_Company ($this->get ('eid'));
				break;
			}
		return parent::get ($key, $opts);
		}

	public function __destruct () {
		}
	};
?>
