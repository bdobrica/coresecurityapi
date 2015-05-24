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
class WP_CRM_Forum extends WP_CRM_Model {
	public static $T = 'forums';
	protected static $K = array (
		'oid',						/** the office id								*/
		'cid',						/** the company id 								*/
		'uid',
		'parent',
		'title',
		'description',
		'admins',					/** list of WP_CRM_User objects that can take care of this forum */
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title' => 'Title',
			'description' => 'Description',
			'admins' => 'Administrators',
			),
		'edit' => array (
			'title' => 'Title',
			'description' => 'Description',
			'admins' => 'Administrators',
			),
		'view' => array (
			'title' => 'Title',
			'description' => 'Description',
			'admins' => 'Administrators',
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
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`admins` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'INDEX(`parent`)',
		'FULLTEXT(`title`,`description`)'
		);

	public function render ($class = '') {
		$out = '';
		$topics = new WP_CRM_List ('WP_CRM_Forum_Topic', array (sprintf ('parent=%d', $this->ID)));
		if (!empty ($topics))
		foreach ($topics->get () as $topic) {
			$out .= $topic->render ($class);
			}
		return $out;
		}
	};
?>
