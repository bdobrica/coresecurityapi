#!/usr/bin/php
<?php
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Gdata_App_AuthException');
Zend_Loader::loadClass('Zend_Http_Client');

define ('GUSR', 'bdobrica@gmail.com');
define ('GPWD', 'you only live twice');

function spreadsheet2table ($spreadsheet_id, $worksheet_id, $cols = array()) {
	$out = array();

	try {
		$client = Zend_Gdata_ClientLogin::getHttpClient(GUSR, GPWD, Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME);
		}
	catch (Zend_Gdata_App_AuthException $e) {
		die ('Error:'.$e->getMessage());
		}

	$gdata = new Zend_Gdata_Spreadsheets($client);
	$cells_query = new Zend_Gdata_Spreadsheets_CellQuery();
	$cells_query->setSpreadsheetKey($spreadsheet_id);
	$cells_query->setWorksheetId($worksheet_id);
	$cells = $gdata->getCellFeed($cells_query);

	foreach ($cells->entries as $cell) {
		$cell_column = preg_replace('/[0-9]+/','', $cell->title->text);
		$cell_row = (int) preg_replace('/[A-Z]+/', '', $cell->title->text);
		if (!isset($out[$cell_row])) $out[$cell_row] = array();
		if ($cols[$cell_column])
			$out[$cell_row][$cols[$cell_column]] = $cell->title->content;
		}
	
	return $out;
	}

$out = spreadsheet2table ('tkqfxCAHfYw2H-9cNmqxoOA', 'od6', $cols = array (
	'B' => 'Date',
	'C' => 'Course',
	));

print_r($out);
?>
