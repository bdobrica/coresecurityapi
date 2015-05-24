<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php');
include (dirname(dirname(__FILE__)) . '/common.php');

$data = $_GET['object'] ? $_GET['object'] : $_POST['object'];

list ($class, $id) = explode ('-', $data, 2);

if (is_numeric ($id) && ((int) $id > 0) && class_exists ($class)) {
	try {
		$object = new $class ((int) $id);
		}
	catch (WP_CRM_Exception $wp_crm_exception) {
		$object = null;
		}
	if (!is_null ($object) && method_exists ($object, 'json')) {
		echo $object->json (TRUE);
		exit (0);
		}
	}

echo json_encode (array (
	'class'	=> $class,
	'id'	=> $id,
	'error'	=> 1
	));
?>
