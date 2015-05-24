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
class WP_CRM_Blog_Entry extends WP_CRM_Model {
	public static $T = 'blog_entries';
	protected static $K = array (
		'uid',
		'parent',
		'title',
		'content',
		'status',
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			),
		'edit' => array (
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
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`content` text NOT NULL',
		'`status` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'INDEX(`parent`)',
		'FULLTEXT(`title`,`content`)'
		);

	public function render ($class = '') {
		$out = '';
		
		$out .= '<label>' . $this->data['title'] . '</label>';
		$out .= '<div>' . $this->data['content'] . '</div>';
		$out .= '<hr />';

		return $out;
		}
	};
?>
