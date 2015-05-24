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

/*
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
*/

#$hash = md5 ('orange-juice');

for ($c = 5; $c<158; $c++) {
	$wpdb->query ($wpdb->prepare ('insert into `ca_clients` values (null,%d,1,1,2,1417515324);', array ($c)));
	}

$hash = md5('1234adfbc');
echo $hash . "\n";

echo WP_CRM_Instance::hash ($hash) . "\n";

$b36 = array ();
$char = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$base = strlen ($char);

$dec = 0;
$c = 0;
while ($c < strlen ($hash)) $dec = bcadd (bcmul (16, $dec), hexdec ($hash[$c++]));
while ($dec > 0) {
	array_unshift ($b36, $char[bcmod ($dec, $base)]);
	$dec = bcdiv ($dec, $base, 0);
	}

$b36 = implode ('', $b36);

echo "$b36\n";

$hash = $b36;

$hex = array ();

$dec = 0;
$c = 0;
while ($c < strlen ($b36)) $dec = bcadd (bcmul ($base, $dec), is_numeric($hash[$c]) ? (int) $hash[$c++] : (ord($hash[$c++]) - 55));
while ($dec > 0) {
	array_unshift ($hex, $char[bcmod ($dec, 16)]);
	$dec = bcdiv ($dec, 16, 0);
	}

$hex = implode ('', $hex);

echo "$hex\n";

echo WP_CRM_Instance::hash ($hash, TRUE) . "\n";

?>
