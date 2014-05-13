<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/fpdf.php');
include_once (dirname(dirname(dirname(__FILE__))).'/libs/class.pdf.php');

function wp_crm_csv ($data) {
	$bits = array (',','',"\n");
	if (is_string($data)) {
		return $data . $bits[2];
		}
	else
	if (is_array($data)) {
		if (is_array($data[0])) {
			$out = '';
			foreach ($data as $row) $out .= implode ($bits[0], $row) . $bits[2];
			return $out;
			}
		else {
			return implode ($bits[0], $row) . $bits[2];
			}
		}
	return null;
	}

if (!preg_match('/^[0-9,]+$/', urldecode($_GET['u']))) die ('ERROR');
$persons = explode(',', trim (urldecode($_GET['u']),','));
if (!is_array($persons)) die ('ERROR');
if (empty($persons)) die ('ERROR');

$product = new WP_CRM_Product (array (
		'series' => wp_crm_extract_series ($_GET['s']),
		'number' => wp_crm_extract_number ($_GET['s'])
		));
$rows = array ();

foreach ($persons as $person) {
	$participant = new WP_CRM_Person ($person);

	$rows[] = array (
		$participant->get('first_name'),
		$participant->get('last_name'),
		$participant->get('email'),
		);
	}

if (!empty($rows)) {
	header("Content-type: text/csv");
	header("Content-Disposition: attachment; filename=mailagent.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	echo wp_crm_csv ($rows);
	}
else {
	echo "No records found!";
	}
?>
