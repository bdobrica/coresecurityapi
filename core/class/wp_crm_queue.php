<?php
class WP_CRM_Queue extends WP_CRM_Model {
	public static $T = 'queues';
	protected static $K = array (
		'uid',
		'cid',
		'eid',
		'added',
		'fired'
		);

	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`eid` int(11) NOT NULL DEFAULT 0',
		'`added` int(11) NOT NULL DEFAULT 0',
		'`fired` int(11) NOT NULL DEFAULT 0'
		);

	private $time;
	
	public function __construct ($data = null) {
		$time = time ();
		}

	public function push ($event, $stamp = null) {
		global $wpdb;
		$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . self::$T . '` (uid, cid, eid, added, fired) values (%d, %d, %d, %d, %d);', array (
				$user->ID,
				0,
				$event->get (),
				is_null ($stamp) ? $this->time : (int) $stamp,
				0
				));
		$wpdb->query ($sql);
		}

	public function pop () {
		global $wpdb;

		$sql = $wpdb->prepare ('select eid `' . $wpdb->prefix . self::$T . '` where fired=0 and added<%d order by added limit 0,1;', $this->time);
		$event_id = (int) $wpdb->get_var ($sql);

		try {
			$event = new WP_CRM_Event ($event_id);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			}

		$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . self::$T . '` where fired=0 and eid=%d and added<%d;', array (
			$event_id,
			$this->time
			));
		$wpdb->query ($sql);

		return $event;
		}

	public function run () {
		foreach ($this->pop() as $event) {
			try {
				$event->fire ();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				//$this->push ($event);
				}
			}
		}

	public function __destruct () {
		}
	}
?>
