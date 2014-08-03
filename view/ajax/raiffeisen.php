<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

$list = new WP_CRM_List ('WP_CRM_Person', array (
	'card>0',
	'last_name!=\'\'',
	'flags>16'
	));
$participants = $list->get ();

$out = array ();
$out[] = implode (';', array (
	'Prenume',
	'Nume',
	'Companie',
	'Functie',
	'E-Mail',
	'Telefon'
	));

function fixstr ($string) {
	if (trim($string) == '-') return '';
	if (trim($string) == '') return '';
	return mb_strtoupper (mb_convert_encoding ($string, 'UTF-8'));
	}

if (!empty ($participants))
foreach ($participants as $participant) {
	$out[] = implode (';', array (
		'Prenume'	=> '"' . fixstr(stripslashes($participant->get ('first_name')))	. '"',
		'Nume'		=> '"' . fixstr(stripslashes($participant->get ('last_name')))	. '"',
		'Companie'	=> '"' . fixstr(stripslashes($participant->get ('company')))	. '"',
		'Functie'	=> '"' . fixstr(stripslashes($participant->get ('position')))	. '"',
		'E-Mail'	=> '"' . fixstr(stripslashes($participant->get ('email')))	. '"',
		'Telefon'	=> '"' . fixstr(stripslashes($participant->get ('phone')))	. '"'
		));
	};

echo implode ("\n", $out);
#echo serialize($out);
?>
