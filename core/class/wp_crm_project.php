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
	public static $PROGRAMMES = array (
		'POAT',
		'PODCA',
		'POST',
		'POSM',
		'POSDRU',
		'POSCCE',
		'POR',
		'PNDR'
		);
	public static $AXIS = array (
		);

	public static $T = 'projects';
	protected static $K = array (
		'uid',				/** user ID */
		'oid',				/** office ID */
		'cid',				/** company ID */
		'title',			/** the project title as it will be displayed in the interface */
		'project_title',		/** the project title as it was filled in the project application */
		'description',			/** the project summary */
		'tags',				/** a few project tags */
		'programme_id',			/** financing programme ID> ex. POR, POC, POCU */
		'axis_id',			/** financing axis ID> 331, 332, 112 */
		'client',			/** client ID - company or person, ->get('self') */
		'registration',			/** registration ID */
		'project_id',			/** financing ID */
		'begin',			/** project start date (stamp) */
		'end',				/** project end date */
		'budget',			/** the project budget */
		'cofinancing',			/** the project cofinancing */
		'project',			/** link to a file object containing the project versions */
		'stamp'
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
			'title' => 'Denumire',
			'programme_id' => 'Program',
			'axis_id' => 'Axa',
			'company' => 'Companie',
			'budget' => 'Buget',
			'cofinancing' => 'Cofinantare',
			),
		'safe' => array (
			),
		'excerpt' => array (
			),
		'group' => array (
			)
		);
	protected static $Q = array (
		'`id` int NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int NOT NULL DEFAULT 0',
		'`cid` int NOT NULL DEFAULT 0',
		'`uid` int NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`project_title` text NOT NULL',
		'`description` text NOT NULL',
		'`tags` text NOT NULL',
		'`programme_id` varchar(16) NOT NULL DEFAULT \'\'',
		'`axis_id` varchar(16) NOT NULL DEFAULT \'\'',
		'`client` varchar(32) NOT NULL DEFAULT \'\'',
		'`registration` varchar(32) NOT NULL DEFAULT \'\'',
		'`project_id` varchar(32) NOT NULL DEFAULT \'\'',
		'`begin` int NOT NULL DEFAULT 0',
		'`end` int NOT NULL DEFAULT 0',
		'`budget` float(11,2) NOT NULL DEFAULT 0.00',
		'`cofinancing` float(11,2) NOT NULL DEFAULT 0.00',
		'`project` text NOT NULL',
		'`stamp` int NOT NULL DEFAULT 0'
		);

	public function get ($key = null, $opts = null) {
		if ($key == 'company') {
			if (!$this->data['client']) return '';
			list ($class, $id) = explode ('-', $this->data['client']);
			try {
				$company = new WP_CRM_Company ((int) $id);
				}
			catch (WP_CRM_Exception $exception) {
				$company = null;
				}
			return !is_null ($company) ? $company->get ('name') : '';
			}
		return parent::get ($key, $opts);
		}
	};
?>
