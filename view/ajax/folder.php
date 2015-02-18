<?php
ini_set ('display_errors', 1);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

list ($class, $id) = $_GET['object'] ? explode ('-', $_GET['object']) : explode ('-', $_POST['object']);
if (!class_exists ($class)) die ('Err.');
if (!is_numeric($id)) die ('Err.');

$object = new $class ((int) $id);

$view = new WP_CRM_View ($object, array (
	'toolbar' => array (
		'add' => 'Creaza',
		'upload' => 'Incarca'
		),
	'item' => array (
		),
	));
echo "OK\nREDRAW:.wp-crm-view-folder-wrapper\n";
unset ($view);
?>
