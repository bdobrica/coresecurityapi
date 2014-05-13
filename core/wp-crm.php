<?php
/*
Plugin Name: WP CRM API
Plugin URI: http://www.ublo.ro
Description: WP CRM API
Author: Bogdan Dobrica
Version: 0.1
Author URI: http://www.ublo.ro
*/

spl_autoload_register (function ($class) {
	$class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

?>
