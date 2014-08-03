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

function fixstr ($string) {
	if (trim($string) == '-') return '';
	if (trim($string) == '') return '';
	return mb_strtoupper (mb_convert_encoding ($string, 'UTF-8'));
	}

$participants = $list->get ();

foreach ($participants as $participant) {
	$out[] = implode (CSV_CELL_SEP, array (
		CSV_TEXT_SEP . fixstr (stripslashes (strtoupper ($participant->get ('email')))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . fixstr (stripslashes (strtoupper ($participant->get ('first_name')))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . fixstr (stripslashes (strtoupper ($participant->get ('last_name')))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . fixstr (stripslashes (strtoupper ($participant->get ('uin')))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . fixstr (stripslashes (strtoupper ($participant->get ('company')))) . CSV_TEXT_SEP,
		CSV_TEXT_SEP . fixstr (stripslashes (strtoupper ($participant->get ('package')))) . CSV_TEXT_SEP
		));
	}


echo implode (CSV_ROWS_SEP, $out);
?>
