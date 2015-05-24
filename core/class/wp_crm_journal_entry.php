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
class WP_CRM_Journal_Entry extends WP_CRM_Model {
	private static $TYPES = array (
		'event'		=> 'Eveniment',
		'todo'		=> 'ToDo',
#		'journal'	=> 'Jurnal',
#		'freebusy'	=> '',
		);
	public static $T = 'journal_entries';
	protected static $K = array (
		'jid',				/** journal id	*/
		'oid',				/** office id */
		'cid',				/** company id */
		'uid',				/** user (that added this) id */
		'organizer',			/** object reference to organizer */
		'title',			/** the title of this event */
		'description',			/** the description of this event */
		'tags',				/** the tags attached to this event */
		'type',				/** the type of this event. types are listed in the static::$TYPES from iCalendar format */
		'begin',			/** begin, as time stamp */
		'end',				/** end, as time stamp */
		'stamp',			/** created stamp, as time stamp */
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title' => 'Denumire',
			'jid:array;journal_list' => 'Calendar',
			'description:textarea' => 'Descriere',
			'begin:date' => 'Data de inceput',
			'end:date' => 'Data de final'
			),
		'edit' => array (
			'title' => 'Denumire',
			'jid:array;journal_list' => 'Calendar',
			'description:textarea' => 'Descriere',
			'begin:date' => 'Data de inceput',
			'end:date' => 'Data de final'
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
		'`jid` int NOT NULL DEFAULT 0',
		'`oid` int NOT NULL DEFAULT 0',
		'`cid` int NOT NULL DEFAULT 0',
		'`uid` int NOT NULL DEFAULT 0',
		'`organizer` varchar(64) NOT NULL DEFAULT \'\'',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`tags` text NOT NULL',
		'`type` varchar(32) NOT NULL DEFAULT \'event\'',
		'`begin` int NOT NULL DEFAULT 0',
		'`end` int NOT NULL DEFAULT 0',
		'`stamp` int NOT NULL DEFAULT 0'
		);

	private $journals;

	public function get ($key = null, $opts = null) {
		if (is_string ($key))
			switch ($key) {
				case 'journal_list':
					if (!empty ($this->journals)) return $this->journals;
					$this->journals = array ();
					$journals = new WP_CRM_List ('WP_CRM_Journal');
					if (!$journals->is ('empty'))
						foreach ($journals->get() as $journal)
							$this->journals[$journal->get()] = $journal->get ('title');
					return $this->journals;
					break;
				}
		return parent::get ($key, $opts);
		}

	public function render ($class) {
		return '<h3>' . $this->data['title'] . '</h3>
<div><i class="fa fa-calendar"></i> ' . date ('d-m-Y', $this->data['begin']) . ' - ' . date ('d-m-Y', $this->data['end']) . '</div>
<div class="' . $class . '-journal-entry">' . $this->data['description'] . '</div>
<div class="clearfix" style="margin-top: 2em;"></div>
<button class="btn btn-sm btn-danger btn-block wp-crm-form-button-close"><i class="fa fa-times"></i> Inchide</button>';
		}
	};
?>
