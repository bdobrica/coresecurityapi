<?php
/*
App Title: Camera
App Description:
App Size: 1
App Style: 
App Icon: bars
*/
ini_set ('display_errors', 1);
$room = new WP_CRM_Room (1);
$view = new WP_CRM_View ($room);
unset ($view);
?>
