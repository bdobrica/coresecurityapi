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
class WP_CRM_Forum_Topic extends WP_CRM_Model {
	public static $T = 'forum_topics';
	protected static $K = array (
		'uid',
		'parent',
		'title',
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
		'`description` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'`flags` int(1) NOT NULL DEFAULT 0',
		'INDEX(`parent`)',
		'FULLTEXT(`title`,`description`)'
		);

	public function render ($class = '') {
		$out = '';
		$out .= '<label>' . $this->data['title'] . '</label>';
		$out .= '<div>' . $this->data['description'] . '</div> <hr />';
		$out .= '<div>';
		$replies = new WP_CRM_List ('WP_CRM_Forum_Reply', array (sprintf ('parent=%d', $this->ID)));
		if (!$replies->is ('empty'))
		foreach ($replies->get() as $reply) {
			$out .= $reply->render ($class);
			}
		$out .= '</div>';
		$out .= '<form action="" method="post"><textarea rows="5" name="" class="form-control"></textarea><button class="btn btn-primary btn-block">Raspunde</button></form>';
		return $out;
		}
	};
?>
