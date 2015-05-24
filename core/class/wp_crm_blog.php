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
class WP_CRM_Blog extends WP_CRM_Model {
	public static $T = 'blogs';
	protected static $K = array (
		'oid',						/** the office id								*/
		'cid',						/** the company id 								*/
		'uid',						/** the user that generated this task						*/
		'title',
		'description',
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title'	=> 'Denumire',
			'description' => 'Descriere',
			),
		'edit' => array (
			'title'	=> 'Denumire',
			'description' => 'Descriere',
			),
		'view' => array (
			'title'	=> 'Denumire',
			'description' => 'Descriere',
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
		'`oid` int(11) NOT NULL DEFAULT 0',		/** office id									*/
		'`cid` int(11) NOT NULL DEFAULT 0',		/** company id									*/
		'`uid` int(11) NOT NULL DEFAULT 0',		/** the user that generated this product					*/
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	public function render ($class = '') {
		$out = '';
		$entries = new WP_CRM_List ('WP_CRM_Blog_Entry', array (sprintf ('parent=%d', $this->ID)));
		$out .= '<h2>' . $this->data['title'] . '</h2>';
		$out .= '<em>' . $this->data['description'] . '</em><hr />';
		if (!$entries->is ('empty'))
		foreach ($entries->get() as $entry)
			$out .= $entry->render ($class);
		
		return $out;
		}
	};
?>
