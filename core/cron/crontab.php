#!/usr/bin/php
<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-blog-header.php');

spl_autoload_register (function ($class) {
	$class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

/**
 * the correct way to fire an event:
 * step 1. check if the event exists. if not,
 * step 2. create the event
 * step 3. fire the event
 */
try {
	$event = new WP_CRM_Event ('timer');
	}
catch (WP_CRM_Exception $wp_crm_exception) {
	$event = new WP_CRM_Event (array (
			'event' => 'timer',
			'context' => serialize (array ('time' => 'int'))
			));
	$event->save ();
	}

$event->fire (array ('time' => time()));
?>
