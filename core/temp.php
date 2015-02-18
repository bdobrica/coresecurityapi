<?php
/**
 * Test objects method inside the WP_CRM ecosystem.
 */
ini_set ('display_errors', 1);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(__FILE__)))).'/wp-blog-header.php');
spl_autoload_register (function ($class) {
	$class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

#WP_CRM_APP::scan();

#$cs = new WP_CRM_Company_Structure (1);
#print_r ($cs->get ('tree'));

#WP_CRM_Course::scan();
#$course = new WP_CRM_Course (1);
#var_dump ($course->render ());

include (dirname(__FILE__).'/templates/class/tbs_class.php');

$tbs = new clsTinyButStrong ();
$tbs->LoadTemplate (dirname(__FILE__).'/templates/default/example.html');
$data = array (
	'row1' => array ('col1' => 1, 'col2' => 2, 'col3' => 3),
	'row2' => array ('col1' => 2, 'col2' => 4, 'col3' => 8)
	);
$tbs->MergeBlock ('a', array ('col1', 'col2', 'col3'));
$tbs->MergeBlock ('b', $data);
$tbs->Show ();
?>
