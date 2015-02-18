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


/*include ('accounts.php');

ksort ($accounts);
$parents = array ();

foreach ($accounts as $reference => $name) {
	$func = 'U';
	if (strpos ($name, '(A)') !== FALSE) $func = 'A';
	if (strpos ($name, '(P)') !== FALSE) $func = 'P';
	if (strpos ($name, '(A/P)') !== FALSE) $func = 'B';

	$account = new WP_CRM_Account (array (
		'reference' => (int) $reference,
		'type' => 'S',
		'func' => $func,
		'name' => trim ($name),
		'parent' => strlen ($reference) < 2 ? 0 : $parents [(int) (substr ($reference, 0, strlen($reference) - 1))]
		));
	$account->save ();
	$parents[$reference] = $account->get ();
	unset ($account);
	}
*/

#WP_CRM_Partition::install ();
#WP_CRM_Account::install ();
#WP_CRM_Currency::scan ();
#WP_CRM_File::install ();
#WP_CRM_App::upgrade();
#WP_CRM_Meta::install ();
WP_CRM_ACL::install();
WP_CRM_App::scan();
#$actions = WP_CRM_Action::scan();
#print_r ($actions);

#WP_CRM_Resource::install ();
#WP_CRM_Machine::install ();
#WP_CRM_Purchase::install ();
#WP_CRM_Newsletter::install ();
#WP_CRM_Notice::install ();
#WP_CRM_Report::install ();

#$company = new WP_CRM_Company (1);

#$contact = $company->get ('contact');
#var_dump ($contact);

#$cs = new WP_CRM_Company_Structure (1);
#print_r ($cs->get ('tree'));

#WP_CRM_Course::scan();
#$course = new WP_CRM_Course (1);
#var_dump ($course->render ());

#WP_CRM_Group::scan();

#include (dirname(__FILE__).'/template/class/tbs_class.php');
?>
