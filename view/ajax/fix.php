<?php
exit (0);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

$invoices = new WP_CRM_List ('WP_CRM_Invoice');
foreach ($invoices->get () as $invoice) {
	echo $invoice->get ('series') . ":\t";
	$clients = new WP_CRM_List ('WP_CRM_Client', array ('iid=' . $invoice->get()));
	echo $clients->get ('size') . "\n";
	if (!$clients->is ('empty')) {
		$edit = TRUE;
		foreach ($clients->get () as $client) {
			if ($edit && ($client->get ('first_name') == $client->get ('last_name')) && ($invoice->get ('buyer') == 'company') && $invoice->get ('did')) {
				$edit = FALSE;
				$client->set ('uid', $invoice->get ('did'));
				echo "\t\t\t-> updated\n";
				}
			}
		}
	}
#$invoice = new WP_CRM_Invoice (241);
#$clients = new WP_CRM_List ('WP_CRM_Client', array ('iid=' . $invoice->get()));
#foreach ($clients->get () as $client) {
#	$client->set ('first_name', 'Bogdanel');
#	}
?>
