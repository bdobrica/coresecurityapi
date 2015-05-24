<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Notices are messages passed from the system to users. Usually bound to an event via an action.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Notice extends WP_CRM_Model {
	public static $T = 'notices';

	protected static $K = array (
		'oid',				/** office id */
		'cid',				/** company id */
		'uid',				/** recipient id */
		'type',				/** the type of this notice */
		'context',			/** the context of this notice */
		'reference',			/** reference WP_CRM_Object-#ObjectID */
		'title',			/** the notice subject */
		'description',			/** the notice content */
		'status',			/** the notice status. can be either read(0) or unread(1) */
		'stamp',			/** the time when the message was sent */
		);

	protected static $M_K = array (
		);

	protected static $U = array (
		);

	public static $F = array (
		'new' => array (
			'uid' => 'Destinatar',
			'title' => 'Subiect',
			'description' => 'Continut'
			),
		'edit' => array (
			),
		'view' => array (
			'reference' => 'Referinta',
			'title:string' => 'Subiect',
			'description:string' => 'Continut',
			'stamp:date' => 'Data'
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
		'`oid` int NOT NULL DEFAULT 0',
		'`cid` int NOT NULL DEFAULT 0',
		'`uid` int NOT NULL DEFAULT 0',
		'`type` varchar(32) NOT NULL DEFAULT \'\'',
		'`context` text NOT NULL',
		'`reference` text NOT NULL',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`status` enum(\'read\', \'unread\') DEFAULT \'unread\'',
		'`stamp` int NOT NULL DEFAULT 0'
		);

	public static function notice ($event = null, $who = null, $what = null, $from = null, $when = null) {
		global
			$wp_crm_user;

		if (is_null ($who)) $who = is_object ($wp_crm_user) ? $wp_crm_user : ($wp_crm_user = new WP_CRM_User (FALSE));
		if (is_null ($when)) $when = time ();

		switch ($event) {
			case 'timer':
				$title = 'Timer';
				$description = '';
				$reference = '';
				break;
			case 'save':
				$title = 'Creare';
				$description = '';
				$reference = '';
				break;
			case 'delete':
				$title = '';
				$description = '';
				$reference = '';
				break;
			case 'edit':
				$title = '';
				$description = '';
				$reference = '';
				break;
			case 'order':
				$title = '';
				$description = '';
				$reference = '';
				break;
			default:
				$title = '';
				$description = '';
				$reference = '';
				break;
			}
		$notice = new WP_CRM_Notice (array (
			'type'		=> $event,
			'context'	=> serialize (array (
						)),
			'reference'	=> $reference,
			'title'		=> $title,
			'description'	=> $description,
			'status'	=> 'unread',
			'stamp'		=> $when
			));
		try {
			$notice->save ();
			return TRUE;
			}
		catch (WP_CRM_Exception $exception) {
			return FALSE;
			}
		}
	};
?>
