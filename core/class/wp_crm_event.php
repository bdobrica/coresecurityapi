<?php
class WP_CRM_Event extends WP_CRM_Model {
	public static $T = 'events';
	protected static $K = array (
		'eid',			# parent events (can create a chain of events)
		'event',		# event unique identifier, like "timer", camelcase, firstletter small
		'context',		# context description: serialized array of (object => type) pairs
		'filter',		# limit actions based on this filter, array of statements concatenated with and
		'flags'
		);
	public static $F = array (
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`eid` int(11) NOT NULL DEFAULT 0',
		'`event` varchar(32) NOT NULL DEFAULT \'\' UNIQUE',
		'`context` text NOT NULL',
		'`filter` text NOT NULL',
		'`flags` int(11) NOT NULL DEFAULT 0'
		);

	private $do;

	public function __construct ($data = null) {
		global $wpdb;

		$this->do = null;

		if (is_string ($data) && !is_numeric($data)) {
			$data = (int) $wpdb->get_var ($wpdb->prepare ('select id from `' . $wpdb->prefix . self::$T . '` where event=%s', array ($data)));
			if (!$data) throw new WP_CRM_Exception (WP_CRM_Exception::Invalid_ID);
			}

		parent::__construct ($data);
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'actions':
				if (!$this->ID) return null;
				$this->do = new WP_CRM_List ('WP_CRM_Action', array ('eid=' . $this->ID));
				return $this->do;
				break;
			}
		return parent::get ($key, $opts);
		}

	public function fire ($data = null) {
		$out = array ();

		if (!is_object ($this->do)) self::get ('actions');
		if (!(is_null ($this->do) || $this->do->is ('empty')))
			foreach ($this->do->get() as $action)
				try {
					$action->do ($data);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					$out[$action->get ()] = $wp_crm_exception->get ('code');
					}

		if (!empty($out))
			throw new WP_CRM_Exception (WP_CRM_Exception::Event_Misfired);
		}
	}
?>
