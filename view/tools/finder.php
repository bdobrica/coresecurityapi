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

$opts = array(
	// 'debug' => true,
	'roots' => array(
		array(
			'driver'        => 'WPCRM',   // driver for accessing file system (REQUIRED)
			'path'          => '1',         // path to files (REQUIRED)
			'URL'           => '', // URL to files (REQUIRED)
			'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)
			'attributes' => array(
			array(
				'pattern' => '/./', //You can also set permissions for file types by adding, for example, .jpg inside pattern.
				'read'    => true,
				'write'   => true,
				'locked'  => true
			)
		)
		)
	)
);

// run WP_CRM_Finder
$connector = new WP_CRM_FinderConn(new WP_CRM_Finder($opts));
$connector->run();

