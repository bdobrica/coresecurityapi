#!/usr/bin/php
<?php
ini_set ('error_reporting', 0);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

spl_autoload_register (function ($class) {
	$class_file = dirname(dirname(__FILE__)) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

$jabber = new WP_CRM_JabberAuth ();
$jabber->listen ();
?>
