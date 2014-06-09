<?php
/**
 * The WP_CRM_Process is a tree of tasks.
 */
class WP_CRM_Process extends WP_CRM_Model {
	public static $T = 'processes';

	protected static $K = array (
		'oid',					// the office this process is assigned to
		'cid',					// the company this process is assigned to; if 0, it's attached to office
		'pid',					// the product this process is assigned to; if 0, it's attached to company
		'title',
		'description'
		);

	public static $F = array (
		);

	protected static $Q = array (
		);
	}
?>
