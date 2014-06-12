<?php
/**
 * WP_CRM_Tree is a wrapper class to tree like collections of objects. Mostly behaves like WP_CRM_List.
 * Should be WP_CRM_View compatible.
 * WP_CRM_Tree should be used in conjunction with WP_CRM_(Company)_Structure to define the organigram of
 * the company, with WP_CRM_Process to define the WP_CRM_Task tree inside a company process etc.
 */
class WP_CRM_Tree {
	private $tree;

	private function _leaf ($id = null, $stem = null, $data = null) {
		return (object) array (
			'id' => (int) ($id),
			'data' => $data,
			'stem' => (int) ($stem)
			);
		}

	private function _grow ($list) {
		$leafs = array ();
		foreach ($list as $item)
			$leafs[$item->stem][] = $item;

		foreach ($list as $item)
			if (isset ($leafs[$item->id]))
				$item->leafs = $leafs[$item->id];

		$this->tree = $leafs[0];
		}

	public function __construct ($data = null) {
		}

	public function get ($key = null, $opts = null) {
		}

	public function set ($key = null, $value = null) {
		}

	public function is ($key = null, $opts = null) {
		}
	}
?>
