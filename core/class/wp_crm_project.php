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
class WP_CRM_Project extends WP_CRM_Model {
	public static $T = 'projects';
	protected static $K = array (
		'uid',				/** user ID */
		'oid',				/** office ID */
		'cid',				/** company ID */
		'programme_id',			/** financing programme ID> ex. POR, POC, POCU */
		'axis_id',			/** financing axis ID> 331, 332, 112 */
		'client',			/** client ID - company or person */
		'registration',			/** registration ID */
		'project_id',			/** financing ID */
		'begin',			/** project start date (stamp) */
		'end',				/** project end date */
		'project',			/** link to a file object containing the project versions */
		'checklist'			/** checklist object. shouldn't be an extension of WP_CRM_Model */
						/**
						 * The CheckList object should play well with WP_CRM_Form_Structure. It should be a collection
						 * of fields (strings, numbers and files) with filters. 
						 * The CheckList object is stored serialized.
						 */

/**
 * The Project object has structure. A project is build from activities. There are a number of base activities (included in the project).
 * As usual, activities have people assigned to them. And resources. And machines. Useful to build budgets.
 */
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	/**
	 * The link ids specified below are for partners. The client is specified in the client field
	 */
	protected static $L = array (
		'WP_CRM_Company',
		'WP_CRM_Person'
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
	protected static $Q;
	};
?>
