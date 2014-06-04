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
	if (!preg_match ('/^[A-z0-9_-]+$/', $key))
		die ('{"error":1}');

	if ($data['error'] > 0) continue;
	$hash = sha1_file ($data['tmp_name']);
	$ext = substr ($data['name'], 1 + strpos ($data['name'], '.'));

	/**
	 * I don't know if it's a good idea to place each file in a folder
	 * named after it's key.
	 */
	/*
	if (!is_dir (WP_CONTENT_DIR . '/wp-crm/data'))
		if (!@mkdir (WP_CONTENT_DIR . '/wp-crm/data'))
			die ('{"error":1}');
	if (!is_dir (WP_CONTENT_DIR . '/wp-crm/data/' . $key))
		if (!@mkdir (WP_CONTENT_DIR . '/wp-crm/data/' . $key))
			die ('{"error":1}');
	*/

	if (!@move_uploaded_file ($data['tmp_name'], WP_CONTENT_DIR . '/wp-crm/' . $hash . '.' . $ext)) die ('{"error":1}');
	
	
	$files[] = array (
		'url' => content_url () . '/wp-crm/' . $hash . '.' . $ext,
		'name' => $data['name'] );
	}

echo json_encode ($files);
?>
