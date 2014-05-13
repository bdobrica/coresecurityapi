<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

$class = $_GET['class'];
if (!class_exists ($class)) die ();
if (strpos ($class, 'WP_CRM_') !== 0) die ();
if (!preg_match ('/^[A-z_]+$/', $class)) die ();

$wp_crm_object = new $class ((int) $_GET['id']);
echo $wp_crm_object->get ($_GET['key']);
?>
