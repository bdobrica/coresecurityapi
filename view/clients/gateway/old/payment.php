<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include (dirname(__FILE__).'/card/mobilpay.php');
session_start();

$out = 'test';

echo $out;
?>
