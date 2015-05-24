<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Callendar Object.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Journal extends WP_CRM_Model {
	public static $T = 'journals';
	protected static $K = array (
		'oid',
		'cid',
		'uid',
		'title',
		'description',
		'tags',
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title' => 'Denumire',
			'color:color' => 'Culoare',
			'private:bool' => 'Privat'
			),
		'edit' => array (
			'title' => 'Denumire',
			'color:color' => 'Culoare',
			'private:bool' => 'Privat'
			),
		'view' => array (
			'title' => 'Calendar',
			'private' => 'Privat',
			'color' => 'Culoare'
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
		'`color` varchar(6) NOT NULL DEFAULT \'EEEEEE\'',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`tags` text NOT NULL',
		'`stamp` int NOT NULL DEFAULT 0'
		);

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'entries':
					$out = array ();
					$list = new WP_CRM_List ('WP_CRM_Journal_Entry', array (sprintf ('jid=%d', $this->ID)));
					if (!$list->is ('empty'))
					foreach ($list->get () as $entry) {
						$out[] = array (
							'id' => $entry->get ('self'),
							'title' => $entry->get ('title'),
							'start' => $entry->get ('begin'),
							'end' => $entry->get ('end'),
							'color' => strtolower('#' . $this->get ('color')),
							'allDay' => TRUE
							);
						}
					return $out;
					break;
				}
			}
		return parent::get ($key, $opts);
		}
	};
?>
