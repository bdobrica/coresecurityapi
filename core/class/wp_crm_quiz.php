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
class WP_CRM_Quiz extends WP_CRM_Model {
	public static $T = 'quizzes';
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
			'title' => 'Title',
			'description:textarea' => 'Description',
			'questions:children' => 'Intrebari',
			),
		'edit' => array (
			'title' => 'Title',
			'description:textarea' => 'Description',
			'questions:children' => 'Intrebari',
			),
		'view' => array (
			'title' => 'Title',
			'description:textarea' => 'Description',
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
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	private $questions;

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'questions':
					if (!is_object ($this->questions))
						$this->questions = new WP_CRM_List ('WP_CRM_Quiz_Question', array (sprintf ('parent=%d', $this->ID)));
					return $this->questions;
					break;
				}
			}
		return parent::get ($key, $opts);
		}
	};
?>
