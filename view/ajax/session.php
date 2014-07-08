<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

global
	$wp_crm_buyer,
	$wp_crm_state;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();
$wp_crm_state->set ('state', WP_CRM_State::EditObject);

list ($class, $id) = $_GET['object'] ? explode ('-', $_GET['object']) : explode ('-', $_POST['object']);
if (!class_exists ($class)) die ('Err.');
if (!is_numeric($id)) die ('Err.');

$object = new $class ((int) $id);

$list = new WP_CRM_List ('WP_CRM_Product');
$view = new WP_CRM_View ($list);
?>
