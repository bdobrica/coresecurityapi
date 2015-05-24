<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php');
include (dirname(dirname(__FILE__)) . '/common.php');

$journals = new WP_CRM_List ('WP_CRM_Journal');

if ($journals->is ('empty')) {
	echo '[]';
	exit (0);
	}

$entries = array ();
foreach ($journals->get () as $journal) $entries = array_merge ($entries, $journal->get ('entries'));
echo json_encode ($entries);
exit (0);
?>
