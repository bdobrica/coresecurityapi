<?php
define ('WP_USE_THEMES', false);
define (CSV_CELL_SEP, ';');
define (CSV_TEXT_SEP, '"');
define (CSV_ROWS_SEP, "\n");

include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

$out = '';

$list = new WP_CRM_List ('WP_CRM_Person', array (
	'card>0',
	'last_name!=\'\''
	));

$participants = $list->get ();

foreach ($participants as $participant) {
	$out[] = implode (CSV_CELL_SEP, array (
		CSV_TEXT_SEP . stripslashes (strtoupper ($participant->get ('email'))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . stripslashes (strtoupper ($participant->get ('first_name'))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . stripslashes (strtoupper ($participant->get ('last_name'))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . stripslashes (strtoupper ($participant->get ('company'))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . stripslashes (strtoupper ($participant->get ('package'))) . CSV_TEXT_SEP
		));
	}

$list = new WP_CRM_List ('WP_CRM_Invoice', array (
	'uid=1',
	'value>0'
	));

$invoices = $list->get ();

foreach ($invoices as $invoice) {
	if (strpos ($invoice->buyer->get ('email'), '@') === FALSE) continue;
	if ($invoice->get ('buyer') == 'company') {
		$out[] = implode (CSV_CELL_SEP, array (
			CSV_TEXT_SEP . stripslashes (strtoupper ($invoice->buyer->get ('email'))) . CSV_TEXT_SEP,
			CSV_TEXT_SEP . '' . CSV_TEXT_SEP,
			CSV_TEXT_SEP . '' . CSV_TEXT_SEP,
			CSV_TEXT_SEP . stripslashes (strtoupper ($invoice->buyer->get ('name'))) . CSV_TEXT_SEP,
			CSV_TEXT_SEP . 'HR' . CSV_TEXT_SEP
			));
		}
	else {
		$out[] = implode (CSV_CELL_SEP, array (
			CSV_TEXT_SEP . stripslashes (strtoupper ($invoice->buyer->get ('email'))) . CSV_TEXT_SEP,
			CSV_TEXT_SEP . stripslashes (strtoupper ($invoice->buyer->get ('first_name'))) . CSV_TEXT_SEP,
			CSV_TEXT_SEP . stripslashes (strtoupper ($invoice->buyer->get ('last_name'))) . CSV_TEXT_SEP,
			CSV_TEXT_SEP . '' . CSV_TEXT_SEP,
			CSV_TEXT_SEP . 'HR' . CSV_TEXT_SEP
			));
		}
	}

echo implode (CSV_ROWS_SEP, $out);
?>
