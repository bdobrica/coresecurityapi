<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php');
include (dirname(dirname(__FILE__)) . '/common.php');

if (!empty($_POST)) {
	$object = new WP_CRM_Person ($_POST);
	$object->save ();
	echo $object->json ();
	}
else {
	$list = new WP_CRM_List ('WP_CRM_Person', array ('uin!=\'\''));
	echo $list->get ('json');
	}
?>
