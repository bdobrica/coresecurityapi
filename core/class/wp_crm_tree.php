<?php
/**
 * WP_CRM_Tree is a wrapper class to tree like collections of objects. Mostly behaves like WP_CRM_List.
 * Should be WP_CRM_View compatible.
 * WP_CRM_Tree should be used in conjunction with WP_CRM_(Company)_Structure to define the organigram of
 * the company, with WP_CRM_Process to define the WP_CRM_Task tree inside a company process etc.
 */
class WP_CRM_Tree {
	private $class;
	private $filter;
	private $tree;

	private static function leaf ($id = null, $data = null, $stem = null) {
		return (object) array (
			'id' => (int) ($id),
			'data' => $data,
			'stem' => (int) ($stem)
			);
		}

	public function grow ($data = null) {
		$leaves = array ();
		$list = array ();

		if (is_null ($data)) return;
		if (empty ($data)) return;

		foreach ($data as $id => $raw)
			$list[] = self::leaf ($id, $raw['data'], $raw['parent']);

		foreach ($list as $item)
			$leaves[$item->stem][] = $item;

		foreach ($list as $item)
			if (isset ($leaves[$item->id]))
				$item->leaves = $leaves[$item->id];

		$this->tree = $leaves[0];
		}

	private function flat ($data = null) {
		if (is_null ($data)) $data = $this->tree;
		
		$flat = array ();

		foreach ($data as $leaf) {
			$flat[$leaf->data->get()] = $leaf->data;
			}

		foreach ($data as $leaf) {
			$flat = array_merge ($flat, $this->flat ($leaf->leaves));
			}

		return $flat;
		}

	private function load () {
		global $wpdb;
		$class = $this->class;
		$sql = $wpdb->prepare ('select id from `' . $wpdb->prefix . $class::$T . '` where ' . (empty($this->filter) ? 1 : implode (' and ', $this->filter)), null);
		echo $sql;
		$ids = $wpdb->get_col ($sql);

		$data = array ();

		if (!empty($ids))
			foreach ($ids as $id)
				if (!isset($data[$id])) {
					try {
						$data[$id] = array (
							'data' => null,
							'parent' => null
							);
						$object = new $this->class ((int) $id);
						$data[$id]['data'] = $object;
						$parent = $object->get ('parent');
						if ($parent == $id) $parent = 0;
						$data[$id]['parent'] = $parent;
						}
					catch (WP_CRM_Exception $wp_crm_exception) {
						}
					}

		$this->grow ($data);
		}

	public function __construct ($class, $filter = null) {
		$this->class = $class;
		$this->filter = $filter;
		$this->tree = null;
		}

	public function get ($key = null, $opts = null) {
		if (is_null ($this->tree))
			$this->load ();
		switch ((string) $key) {
			case 'list':
				return $this->flat ();
				break;
			case 'tree':
				return $this->tree;
				break;
			}
		return json_encode ($this->tree);
		}

	public function set ($key = null, $value = null) {
		}

	public function is ($key = null, $opts = null) {
		}
	}
?>
