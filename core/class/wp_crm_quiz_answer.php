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
class WP_CRM_Quiz_Answer extends WP_CRM_Model {
	public static $T = 'quiz_answers';
	protected static $K = array (
		'parent',
		'answer',
		'grade',
		'valid'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'answer'	=> 'Answer',
			'valid:switch'	=> 'Valid',
			'grade'		=> 'Grade'
			),
		'edit' => array (
			'answer'	=> 'Answer',
			'valid:switch'	=> 'Valid',
			'grade'		=> 'Grade'
			),
		'view' => array (
			'answer'	=> 'Answer',
			'valid:switch'	=> 'Valid',
			'grade'		=> 'Grade'
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
		'`answer` text NOT NULL',
		'`grade` int(11) NOT NULL DEFAULT 1',
		'`valid` int(1) NOT NULL DEFAULT 0',
		'INDEX (`parent`)'
		);
	};
?>
