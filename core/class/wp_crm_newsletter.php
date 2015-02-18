<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Object used in managing newsletters. It uses Events and Actions to fire.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Newsletter extends WP_CRM_Model {
	public static $T = 'newsletters';
	protected static $K = array (
		'oid',					/** office id */
		'cid',					/** company id */
		'uid',					/** the user sending the newsletter id */
		'recipients',				/** the recipient list: either WP_CRM_Object-#ObjectID comma separated list, either WP_CRM_{Group} object */
		'subject',				/** the subject */
		'message',				/** the content */
		'attachments',				/** attached files, as comma separated objects WP_CRM_File-#FileID */
		'status',				/** the status of the newsletter */
		'stamp'					/** the destination time and date */
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'recipients' => 'Destinatari',
			'subject' => 'Subiect',
			'message' => 'Mesaj',
			'attachments' => 'Atasamente',
			'stamp' => 'Data livrarii'
			),
		'edit' => array (
			),
		'view' => array (
			'subject:string' => 'Subiect',
			'attachments' => 'Atasamente',
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
		'`recipients` text NOT NULL',
		'`subject` text NOT NULL',
		'`message` text NOT NULL',
		'`status` enum(\'sent\',\'queued\',\'canceled\') NOT NULL DEFAULT \'queued\'',
		'`stamp` int NOT NULL DEFAULT 0',
		);
	};
?>
