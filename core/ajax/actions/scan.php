<?php
exit (0);
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');

$gallery = new WP_CRM_Gallery ();
$gallery->scan ('/mnt/www/traininguri.ro/traininguri.ro/wp-content/galleries/leadership-530');
?>
