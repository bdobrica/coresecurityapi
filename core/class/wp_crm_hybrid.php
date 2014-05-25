<?php
/**
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 * This is a class to dynamically link multiple objects,
 * with one object acting as an origin (tree root) and
 * other objects spanning bellow.
 * Each WP_CRM_Hybrid is an object in the database.
 * Idea came from attaching to WP_User a
 *	- WP_CRM_Person
 *	- WP_CRM_Company/WP_CRM_Office
 */
class WP_CRM_Hybrid extends WP_CRM_Model {
	public static $T = 'hybrid';
	}
