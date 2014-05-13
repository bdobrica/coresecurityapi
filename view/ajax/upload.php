<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

$files = array ();

if (empty($_FILES)) die ('{"error":1}');
if (!is_dir (WP_CONTENT_DIR . '/wp-crm'))
	if (!@mkdir (WP_CONTENT_DIR . '/wp-crm'))
		die ('{"error":1}');
foreach ($_FILES as $key => $data) {
	if ($data['error'] > 0) continue;
	$hash = sha1_file ($data['tmp_name']);
	$ext = substr ($data['name'], 1 + strpos ($data['name'], '.'));

	if (!@move_uploaded_file ($data['tmp_name'], WP_CONTENT_DIR . '/wp-crm/' . $hash . '.' . $ext)) die ('{"error":1}');
	
	
	$files[] = array ('url' => content_url () . '/wp-crm/' . $hash . '.' . $ext);
	}

echo json_encode ($files);
?>
