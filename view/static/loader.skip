<?php
/**
 * Test objects method inside the WP_CRM ecosystem.
 */
ini_set ('display_errors', 1);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
WP_CRM_APP::scan();
spl_autoload_register (function ($class) {
        $class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
        if (file_exists($class_file)) include ($class_file);
        });
?>