<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

$cookie = new WP_CRM_Cookie (WP_CRM_Cookie::Referer);
if ($_GET['r'])
	$cookie->set (urldecode($_GET['r']), WP_CRM_Cookie::IfEmpty);
?>
