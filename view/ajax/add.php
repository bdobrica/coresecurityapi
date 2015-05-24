<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

global
	$wp_crm_buyer,
	$wp_crm_state;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();
$wp_crm_state->set ('state', WP_CRM_State::AddObject);

$data = $_GET['object'] ? $_GET['object'] : $_POST['object'];
$context = $_GET['context'] ? $_GET['context'] : $_POST['context'];

list ($data_o, $filter) = explode (';', $data);
list ($class, $id) = explode ('-', $data_o);

$append = array ();
if (strpos ($filter, '=') !== FALSE) {
	$filter = explode (' and ', urldecode ($filter));
	foreach ($filter as $keyval) {
		if ($keyval == 1) {
			/* "1 and " in filters has no effect. thus, if 1 is pressent as a filter atom,
			the corresponding filtering context is discarded when adding a new object. */
			$append = array ();
			break;
			}
		if (strpos ($keyval, '=') === FALSE) continue;
		list ($key, $val) = explode ('=', $keyval);
		$append[$key] = $val;
		}
	}

if (!class_exists ($class)) die ('Err.');
if (!is_numeric($id)) die ('Err.');

if (!empty($context)) $context = (array) json_decode (trim (stripslashes (implode ('', explode ('\\', $context)))));

$object = new $class ();

if (!empty ($context)) $object->set ($context);


$structure = new WP_CRM_Form_Structure ($object);
if (!empty ($append)) 
	$structure->append ($append);

$form = new WP_CRM_Form ($structure);
$form->set ('state', $wp_crm_state->get());

if ($_POST['object']) {
	$action = $form->action ();
	if (!$action['error']) {
		echo "OK\nADD:" . $action['return']['object']->json ('view');
		exit (0);
		}
	}

$form->render (TRUE);
?>
