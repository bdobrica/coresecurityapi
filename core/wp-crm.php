<?php
/*
Plugin Name: WP CRM API
Plugin URI: http://www.ublo.ro
Description: WP CRM API
Author: Bogdan Dobrica
Version: 0.1
Author URI: http://www.ublo.ro
*/
ini_set ('display_errors', 1);

spl_autoload_register (function ($class) {
	$class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

class WP_CRM_Plugin_Wrapper {
        private static $version = '0.1';
        private static $update_url = 'http://www.coresecurity.ro/public/plugins/wp-crm/';

	public static function activate () {
		global $current_user;
		$current_user = wp_get_current_user ();

		if (($d = opendir (dirname(__FILE__) . '/class/')) === FALSE) die ();
		while (($n = readdir ($d)) !== FALSE) {
			if (strpos ($n, '.php') === FALSE) continue;
			if (($f = fopen (dirname(__FILE__) . '/class/' . $n, 'r')) === FALSE) continue;
			while ($l = fgets ($f)) {
				if (strpos ($l, 'class') === 0) {
					$class = substr ($l, 6, strpos ($l, ' ', 6) - 6);

					if (class_exists ($class) && method_exists ($class, 'install')) {
						call_user_func (array ($class, 'install'));
						}
					break;
					}
				}
			fclose ($f);
			}
		// create roles - wordpress prevents redefining roles

		/**
		 * wp_crm_admin is ROOT
		 */
		add_role ('wp_crm_admin', 'WP CRM Office Administrator', array (
			'wp_crm_admin'	=> true,
			'wp_crm_pay'	=> true,
			'wp_crm_work'	=> true
			));
		/**
		 * wp_crm_accountant can do financial operations
		 */
		add_role ('wp_crm_accountant', 'WP CRM Office Accountant', array (
			'wp_crm_pay'	=> true,
			'wp_crm_work'	=> true
			));
		/**
		 * wp_crm_user is a simple employee
		 */
		add_role ('wp_crm_user', 'WP CRM Office User', array (
			'wp_crm_work'	=> true
			));
		/**
		 * wp_crm_client is the client (confirmed by different mechanisms)
		 */
		add_role ('wp_crm_client', 'WP CRM Client', array (
			'wp_crm_loyal'	=> true,
			'wp_crm_shop'	=> true
			));
		/**
		 * wp_crm_customer is a potential client
		 */
		add_role ('wp_crm_customer', 'WP CRM Customer', array (
			'wp_crm_shop'	=> true
			));
		/**
		 * wp_crm_sleeper is an unconfirmed user.
		 */
		add_role ('wp_crm_subscriber', 'WP CRM Subscriber', array (
			'wp_crm_wakeup' => true
			));

		/**
		 * should create some admin features:
		 * - an admin office
		 * - an admin company
		 * - an admin email account to send platform emails
		 */
		$admin_office = new WP_CRM_Office (array (
			'name' => 'Administrator\'s Office'
			));
		$admin_office->save ();
		$admin_office->set ('owner', $current_user->ID);

		$admin_company = new WP_CRM_Company (array (
			'oid' => $admin_office->get (),
			'name' => 'Administrator\'s Company',
			'uin' => '0'
			));
		$admin_company->save ();
		$admin_office->add ($admin_company);
		
		$admin_email = new WP_CRM_Mail (array (
			'oid' => $admin_office->get (),
			'cid' => $admin_company->get (),
			'username' => $current_user->user_email,
			'name' => $current_user->display_name
			));
		$admin_email->save ();
		}

	public static function check_update ($transient) {
                if (empty ($transient->checked)) return $transient;

                $request = wp_remote_post (self::$update_url, array ('body' => array ('action' => 'version')));
                if (!is_wp_error ($request) || wp_remote_retrieve_response_code ($request) === 200) $version = $request['body']; else $version = FALSE;

                if (version_compare (self::$version, $version, '<')) {
                        $obj = new stdClass ();
                        $obj->slug = basename (__FILE__);
                        $obj->new_version = $version;
                        $obj->url = self::$update_url;
                        $obj->package = self::$update_url;
                        $transient->response[plugin_basename(__FILE__)] = $obj;
                        }

                return $transient;
                }

	public static function theme ($theme) {
		/**
		 * scan for apps
		 * - needs some fixing because switch_theme is called before the theme is switched
		 *   and ::scan () uses a constant probably undefined
		 */
		/*
		$themes = wp_get_themes ();
		$theme = $themes[$theme];
		WP_CRM_App::scan ($theme['Template Dir']);
		*/
		WP_CRM_App::scan ();
		}

        public static function check_info ($false, $action, $arg) {
                if ($arg->slug !== basename (__FILE__)) return FALSE;

                $request = wp_remote_post (self::$update_url, array ('body' => array ('action' => 'info')));
                if (!is_wp_error ($request) || wp_remote_retrieve_response_code ($request) === 200) return unserialize($request['body']);
                return FALSE;
                }

        public static function update () {
                $plugin_slug = plugin_basename (__FILE__);
                $slug = basename (__FILE__);

                add_filter ('pre_set_site_transient_update_plugins', array ('WP_CRM_Plugin_Wrapper', 'check_update'));
                add_filter ('plugins_api', array ('WP_CRM_Plugin_Wrapper', 'check_info'), 10, 3);
                }
	}


register_activation_hook (__FILE__, array ('WP_CRM_Plugin_Wrapper', 'activate'));
add_action ('init', array ('WP_CRM_Plugin_Wrapper', 'update'));
add_action ('switch_theme', array ('WP_CRM_Plugin_Wrapper', 'theme'));
?>
