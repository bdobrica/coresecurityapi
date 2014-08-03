<?php
ini_set ('display_errors', 1);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(__FILE__)))).'/wp-blog-header.php');

spl_autoload_register (function ($class) {
	$class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});

if (($d = opendir (dirname(__FILE__) . '/class/')) === FALSE) die ();
while (($n = readdir ($d)) !== FALSE) {
	if (strpos ($n, '.php') === FALSE) continue;
	if (($f = fopen (dirname(__FILE__) . '/class/' . $n, 'r')) === FALSE) continue;
	while ($l = fgets ($f)) {
		if (strpos ($l, 'class') === 0) {
			$class = substr ($l, 6, strpos ($l, ' ', 6) - 6);

			if (class_exists ($class) && method_exists ($class, 'install')) {
				call_user_func (array ($class, 'install'));
				}
			break;
			}
		}
	fclose ($f);
	}

// create role - wordpress prevents redefining roles

add_role ('wp_crm_admin', 'WP CRM Office Administrator', array (
	'wp_crm_admin' => true,
	'wp_crm_pay' => true,
	'wp_crm_work' => true
	));

add_role ('wp_crm_accountant', 'WP CRM Office Accountant', array (
	'wp_crm_pay' => true,
	'wp_crm_work' => true
	));

add_role ('wp_crm_user', 'WP CRM Office User', array (
	'wp_crm_work' => true
	));
?>
