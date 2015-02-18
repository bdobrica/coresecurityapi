<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

$data = $_GET['object'] ? $_GET['object'] : $_POST['object'];
$key = $_GET['key'] ? $_GET['key'] : $_POST['key'];
$value = urldecode ($_GET['value'] ? $_GET['value'] : $_POST['value']);

list ($data_o, $filter) = explode (';', $data);
list ($class, $id) = explode ('-', $data_o);

try {
	$object = new $class ((int) $id);
	}
catch (WP_CRM_Exception $wp_crm_exception) {
	$object = null;
	echo 'ERROR';
	exit (1);
	}

if (!is_null ($object)) echo $object->set ($key, $value) ? 'OK' : 'ERROR';
?>
