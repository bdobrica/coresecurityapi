<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

$files = array ();

if (!is_dir (WP_CRM_File::Path))
	if (!@mkdir (WP_CRM_File::Path))
		die ('{"error":1}');

$append = '';

if ($_POST['p']) {
	list ($object, $key) = explode ('/', urldecode ($_POST['p']));
	if (!class_exists ($object)) die ('{"error":1}');
	if (!$object::has_key ($key)) die ('{"error":1}');
	
	if (!is_dir (WP_CRM_File::Path . DIRECTORY_SEPARATOR . strtolower ($object)))
		if (!@mkdir (WP_CRM_File::Path . DIRECTORY_SEPARATOR . strtolower ($object)))
			die ('{"error":1}');

	if (!is_dir (WP_CRM_File::Path . DIRECTORY_SEPARATOR . strtolower ($object) . DIRECTORY_SEPARATOR . $key))
		if (!@mkdir (WP_CRM_File::Path . DIRECTORY_SEPARATOR . strtolower ($object) . DIRECTORY_SEPARATOR . $key))
			die ('{"error":1}');

	$append = strtolower ($object) . DIRECTORY_SEPARATOR . $key;
	}

if (!empty ($_FILES)) {
	foreach ($_FILES as $key => $data) {
		if (!preg_match ('/^[A-z0-9_-]+$/', $key))
			die ('{"error":1}');

		if ($data['error'] > 0) continue;

		try {
			$file = new WP_CRM_File (array (
				'append' => $append,
				'path' => $data['tmp_name'],
				'name' => $data['name']
				));
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			$file = null;
			}

		if ($file instanceof WP_CRM_File) {
			try {
				$file->save ();
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$file = null;
				}
			}

		if (!is_null ($file)) $files[] = (object) array (
			'id' => $file->get (),
			'title' => $file->get ('title'),
			'url' => $file->get ('url'),
			'type' => $file->get ('type'),
			'class' => 'WP_CRM_File'
			);
		}
	}
else
if (@$_SERVER['HTTP_X_FILE_NAME']) {
	try {
		$file = new WP_CRM_File (array (
			'path' => 'input',
			'append' => $append,
			'name' => urldecode (@$_SERVER['HTTP_X_FILE_NAME']),
			'length' => urldecode (@$_SERVER['HTTP_X_FILE_SIZE'])
			));
		}
	catch (WP_CRM_Exception $wp_crm_exception) {
		$file = null;
		}

	if ($file instanceof WP_CRM_File) {
		try {
			$file->save ();
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			$file = null;
			}
		}

	if (!is_null ($file)) $files[] = (object) array (
		'id' => $file->get (),
		'title' => $file->get ('title'),
		'url' => $file->get ('url'),
		'type' => $file->get ('type'),
		'class' => 'WP_CRM_File'
		);
	}
echo json_encode ($files);
?>
