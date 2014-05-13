<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(__FILE__)))).'/wp-blog-header.php');

$invoice = new WP_CRM_Invoice ((int) $_GET['inv']);
$invoice->view (TRUE, null, TRUE);
?>
