<?php
class WP_CRM_Fast_SignOn extends WP_CRM_Model {
	public static $T = 'fastsignon';
	protected static $K = array (
		'uid',
		'hash',
		'expire'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`hash` varchar(64) NOT NULL DEFAULT \'\'',
		'`expire` int(11) NOT NULL DEFAULT 0',
		'KEY (`hash`)'
		);

	public function __construct ($data = null) {
		global $wpdb;
		if (is_numeric ($data)) {
			$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . self::$T . '` where uid=%d and expire>%d limit 1;', array (
				$data,
				time ()
				));
			$data = $wpdb->get_var ($sql);
			}

		parent::__construct ($data);
		}

	public static function init () {
		global $wpdb;

		$salt = mt_rand ();
		$list = new WP_CRM_List ('WP_CRM_User');
		foreach ($list->get () as $user) {
			$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . self::$T . '` (uid,hash,expire) values (%d,%s,%d);', array (
				$user->get (),
				$user->get ('hash'),
				time () + 7 * 86400
				));
			$wpdb->query ($sql);
			}
		}

	public static function signon ($hash) {
		global
			$current_user,
			$wpdb;

		$sql = $wpdb->prepare ('select uid from `' . $wpdb->prefix . self::$T . '` where hash=%s and expire>%d;', array (
			$hash,
			time ()
			));

		$user_id = $wpdb->get_var ($sql);
		if ($user_id) {
			$current_user = new WP_User ((int) $user_id);
			}

		return $current_user;
		}
	}
?>
