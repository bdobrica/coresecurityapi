<?php
/**
 * WP_CRM_Office is a group of companies that share
 * owner and employees.
 * @see WP_CRM_Company
 */
class WP_CRM_Office extends WP_CRM_Model {
	public static $T = 'offices';
	protected static $K = array (
	    'uid',
		'name',
		'description',
		'url',
		'companies'
		);
	public static $F = array (
		'new' => array (
			'name' => 'Denumire',
			'description:textarea' => 'Descriere',
			'url' => 'Link',
			#'companies' => 'Companii Membre'
			),
		'view' => array (
			'name' => 'Denumire',
			'type' => 'Tip',
			'description' => 'Descriere',
			'url' => 'Link',
			#'companies' => 'Companii Membre'
			),
		'edit' => array (
			'name' => 'Denumire',
			'description:textarea' => 'Descriere',
			'url' => 'Link',
			#'companies' => 'Companii Membre'
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT  \'\'',
		'`name` varchar(64) NOT NULL DEFAULT \'\' UNIQUE',
		'`description` text NOT NULL',
		'`url` text NOT NULL',
		'`companies` text NOT NULL'
		);
	/**
	 * Mapping roles to maximum companies held in one office
	 * @var array
	 */
	private static $ALLOWED_COMPANIES = array (
		'wp_crm_admin'		=> -1,
		'wp_crm_acountant'	=> 0,
		);
	/**
	 * Mapping roles to maximum offices held by one user
	 * The user offices' array is stored in _wp_crm_offices
	 * meta property.
	 * @var array
	 */
	public static $ALLOWED_OFFICES = array (
		'wp_crm_admin'		=> -1,
		'wp_crm_acountant'	=> 0,
		);

	public function add ($company = null) {
		if (is_numeric ($company)) {
			try {
				$company = new WP_CRM_Company ((int) $company);
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$company = null;
				}
			}
		if (is_object ($company)) {
			$companies = $this->get ('companies');
			if ($companies)
				$companies = unserialize ($companies);
			else
				$companies = array ();
			$companies[] = $company->get ();
			$this->set ('companies', serialize ($companies));
			}
		}

	public function del ($company = null) {
		if (is_numeric ($company)) {
			try {
				$company = new WP_CRM_Company ((int) $company);
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$company = null;
				}
			}
		if (is_object ($company)) {
			$companies = $this->get ('companies');
			if ($companies)
				$companies = unserialize ($companies);
			else
				$companies = array ();
			$companies = array_diff ($companies, array ($company->get()));
			$this->set ('companies', serialize ($companies));
			}
		}

	public function set ($key = null, $value = null) {
		global $current_user;
		$current_user = wp_get_current_user ();

		if (is_string ($key) && ($key == 'owner')) {
			if (is_null ($value)) {
				$value = $current_user->ID;
				}
			if (is_object ($value)) {
				$value = $value->ID;
				}
			if (is_numeric ($value)) {
				$offices = get_user_meta ($value, $wpdb->prefix . WP_CRM_OFFICE::$T, TRUE);
				if (is_array ($offices)) {
					if (!in_array ($this->ID, $offices)) {
						$offices[] = $this->ID;
						update_user_meta ($value, $wpdb->prefix . WP_CRM_OFFICE::$T, $offices);
						}
					}
				else {
					if ($this->ID != $offices) {
						update_user_meta ($value, $wpdb->prefix . WP_CRM_OFFICE::$T, $this->ID);
						}
					}
				}
			return TRUE;
			}
		parent::set ($key, $value);
		}
	}
?>
