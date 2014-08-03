#!/usr/bin/php
<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

spl_autoload_register (function ($class) {
	$class_file = dirname(dirname(__FILE__)) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

$events = new WP_CRM_List ('WP_CRM_Event', array ('flags=' . WP_CRM_Event::Active));

if (!$events->is ('empty'))
	foreach ($events->get () as $event)
		$event->fire ();
?>
