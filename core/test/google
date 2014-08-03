#!/usr/bin/php
<?php
require_once 'Zend/Loader.php';

define ('GUSR', 'bdobrica@gmail.com');
define ('GPWD', 'you only live twice');

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Gdata_App_AuthException');
Zend_Loader::loadClass('Zend_Http_Client');

try {
	$client = Zend_Gdata_ClientLogin::getHttpClient(GUSR, GPWD, Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME);
	}
catch (Zend_Gdata_App_AuthException $e) {
	exit ('Error:'.$e->getMessage());
	}

$gdata = new Zend_Gdata_Spreadsheets($client);
$spreadsheets = $gdata->getSpreadsheetFeed();

$spreadsheet_key = explode ('/', $spreadsheets->entries[0]->id->text);
$spreadsheet_key = $spreadsheet_key[5];
echo "$spreadsheet_key\n";

$worksheets_query = new Zend_Gdata_Spreadsheets_DocumentQuery();
$worksheets_query->setSpreadsheetKey($spreadsheet_key);
$worksheets = $gdata->getWorksheetFeed($worksheets_query);

$worksheet_key = explode('/', $worksheets->entries[0]->id->text);
$worksheet_key = $worksheet_key[8];
echo "$worksheet_key\n";

$cells_query = new Zend_Gdata_Spreadsheets_CellQuery();
$cells_query->setSpreadsheetKey($spreadsheet_key);
$cells_query->setWorksheetId($worksheet_key);
$cells = $gdata->getCellFeed($cells_query);

foreach ($cells->entries as $cell) {
	echo $cell->title->text . "\t" . $cell->content->text . "\n";
	}

print_r($cells);
?>
