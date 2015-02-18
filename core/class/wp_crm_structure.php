<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Abstract class for defining object dynamic structures.
 * Dynamic structures are tree like, attached to a root object (for which it describes
 * the internal state) and a series of named levels (by slug, name) which can contain
 * references to other objects. A reference is passed by class and object id.
 * The structure:
 * ROOT += WP_CRM_Model (#)
 * 	+- Level 1 (level-1) ---+
 *	|			+-- WP_CRM_Model (#)
 *	|			+-- WP_CRM_Model (#)
 *	|			+-- Level 1.1 (level-1-1) ------+
 *	|							+--- WP_CRM_Model (#)
 *	+- Level 2 (level-2) ---+
 *	|			+-- WP_CRM_Model (#)
 *	...			...
 *
 * @category Abstract
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
abstract class WP_CRM_Structure {
	/**
	 * Version string a.b.c
	 * a # major release: paradigm changed
	 * b # minor release: added removed methods/properties/altered tables
	 * c # review: fixed bugs in already implemented methods/properties
	 */
	public static $version = '1.0.0';
	/**
	 * Constants for defining the tree structure.
	 * ROOT = the root object
	 * STEM = 
	 */
	const ROOT	= 1;
	const STEM	= 2;
	/**
	 * The attached database table. No prefix.
	 * @var string
	 */
	public static $T = '';
	/**
	 * The parent object class.
	 * @var string
	 */
	public static $ROOT = '';
	/**
	 * The child object class.
	 * @var string
	 */
	public static $CHILD = '';
	/**
	 * Pair of (actions, form elements).
	 * Form elements are defined as name[:type] => label, where
	 * 	name is a string containing the database key,
	 *	type is a string containing the type prefixed by :
	 *	label is a string containing the displayed label
	 *	Common types are: text (default), basket, checkbox, radio, select, button, submit, close, textarea, email, password, hidden, label, tos, date, seller, buyer, product, spread, matrix, file
	 * @see WP_CRM_Form::_render()
	 * @var array
	 */
	public static $CHILD_F = array (
		'edit' => array (
			)
		);
	/*
	 * The table structure.
	 * @var array
	 */
	public static $Q = array (
		'`id` int NOT null PRIMARY KEY AUTO_INCREMENT',		/** The structure reference id */
		'`title` text NOT null',				/** The stem name */
		'`description` text NOT null',				/** The stem description */
		'`root` int NOT null DEFAULT 0',			/** The root object id. Root objects have root = 0. */
		'`parent` int NOT null DEFAULT 0',			/** The parent of this leaf */
		'`type` int NOT null DEFAULT 0',			/** The type (ROOT, STEM) of this leaf */
		'`object` varchar(64) NOT null DEFAULT \'\'',		/** The object contained in this leaf. Can be empty. */
		'`oid` int NOT null DEFAULT 0',				/** The id of the object contained in this leaf. */
		'INDEX (`type`)',
		'INDEX (`root`)',
		'UNIQUE (`root`,`object`,`oid`)'
		);


	protected $root;						/** The root id. */
	protected $list;
	protected $tree;
	protected $data;

	public function __construct ($data = null) {
		global $wpdb;

		if (is_object ($data) && (get_class ($data) == static::$ROOT)) {
			$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . static::$T . '` where root=0 and object=%s and oid=%d', array (
				static::$ROOT,
				$data->get ()
				));

			$this->root = $wpdb->get_var ($sql);
			if (!$this->root) {
				$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '` (`object`,`oid`) values (%s,%d);', array (
					static::$ROOT,
					$data->get ()
					));
				$wpdb->query ($sql);
				$this->root = $wpdb->insert_id; 
				}
			if (!$this->root) throw new WP_CRM_Exception (WP_CRM_Exception::Database_Error);

			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where root=%d;', $this->root);
			$stems = $wpdb->get_results ($sql);

			$this->list = array ();
			$this->tree = null;
			$this->data = array ();

			if ($stems) {
				$items = array ();
				foreach ($stems as $stem) {
					if ($stem->object && $stem->oid) {
						try {
							$object = new $stem->object ($stem->oid);
							$this->list[$stem->id] = $object;
							$this->data[$stem->id] = array (
								'title' => $stem->title,
								'description' => $stem->description
								);
							$items[] = (object) array (
								'id' => $stem->id,
								'parent' => $stem->parent,
								'name' => $stem->title,
								'description' => $stem->description,
								'object' => $object,
								'children' => null
								);
							}
						catch (WP_CRM_Exception $wp_crm_exception) {
							$items[] = (object) array (
								'id' => $stem->id,
								'parent' => $stem->parent,
								'name' => $stem->title,
								'description' => $stem->description,
								'object' => null,
								'children' => null
								);
							}
						}
					else {
						$items[] = (object) array (
							'id' => $stem->id,
							'parent' => $stem->parent,
							'name' => $stem->title,
							'description' => $stem->description,
							'object' => null,
							'children' => null
							);
						}
					}

				$children = array ();
				foreach ($items as $item)
					$children[$item->parent][] = $item;

				foreach ($items as $item)
					if (isset ($children[$item->id]))
						$item->children = $children[$item->id];
				
				$this->tree = $children[0];
				}
			}
		}
	
	public function get ($key = null, $opts = null) {
		$out = array ();
		$out[] = array_merge (array ('title' => 'Titlu'), static::$CHILD_F['edit']);

		if (!empty ($this->list)) {
			foreach ($this->list as $id => $item) {
				$row = array ('title' => $this->data[$id]['title']);

				foreach (static::$CHILD_F['edit'] as $key => $label)
					$row[$key] = $item->get ($key);
				
				$out[$id] = $row;
				}
			}

		return $out;
		}

	public function set ($key = null, $value = null) {
		global $wpdb;

		if (!is_array ($key)) {
			return FALSE;
			}
		if (!empty ($key)) {
			foreach ($key as $id => $data) {
				if (!empty ($data)) {
					if (isset ($this->list[$id]) && is_object ($this->list[$id])) {
						$this->list[$id]->set ($data);
						if (!empty ($data['title'])) {
							$sql = $wpdb->prepare ('update `' . $wpdb->prefix . static::$T . '` set title=%s where id=%d;', array (
									$data['title'],
									(int) $id
									));
							$wpdb->query ($sql);
							}
						}
					else {
						$object = null;
						try {
							$object = new static::$CHILD ($data);
							$object->save ();
							}
						catch (WP_CRM_Exception $wp_crm_exception) {
							}

						if (!is_null ($object)) {
							$sql = $wpdb->prepare ('insert into `' . $wpdb->prefix . static::$T . '` (title,description,root,parent,type,object,oid) values (%s,%s,%d,%d,%d,%s,%d);', array (
									$data['title'],
									'',
									$this->root,
									0,
									self::STEM,
									static::$CHILD,
									$object->get ()
									));
							$wpdb->query ($sql);
							$this->list[$wpdb->insert_id] = $object;

							$key[$wpdb->insert_id] = $data;
							unset ($key[$id]);
							}
						}
					}
				}
			if (sizeof ($key) && sizeof ($this->list)) {
				$removed = array_diff (array_keys ($this->list), array_keys ($key));
				print_r ($removed);
				if (!empty ($removed))
					foreach ($removed as $remove) {
						$sql = $wpdb->prepare ('delete from `' . $wpdb->prefix . static::$T . '` where id=%d;', array (
								(int) $remove
								));
						$wpdb->query ($sql);
						}
				}
			}
		}

	public function add ($data = null) {
		if (is_object ($data) && get_class ($data) == $CHILD) {
			}
		}

	public static function install ($unistall = FALSE) {
		global $wpdb;

		if (empty (static::$T)) return;
		if (empty (static::$Q)) return;

		$sql = $uninstall ?
			'drop table `' . $wpdb->prefix . static::$T . '`;' :
			'create table `' . $wpdb->prefix . static::$T . '` (' . implode (',', static::$Q) . ') engine=MyISAM default charset=utf8;';

		if ($wpdb->get_var ('show tables like \'' . $wpdb->prefix . static::$T . '\';') != ($wpdb->prefix . static::$T))
			$wpdb->query ($sql);
		}
	}
?>
