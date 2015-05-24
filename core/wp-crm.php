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
		global $wp_crm_user;

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

		$wp_crm_user = new WP_CRM_User (FALSE);
		/**
		 * should create some admin features:
		 * - an admin office
		 * - an admin company
		 * - an admin email account to send platform emails
		 */
		$first_office = null;
		$wp_crm_offices = $wp_crm_user->get ('offices');
		if (empty ($wp_crm_offices)) {
			$first_office = new WP_CRM_Office (array (
				'uid' =>	$wp_crm_user->get (),
				'name' =>	$wp_crm_user->get ('display_name') . '\'s Office'
				));
			try {
				$first_office->save ();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				unset ($first_office);
				$first_office = NULL;
				}
			if (!is_null ($first_office)) {
				$wp_crm_user->set ('offices', array ($first_office->get ()));
				$first_office->set ('owner', $wp_crm_user->get ());
				$wp_crm_user->set ('settings', array (
					'default_office' =>	$first_office->get ()
					));
				}
			}
		if (is_null ($first_office))
			$first_office = new WP_CRM_Office ($wp_crm_user->get ('settings', 'default_office'));

		$wp_crm_companies = $wp_crm_user->get ('companies');
		if (empty ($wp_crm_companies)) {
			$first_company = new WP_CRM_Company (array (
				'uid' =>	$wp_crm_user->get (),
				'oid' =>	$wp_crm_user->get ('settings', 'default_office'),
				'name' =>	$wp_crm_user->get ('display_name') . '\'s Company'
				));
			try {
				$first_company->save ();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				unset ($first_company);
				$first_company = NULL;
				}
			if (!is_null ($first_company)) {
				$wp_crm_user->set ('companies', array ($first_company->get ()));
				if (is_object ($first_office)) $first_office->add ($first_company);
				$wp_crm_user->set ('settings', array (
					'default_company' =>	$first_office->get ()
					));
				}
			}

		if ($wp_crm_user->get ('user_email')) {
			$admin_email = new WP_CRM_Mail (array (
				'oid' =>	$wp_crm_user->get ('settings', 'default_office'),
				'cid' =>	$wp_crm_user->get ('settings', 'default_company'),
				'username' =>	$wp_crm_user->get ('user_email'),
				'name' =>	$wp_crm_user->get ('display_name')
				));
			try {
				$admin_email->save ();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				}
			}
		/**
		 * create a default folder:
		 */
		$folders = array ();

		$root_folder = new WP_CRM_Folder (array (
			'title' => 'Root',
			'mime' => 'directory',
			'description' => 'Default Root Folder'
			));
		try {
			$root_folder->save ();
			$folders['root_folder'] = $root_folder->get ();
			}
		catch (WP_CRM_Exception $exception) {
			$root_folder = null;
			}

		if (!is_null ($root_folder)) {
			$scan_folder = new WP_CRM_Folder (array (
				'title' => 'Scan',
				'mime' => 'directory',
				'description' => 'Default Scanner Folder',
				'parent' => $root_folder->get ()
				));
			try {
				$scan_folder->save ();
				$folders['scan_folder'] = $scan_folder->get ();
				}
			catch (WP_CRM_Exception $exception) {
				$scan_folder = null;
				}

			$companies_folder = new WP_CRM_Folder (array (
				'title' => 'Companies',
				'mime' => 'directory',
				'description' => 'Default Companies Folder',
				'parent' => $root_folder->get ()
				));
			try {
				$companies_folder->save ();
				$folders['companies_folder'] = $companies_folder->get ();
				}
			catch (WP_CRM_Exception $exception) {
				$companies_folder = null;
				}

			$wp_crm_user->set ('settings', $folders);
			}
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

		load_plugin_textdomain ('WP_CRM_Plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');

                add_filter ('pre_set_site_transient_update_plugins', array ('WP_CRM_Plugin_Wrapper', 'check_update'));
                add_filter ('plugins_api', array ('WP_CRM_Plugin_Wrapper', 'check_info'), 10, 3);
                }
	}


register_activation_hook (__FILE__, array ('WP_CRM_Plugin_Wrapper', 'activate'));
add_action ('init', array ('WP_CRM_Plugin_Wrapper', 'update'));
add_action ('switch_theme', array ('WP_CRM_Plugin_Wrapper', 'theme'));
?>
