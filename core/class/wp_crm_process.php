<?php
/**
 * The WP_CRM_Process is a tree of tasks.
 */
class WP_CRM_Process extends WP_CRM_Model {
	public static $T = 'processes';

	protected static $K = array (
		'oid',					// the office this process is assigned to
		'cid',					// the company this process is assigned to; if 0, it's attached to office
		'title',
		'description'
		);

	public static $F = array (
		'new' => array (
			'title' => 'Nume',
			'descriere' => 'Descriere',
			'cid:company' => 'Companie'
			),
		'edit' => array (
			'title' => 'Nume',
			'description' => 'Descriere',
			'cid:company' => 'Companie',
			'tasks:tree' => 'Taskuri'
			),
		'view' => array (
			'title' => 'Nume',
			'description' => 'Descriere',
			),
		);

	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL'
		);

	public function get ($key = null, $opts = null) {
		if (is_string ($key)) {
			switch ($key) {
				case 'tasks':
					$tree = new WP_CRM_Tree ('WP_CRM_Task', array (
						sprintf ('pid=%d', $this->ID)
						));
					return $tree;
					break;
				}
			}
		return parent::get ($key, $opts);
		}
	}
?>
