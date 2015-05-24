<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

global
	$wp_crm_buyer,
	$wp_crm_state;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();

list ($object, $context) = $_GET['object'] ? explode (';', $_GET['object']) : explode (';', $_POST['object']);

list ($class, $id) = explode ('-', $object);
if (!class_exists ($class)) die ();
if (!is_numeric ($id)) die ();
$instance = new $class ((int) $id);

$objects = explode (',', $context);
$context = array ();
foreach ($objects as $object) {
	list ($class, $id) = explode ('-', $object);
	if (!class_exists ($class)) continue;
	if (!is_numeric ($id)) continue;
	$context[] = new $class ((int) $id);
	}
if (empty ($context)) die ();
$instance->select ('instance', $context);

$structure = $instance->get ('form_structure');
$form = new WP_CRM_Form ($structure);

if ($_POST['object']) {
	$form->action ();
	}
else
	$form->render (TRUE);
/*
list ($class, $id) = $_GET['object'] ? explode ('-', $_GET['object']) : explode ('-', $_POST['object']);
if (!class_exists ($class)) die ('Err.');
if (!is_numeric($id)) die ('Err.');

$object = new $class ((int) $id);

$structure = new WP_CRM_Form_Structure ($object);
$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if ($_POST['object']) {
	$form->action ();
	$object = new $class ((int) $id);
	echo "OK\nUPDATE:" . $object->changes ();
	}
else
	$form->render (TRUE);
*/
?>
