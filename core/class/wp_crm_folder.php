<?php
/**
 * Folders are holders of files. They can be hierarchical. Only last leaves of the tree can hold documents. Documents can be either automagically or manually assigned, based on filters.
 * Leaves of the folder tree hold versions of files. Files are stored in the WP_CRM_File objects. There is a N:N relation between leaves and files. Folders have views (of their children).
 */
class WP_CRM_Folder extends WP_CRM_Model {
	public static $T = 'folders';

	protected static $K = array (
		'parent',				# the id of the parent
		'child',				# the id of the file linked to this folder, only if the type is leaf
		'mime',					# the mime type of this object directory / mime
		'title',				# the name of the folder
		'description',				# some description (probably not used)
		'versions',				# a serialized array of versions pointing to ids of WP_CRM_File objects
		'current',				# the current version. if 0, use pop on serialized array
		'updated',				# the last modification timestamp
		'stamp',				# the creation timestamp
		'locked',				# is the folder locked?
		'flags'					# some binary flags
		);

	protected static $Q = array (
		'`id` int NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`parent` int NOT NULL DEFAULT 0',
		'`child` int NOT NULL DEFAULT 0',
		'`mime` varchar(256) NOT NULL DEFAULT \'unknown\'',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`versions` text NOT NULL',
		'`current` int NOT NULL DEFAULT 0',
		'`updated` int NOT NULL DEFAULT 0',
		'`stamp` int NOT NULL DEFAULT 0',
		'`locked` int(1) NOT NULL DEFAULT 0',
		'`flags` int NOT NULL DEFAULT 0',
		'KEY (`parent`)'
		);

	public static $F = array (
		'view' => array (
			'title' => 'Nume',
			'description' => 'Descriere',
			),
		'add' => array (
			'title' => 'Nume',
			),
		'edit' => array (
			'title' => 'Nume',
			)
		);

	private $child;
	private $children;
	private $versions;

	public function __construct ($data = null) {
		$this->versions = array ();
		$this->children = null;

		parent::__construct ($data);

		if ($this->data['versions']) $this->versions = self::_unserialize ($this->data['versions']);
		if ($this->data['child']) {
			try {
				$this->child = new WP_CRM_File ((int) $this->data['child']);
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$this->child = null;
				}
			}
		}

	public function get ($key = null, $opts = null) {
		global $wpdb;

		if (is_string ($key)) {
			switch ($key) {
				case 'file_path':
					return $this->child instanceof WP_CRM_File ? $this->child->get ('path') : FALSE;
					break;
				case 'children':
					if ($this->children instanceof WP_CRM_List) return $this->children;
					$this->children = new WP_CRM_List ('WP_CRM_Folder', array (sprintf ('parent=%d', $this->ID)));
					return $this->children;
					break;
				case 'versions':
					return $this->versions;
					break;
				case 'parent_title':
					if (!$this->data['parent']) return $this->data['title'];
					$sql = $wpdb->prepare ('select title from `' . $wpdb->prefix . self::$T . '` where id=%d;', $this->data['parent']);
					return $wpdb->get_var ($sql);
					break;
				case 'size':
					return 0;
					break;
				}
			}
		return parent::get ($key, $opts);
		}
	}
?>
