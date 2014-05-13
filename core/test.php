<?php
ini_set ('display_errors', 1);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(__FILE__)))).'/wp-blog-header.php');


spl_autoload_register (function ($class) {
	$class_file = dirname(__FILE__) . '/class/' . strtolower($class) . '.php';
	if (file_exists($class_file)) include ($class_file);
	});


WP_CRM_App::scan ();
#$actions = WP_CRM_Action::scan ();

#print_r($actions);


#print_r(unserialize('a:3:{i:0;a:2:{s:1:"u";i:26;s:1:"d";i:27;}i:1;a:2:{s:1:"u";i:28;s:1:"d";i:29;}i:2;a:1:{s:1:"u";i:30;}}'));

#print_r(serialize(array(7)));


#$user_id = 6; # vera.purdel
#$user_id = 7; # roxana.cozma
##$office_id = 1; # extreme
#$office_id = 2; # leadership 530
#update_user_meta ($user_id, '_wp_crm_offices', array (1,2));
#$var = get_user_meta ($user_id, '_wp_crm_offices', TRUE);
#$var = add_user_meta (9,'_wp_crm_offices',4,TRUE);
#var_dump ($var);

#$c = new WP_CRM_Company (408);
#$var = $c->get ('oid');
#var_dump ($var);

/*
$data = $wpdb->get_var ('select option_value from wp_options where option_name=\'wp_user_roles\';');
$data = unserialize ($data);
$data['wp_crm_admin']['capabilities'] = array (
	'wp_crm_admin' => TRUE,
	'wp_crm_pay' => TRUE,
	'wp_crm_work' => TRUE
	);
$data['wp_crm_accountant']['capabilities'] = array (
	'wp_crm_pay' => TRUE,
	'wp_crm_work' => TRUE
	);
$data['wp_crm_user']['capabilities'] = array (
	'wp_crm_work' => TRUE
	);
$data = serialize ($data);

$wpdb->query ($wpdb->prepare ('update wp_options set option_value=%s where option_name=\'wp_user_roles\';', $data));
*/

/*
$role = get_role ('wp_crm_admin');

print_r ($role);

die ();

$invoice_list = array (
	'LDR44' => 220,
	'POW23' => 10,
	'POW24' => 9,
	'POW25' => 10,
	'POW28' => 10,
	'POW29' => 10,
	'POW30' => 10,
	'POW31' => 10,
	'POW33' => 10,
	'POW19' => 10,
	'POW22' => 10,
	'POW18' => 10,
	'POW40' => 210,
	'POW44' => 20,
	'POW56' => 10,
	'POW75' => 20,
	'LDR6' => 190,
	'LDR18' => 220,
	'LDR24' => 220,
	'LDR25' => 190,
	'LDR26' => 220,
	'LDR19' => 190,
	'LDR28' => 220,
	'LDR42' => 220,
	'LDR43' => 440,
	'LDR45' => 190,
	);


foreach ($invoice_list as $invoice_series => $value) {
	$invoice = new WP_CRM_Invoice ($invoice_series);

	$storno = new WP_CRM_Invoice ();
	$storno->set ('buyer', $invoice->buyer);
	$storno->set ('sid', $invoice->get ('sid'));
	$storno->set ('did', $invoice->get ('did'));
	$storno->set ('stamp', 1375246800);
	$storno->set ('products', array (
		'new' => array (
			array (
				'price' => $value,
				'quantity' => 1,
				'vat' => 0,
				'name' => 'Rest de plata factura ' . $invoice->get ('series')
				)
			),
		'old' => array ()
		));

	$storno->save ();

	$payment = new WP_CRM_Payment ();
	$payment->set ('iid', $storno->get ('id'));
	$payment->set ('type', WP_CRM_Payment::Bank);
	$payment->set ('amount', $value);
	$payment->set ('details', 'Rest de plata factura ' . $invoice->get ('series'));
	$payment->set ('stamp', 1375246800);
	$payment->set ();

	echo $invoice->get('id') . '. ' . $invoice->get ('series') . "\n";
	}

*/
#$coupon = new WP_CRM_Coupon ('bogdanel');
#echo $coupon->discount (14,1,1373864400 - 50);

#WP_CRM_Memo::install ();

/*
$client = new SoapClient("http://www.mailagent.ro/MailAgentService.wsdl");
try {
	$response = $client->editSubscriber( "7f63fcb73d3afcf0cc8688e365fb8922", 'addslatina@yahoo.com', array('type' => 6));

	print "<pre>";
	print_r($response);
	print "</pre>";
	} catch ( SoapFault $e ) {
	die( "Exception: " . $e->getMessage() );
	}
*/

?>
