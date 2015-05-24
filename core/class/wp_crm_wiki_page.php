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
class WP_CRM_Wiki_Page extends WP_CRM_Model {
	public static $T = 'wiki_pages';
	protected static $K = array (
		'uid',					/** User ID */
		'parent',				/** Wiki ID */
		'slug',					/** The Page Slug - should be almost unique. The unique key is (slug, version) */
		'title',				/** The Page Title */
		'content',				/** The Page Content */
		'status',				/** The Page Status. Shows for example if the page is approved. */
		'version',				/** The Page Version. All users except wiki owner and page onwers see the last approved version. */
		'stamp'					/** Creation/Update Time */
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
		'`slug` varchar(128) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`content` text NOT NULL',
		'`status` int(11) NOT NULL DEFAULT 0',
		'`version` int(11) NOT NULL DEFAULT 0',
		'`stamp` int(11) NOT NULL DEFAULT 0',
		'INDEX(`parent`)',
		'UNIQUE(`slug`)',
		'FULLTEXT(`title`,`content`)'
		);

	public function render ($class) {
		$out = '';

		$out .= '<div class="row clearfix">
		<div class="col-md-12">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-render">Page</a></li>
				<li><a href="#tab-source">Edit</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="tab-render">' . $this->data['content'] . '
				</div>
				<div class="tab-pane" id="tab-source">
					<textarea name="" rows="20" style="width: 100%;">' . $this->data['content'] . '</textarea>
					<button class="btn btn-danger">Cancel</button>
					<button class="btn btn-success">Salveaza</button>
				</div>
			</div>
		</div>
	</div>';
		
		return $out;
		}
	};
?>
