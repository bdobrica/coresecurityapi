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
class WP_CRM_Dictionary extends WP_CRM_Model {
	public static $T = 'dictionaries';
	protected static $K = array (
		'oid',						/** the office id								*/
		'cid',						/** the company id 								*/
		'uid',
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
			'title' => 'Denumire',
			'description' => 'Descriere',
			),
		'edit' => array (
			'title' => 'Denumire',
			'description' => 'Descriere',
			'entries:children' => 'Termeni',
			),
		'view' => array (
			'title' => 'Denumire',
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
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	private $entries;

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'entries':
					if (!is_object ($this->entries))
						$this->entries = new WP_CRM_List ('WP_CRM_Dictionary_Entry', array (sprintf ('parent=%d', $this->ID)));
					return $this->entries;
					break;
				}
			}
		return parent::get ($key, $opts);
		}

	public function render ($class = '') {
		$out = '';

		if (!is_object ($this->entries))
			$this->entries = new WP_CRM_List ('WP_CRM_Dictionary_Entry', array (sprintf ('parent=%d', $this->ID)));

		if (!$this->entries->is ('empty'))	
		foreach ($this->entries->get () as $entry)
			$out .= '<div><strong>' . $entry->get ('title') . '</strong><br />' . $entry->get ('description') . '</div><hr />';
		
		return $out;
		}
	};
?>
