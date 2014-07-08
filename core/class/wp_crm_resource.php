<?php
/**
 * Resource managing class. Although, humans are resources also, the resources described here are mostly involved in manufacturing
 * and service delivery. Resources can be extensive (are in direct relation with supported Products) or intensive.
 * Resources can be either finite or infinite. Resources can be either money or items/services bought with money.
 */
class WP_CRM_Resource extends WP_CRM_Model {
	public static $T = 'resources';

	protected static $K = array (
		'oid',				// the organization group
		'cid',				// the id of the company that is keeping track of this resources.
		#'tid',				// the task id that uses this resource. all resources are used in some task.
						// tasks make up processes which in turn are attached to products
						// processes may be attached to companies and offices also!
		'title',			// the name of the resource
		'description',			// some description of the resource
		'stock'				// quantity
		);

	public static $F = array (
		'new' => array (
			'title' => 'Denumire',
			'description' => 'Descriere',
			'stock' => 'Cantitate initiala'
			),
		'edit' => array (
			'title' => 'Denumire',
			'description' => 'Descriere',
			'stock' => 'Cantitate initiala'
			),
		'view' => array (
			'title' => 'Denumire',
			'description' => 'Descriere',
			'stock' => 'Cantitate initiala'
			)
		);
	
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`stock` int(11) NOT NULL DEFAULT 0'
		);
	}
?>
