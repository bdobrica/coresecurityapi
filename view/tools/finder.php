<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

header ('HTTP/1.1 200 OK');

global $wp_crm_user;
if (is_null ($wp_crm_user)) $wp_crm_user = new WP_CRM_User (FALSE);

$root = new WP_CRM_Folder ((int) $wp_crm_user->get ('settings', 'root_folder'));

$opts = array(
	'roots' => array(
		array(
			'driver'        => 'WPCRM',   // driver for accessing file system (REQUIRED)
			'path'          => $root->get (),         // path to files (REQUIRED)
			'URL'           => '', // URL to files (REQUIRED)
			)
		)
	);

// run WP_CRM_Finder
$connector = new WP_CRM_FinderConn(new WP_CRM_Finder($opts));
$connector->run();

