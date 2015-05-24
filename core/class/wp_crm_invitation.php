<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Dummy object. Shows how to create a new object.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Invitation extends WP_CRM_Model {
	public static $T = 'invitations';
	protected static $K = array (
		'uid',
		'hash',
		'first_name',
		'last_name',
		'email',
		'phone',
		'subject',
		'content',
		'state',
		'stamp',
		'flags'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		'uid',
		'email'
		);
	public static $F = array (
		'new' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume de familie',
			'email' => 'Adresa de email',
			'phone' => 'Numar de telefon',
#			'subject' => 'Subiectul invitatiei',
#			'content:rte' => 'Mesaj'
			),
		'edit' => array (
			'first_name' => 'Prenume',
			'last_name' => 'Nume de familie',
			'email' => 'Adresa de email',
			'phone' => 'Numar de telefon',
#			'subject' => 'Subiectul invitatiei',
#			'content:rte' => 'Mesaj'
			),
		'view' => array (
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	protected static $Q = array (
		'`id` int NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int NOT NULL DEFAULT 0',
		'`hash` varchar(32) NOT NULL DEFAULT \'\'',
		'`first_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`last_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`email` varchar(64) NOT NULL DEFAULT \'\'',
		'`phone` varchar(12) NOT NULL DEFAULT \'\'',
		'`subject` text NOT NULL',
		'`content` text NOT NULL',
		'`state` int NOT NULL DEFAULT 0',
		'`stamp` int NOT NULL DEFAULT 0',
		'`flags` int NOT NULL DEFAULT 0'
		);

	public function __construct ($data = null) {
		global $wpdb;
		if (is_string ($data) && (strlen ($data) == 32)) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where hash=%s;', array (
					$data
					));
			$data = $wpdb->get_row ($sql, ARRAY_A);
			}
		parent::__construct ($data);
		}

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'subject':
					$settings = new WP_CRM_Settings ();
					if (!$this->data['subject']) return $settings->get ('mail_invitation_subject');
					break;
				case 'content':
					$settings = new WP_CRM_Settings ();
					if (!$this->data['content']) return $settings->get ('mail_invitation_content');
					break;
				}
			}
		return parent::get ($key, $opts);
		}

	public function save () {
		$wp_crm_user = new WP_CRM_User (FALSE);
		$this->data['hash'] = md5 ($wp_crm_user->get ('user_email') . time() . $this->data['email']);

		parent::save ();
		/**
		 * Send the email and decrese the invitation counter.
		 */
		
		$wp_crm_settings = new WP_CRM_Settings ();

		$wp_crm_mail = new WP_CRM_Mail ($wp_crm_settings->get ('email_settings'));
	
		$invitation_url = get_bloginfo ('url') . '/signup?h=' . $this->data['hash'];

		$wp_crm_template = new WP_CRM_Template ($wp_crm_settings->get ('invitation_mail'));
		$wp_crm_template->assign ('invitation_url', $invitation_url);
		$wp_crm_template->assign ('user.name', $wp_crm_user->get ('full_name'));

		$wp_crm_mail->send ($this->data['email'], $wp_crm_template);

		$invitations = (int) $wp_crm_user->get ('defaults', 'promoter_invitations');
		$invitations --;
		$wp_crm_user->set ('defaults', array (
			'promoter_invitations' => $invitations
			));
		}
	};
?>
