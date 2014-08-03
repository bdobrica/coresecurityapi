<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

$list = new WP_CRM_List ('WP_CRM_Person', array (
	'package!=\'\'',
	'last_name!=\'\'',
	'box=\'\''
	));

$participants = $list->get ();
$all = $list->get ('size');

$letters = array ();

foreach ($participants as $participant) {
	$last_name = trim(strtoupper($participant->get ('last_name')));
	$first_letter = substr($last_name,0,1);

	if (!isset($letters[$first_letter])) $letters[$first_letter] = array ();
	$letters[$first_letter][] = $participant;
	}

function parcmp ($a, $b) {
	$la = $a->get ('last_name');
	$lb = $b->get ('last_name');
	return strcmp ($la, $lb);
	}

foreach ($letters as $letter => $participants) {
	usort ($letters[$letter], 'parcmp');
	}

$numlets = array (
	'A' => 0,
	'B' => 0,
	'C' => 0,
	'D' => 0,
	'E' => 0,
	'F' => 0,
	'G' => 0,
	'H' => 0,
	'I' => 0,
	'J' => 0,
	'K' => 0,
	'L' => 0,
	'M' => 0,
	'N' => 0,
	'O' => 0,
	'P' => 0,
	'Q' => 0,
	'R' => 0,
	'S' => 0,
	'T' => 0,
	'U' => 0,
	'V' => 0,
	'W' => 0,
	'X' => 0,
	'Y' => 0,
	'Z' => 0,
	);

$boxes = array (
	1 => array (),
	2 => array (),
	3 => array (),
	4 => array (),
	5 => array (),
	6 => array (),
	7 => array (),
	8 => array (),
	9 => array (),
	10 => array (),
	11 => array (),
	12 => array ()
	);

$olet = ord ('A'); $clet = 'A';

while (array_sum (array_values ($numlets)) < $all-1) {
	#echo ($all - array_sum(array_values ($numlets))) . "%\n";
	for ($b = 1; $b < 13; $b++) {
		#echo $b . "\t" . count($boxes[$b]) . "\t" . count($letters[$clet]) . "\t" . $numlets[$clet] . "\t" . $clet . "\n";
		if (count ($boxes[$b]) > ceil ($all/12)) continue;
		if ($numlets[$clet] < count ($letters[$clet])) {
			$boxes[$b][] = $letters[$clet][$numlets[$clet]];
			$numlets[$clet]++;
			}
		}
	$olet++;
	if ($olet > ord ('Z')) $olet = ord ('A');
	$clet = chr ($olet);
	}

$out = array ();

function fixstr ($string) {
	if (trim($string) == '-') return '';
	if (trim($string) == '') return '';
	return mb_strtoupper (mb_convert_encoding ($string, 'UTF-8'));
	}

foreach ($boxes as $box => $participants) {
	usort ($participants, 'parcmp');
	foreach ($participants as $participant) {
		$participant->set ('box', $box);
		$out[] = implode (';', array (
			'Card'		=> '"' . str_pad ($participant->get ('card'), 10, '0', STR_PAD_LEFT) . '"',
			'Numar'		=> '"' . $box . '"',
			'Prenume'	=> '"' . fixstr(stripslashes($participant->get ('first_name')))	. '"',
			'Nume'		=> '"' . fixstr(stripslashes($participant->get ('last_name')))	. '"',
			'Companie'	=> '"' . fixstr(stripslashes($participant->get ('company')))	. '"',
			'Pachet'	=> '"' . fixstr(stripslashes($participant->get ('package')))	. '"'
			));
		}
	}

/*$list = new WP_CRM_List ('WP_CRM_Person', array (
	'box=\'RF\''
	));

$participants = $list->get ();
if (!empty ($participants))
foreach ($participants as $participant) {
	$out[] = implode (';', array (
		'Card'		=> '"' . str_pad ($participant->get ('card'), 10, '0', STR_PAD_LEFT) . '"',
		'Numar'		=> '"RF"',
		'Prenume'	=> '"' . fixstr(stripslashes($participant->get ('first_name')))	. '"',
		'Nume'		=> '"' . fixstr(stripslashes($participant->get ('last_name')))	. '"',
		'Companie'	=> '"' . fixstr(stripslashes($participant->get ('company')))	. '"',
		'Pachet'	=> '"' . fixstr(stripslashes($participant->get ('package')))	. '"'
		));
	};*/

echo implode ("\n", $out);
#echo serialize($out);
?>
