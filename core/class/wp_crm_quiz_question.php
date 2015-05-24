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
class WP_CRM_Quiz_Question extends WP_CRM_Model {
	public static $T = 'quiz_questions';

	private static $TYPES = array (
		'check'	=> 'Multiple Choice',
		'radio'	=> 'Single Choice',
		'texta'	=> 'Multiline Text',
		'texti'	=> 'Singleline Text',
		);

	protected static $K = array (
		'parent',
		'question',
		'type',
		'grade',
		'valid'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'question' => 'Intrebare',
			'type:array;type_list' => 'Tip',
			'answers:children?type=check' => 'Answers'
			),
		'edit' => array (
			'question' => 'Intrebare',
			'type:array;type_list' => 'Tip',
			'answers:children?type=check' => 'Answers'
			),
		'view' => array (
			'question' => 'Intrebare',
			'type:array;type_list' => 'Tip',
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
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`question` text NOT NULL',
		'`type` varchar(5) NOT NULL DEFAULT \'texti\'',
		'`grade` int(11) NOT NULL DEFAULT 1',
		'`valid` text NOT NULL',
		'INDEX (`parent`)'
		);

	private $answers;

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'type_list':
					return self::$TYPES;
					break;
				case 'answers':
					if (!is_object ($this->answers))
						$this->answers = new WP_CRM_List ('WP_CRM_Quiz_Answer', array (sprintf ('parent=%d', $this->ID)));
					return $this->answers;
					break;
				}
			}

		return parent::get ($key, $opts);
		}
	};
?>
