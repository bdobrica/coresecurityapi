<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$f = fopen (dirname(__FILE__).'/tmp.csv', 'w');

$cols = array('#', 'Name', 'First Name', 'Last Name', 'E-mail', 'UIN', 'Phone','Gender','Age','Birthday');


$list = new WP_CRM_List ('persons');

fputcsv ($f, $cols);

$c = 1;
if (!$list->is('empty'))
	foreach ($list->get() as $person) {
		if (!$person->is ('paying customer')) continue;
		fputcsv ($f, array (
			$c++,
			ucwords(strtolower($person->get ('name'))),
			ucwords(strtolower($person->get ('first_name'))),
			ucwords(strtolower($person->get ('last_name'))),
			strtolower($person->get ('email')),
			trim($person->get ('uin')),
			preg_replace('/[^+0-9]+/', '', $person->get ('phone')),
			$person->get ('gender'),
			$person->get ('age'),
			date('d-m-Y', $person->get ('birthday'))
			));
		}

fclose ($f);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"customers.csv\";" );
header("Content-Transfer-Encoding: binary"); 
readfile (dirname(__FILE__).'/tmp.csv');
?>
