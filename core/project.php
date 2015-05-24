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

$file = fopen ('registru-proiecte-einvest.csv', 'r');
$skip = TRUE;
while ($line = fgets ($file)) {
	if ($skip) {
		$skip = FALSE;
		continue;
		}
	$line = trim ($line);
	$line = str_getcsv ($line);

	list ($axis, ) = explode (' ', str_replace ('O ', 'O', trim($line[5])), 2);
	$axis = trim ($axis, '.');

	#$company = new WP_CRM_Company (array ('uin' => $line[7]));
	#$company->save ();
	$company = new WP_CRM_Company (WP_CRM_Company::openapi (trim($line[7])));
	$company->save ();

	$project = new WP_CRM_Project ((int) $line[0]);
/*
	$project_data = array (
		'title'		=> trim($line[1]),
		'project_title'	=> trim($line[2]),
		'description'	=> trim($line[3]),
		'programme_id'	=> trim($line[4]),
		'axis_id'	=> $axis,
		'client'	=> $company->get ('self'),
		'registration'	=> '',
		'project_id'	=> trim($line[8]),
		'begin'		=> strtotime ($line[9]),
		'end'		=> strtotime ($line[10]),
		'project'	=> '',
		'budget'	=> (float) trim(str_replace(',','',$line[11])),
		'cofinancing'	=> (float) trim(str_replace(',','',$line[12])),
		'state'		=> trim($line[13])
		);

	$project = new WP_CRM_Project ($project_data);
	$project->save ();
*/
	$project->set ('client', $company->get ('self'));
	}
fclose ($file);
?>
