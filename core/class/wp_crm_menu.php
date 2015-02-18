<?php
class WP_CRM_Menu extends WP_CRM_Model {
	const WP_CRM_Menu_List		= 1;
	const WP_CRM_Menu_Dashboard	= 2;

	public static $T = 'menus';
	protected static $K = array (
		'aid'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`aid` int(11) NOT NULL DEFAULT 0',
		'`appslug` varchar(64) NOT NULL DEFAULT \'\'',
		);

	private $apps;
	private $render;

	public function __construct ($data = null, $render = null) {
		global $wpdb;

		$this->render = is_null ($render) ? self::WP_CRM_Menu_Dashboard : (int) $render;

		$this->apps = array ();
		$sql = $wpdb->prepare ('select aid from `' . $wpdb->prefix . self::$T . '` where uid=%d;', (int) $data);
		$apps = $wpdb->get_col ($sql);

		if (empty($apps)) {
			if (current_user_can('add_users')) {
				$sql = 'select id from `' . $wpdb->prefix . WP_CRM_App::$T . '`;';
				$apps = $wpdb->get_col ($sql);
				}
			}

		/**
		 * Add a profile app (if it exists) to all users.
		 */
		$sql = 'select id from `' . $wpdb->prefix . WP_CRM_App::$T . '` where slug=\'user\';';
		$profile = $wpdb->get_var ($sql);
		if ($profile) {
			if (empty ($apps))
				$apps = array ($profile);
			else
				if (!in_array ($profile, $apps))
					$apps = array_merge (array ($profile), $apps);
			}

		if (!empty($apps))
		foreach ($apps as $app) {
			$wp_crm_app = new WP_CRM_App ((int) $app);
			$this->apps[$wp_crm_app->get ('slug')] = $wp_crm_app;
			}
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'render':
				return $this->render;
				break;
			default:
				return $this->apps;
				break;
			}
		}

	public function set ($key = null, $value = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'render':
					$this->render = (int) $value;
					return TRUE;
					break;
				default:
					return parent::set ($key, $value);
					break;
				}
			}
		return parent::set ($key, $value);
		}
	}
?>
