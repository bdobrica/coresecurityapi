<?php
class WP_CRM_Cookie {
	const Referer		= 'WP_CRM_REFERER';
	#TTL
	const TTL_Session	= 0;
	const TTL_Day		= 86400;
	const TTL_Week		= 604800;
	const TTL_Month		= 2419200;

	const IfEmpty		= 1;

	private $name;
	private $data;

	public function __construct ($name = '', $data = null) {
		$this->name = $name;

		if (is_null ($data))
			$data = unserialize ($_COOKIE[$this->name]) !== FALSE ? unserialize ($_COOKIE[$this->name]) : $_COOKIE[$this->name];
		else
			setcookie ($this->name, (is_array ($data) || is_object ($data)) ? serialize ($data) : $data, time() + self::TTL_Week, '/', '.' . str_replace ('www.', '', $_SERVER['HTTP_HOST']));
		$this->data = $data ? $data : null;
		}

	public function get () {
		return $this->data;
		}

	public function set ($data, $opts = null) {
		switch ((int) $opts) {
			case self::IfEmpty:
				if (!is_null ($this->data)) return FALSE;
				break;
			}

		$this->data = $data;
		setcookie ($this->name, (is_array ($data) || is_object ($data)) ? serialize ($data) : $data, time() + self::TTL_Week, '/', '.' . str_replace ('www.', '', $_SERVER['HTTP_HOST']));
		return TRUE;
		}

	public function __destruct () {
		}
	}
?>
