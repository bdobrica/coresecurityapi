<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

$metro = new WP_CRM_Metro (1);
echo $metro->style ();
?>
