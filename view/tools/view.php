<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

if (isset ($_GET['grp'])) {
	$group = new WP_CRM_Group ($_GET['grp']);
	$pdf = new PDF ();
	$out = $group->gview (FALSE, $pdf);
	$out['pdf']->out ('invoices.pdf');
	}
else
if (isset ($_GET['inv'])) {
	$invoice = new WP_CRM_Invoice ((int) $_GET['inv']);
	$invoice->view (TRUE, null, TRUE);
	}
?>
