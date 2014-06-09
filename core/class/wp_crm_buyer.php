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
		global $current_user;

		if (is_null($data)) {
			$current_user = wp_get_current_user ();
			if ($current_user->ID) {
				try {
					$this->entity = new WP_CRM_Person ($current_user->user_email);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					throw new WP_CRM_Exception (WP_CRM_Exception::Missing_Person);
					}

				$this->set ('type', 'person');
				$this->set ('eid', $this->entity->get ());
				$this->set ('stamp', time());
				$this->ID = -$current_user->ID;
				}
			else
			if (isset ($_COOKIE[__CLASS__]) && is_numeric($_COOKIE[__CLASS__])) {
				$data = (int) $_COOKIE[__CLASS__];
				parent::__construct ($data);
				}
			}

		if (!$current_user->ID) {
			if (!$this->get ()) {
				$this->set ('type', 'person');
				$this->set ('stamp', time());
				$this->save ();
				}

			setcookie (__CLASS__, $this->get (), time() + self::Cookie_TTL, '/', '.' . self::TLD);
			}
		}

	public function get ($key = null, $opts = null) {
		if (in_array ((string) $key, self::$K))
			return parent::get ($key, $opts);

		if (!is_object ($this->entity)) {
			$this->entity = $this->get ('type') == 'person' ?
				new WP_CRM_Person ($this->get ('eid')) :
				new WP_CRM_Company ($this->get ('eid'));
			}
		switch ((string) $key) {
			case 'entity':
				return is_object ($this->entity) ? $this->entity : null;
				break;
			}
		return $this->entity->get ($key, $opts);
		}

	public function set ($key = null, $value = null) {
		if (is_string ($key)) {
			if (in_array ($key, self::$K))
				return parent::set ($key, $value);
			else {
				if (!is_object ($this->entity)) {
					$this->entity = $this->get ('type') == 'person' ?
						new WP_CRM_Person ($this->get ('eid')) :
						new WP_CRM_Company ($this->get ('eid'));
					}
				return $this->entity->set ($key, $value);
				}
			}

		if (is_array ($key)) {
			$keys = array_keys ($key);
			$local_keys = array_intersect ($keys, self::$K);
			if (!empty ($local_keys)) {
				$local = array ();
				foreach ($local_keys as $local_key)
					$local[$local_key] = $key[$local_key];
				parent::set ($local);
				}

			$entity = array ();
			if (!empty ($keys))
				foreach ($keys as $local_key)
					if (strpos ($local_key, 'buyer_') === 0)
						$entity[substr($local_key, 6)] = $key[$local_key];

			if (!empty ($entity)) {
				if (!is_object ($this->entity)) {
					$this->entity = $this->get ('type') == 'person' ?
						new WP_CRM_Person ($this->get ('eid')) :
						new WP_CRM_Company ($this->get ('eid'));
					}
				$this->entity->set ($entity);
				}
			}
		}

	public function field ($key, $context = 'edit') {
		if (!is_object ($this->entity)) {
			$this->entity = $this->get ('type') == 'person' ?
				new WP_CRM_Person ($this->get ('eid')) :
				new WP_CRM_Company ($this->get ('eid'));
			}
		return $this->entity->field ($key, $context);
		}

	public function __destruct () {
		}
	};
?>
