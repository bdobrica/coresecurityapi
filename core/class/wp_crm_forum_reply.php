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
class WP_CRM_Forum_Reply extends WP_CRM_Model {
	public static $T = 'forum_replies';
	protected static $K = array (
		'uid',
		'parent',
		'description',
		'stamp',
		'flags'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'description'		=> 'Reply'
			),
		'edit' => array (
			'description'		=> 'Reply'
			),
		'view' => array (
			'description'		=> 'Reply'
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`description` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'`flags` int(1) NOT NULL DEFAULT 0',
		'INDEX(`parent`)',
		'FULLTEXT(`description`)'
		);

	public function render ($class = '') {
		$out = '';
		$user = new WP_CRM_User ((int) $this->data['uid']);
		$out .= '<label>' . $user->get ('name') . '</label>';
		$out .= '<div>' . $this->data['description'] . '</div>';
		return $out;
		}
	};
?>
