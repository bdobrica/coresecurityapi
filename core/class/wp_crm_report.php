<?php
/**
 * Core of WP_CRM_*
 */

/**
 * Report is an object used to generate charts and business intelligence reports from existing data.
 *
 * @package WP_CRM
 * @copyright Core Security Advisers SRL
 * @author Bogdan Dobrica <bdobrica @ gmail.com>
 * @version 0.1
 *
 */
class WP_CRM_Report extends WP_CRM_Model {
	public static $T = 'reports';
	protected static $K = array (
		'oid',
		'cid',
		'uid',
		'title',
		'description',
		'xrange',
		'yrange',
		'collection',
		'stamp'
		);
	protected static $M_K = array (
		);
	protected static $U = array (
		);
	public static $F = array (
		'new' => array (
			'title' => 'Denumire raport',
			'description' => 'Descriere raport',
			'xrange' => 'Interval abscisa',
			'yrange' => 'Interval ordonata',
			'collection' => 'Colectie obiecte'
			),
		'edit' => array (
			),
		'view' => array (
			'title' => 'Denumire raport',
			'description' => 'Descriere raport',
			'stamp:date' => 'Data inregitrarii'
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
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`xrange` text NOT NULL',
		'`yrange` text NOT NULL',
		'`collection` text NOT NULL',
		'`stamp` int NOT NULL DEFAULT 0'
		);
	};
?>
