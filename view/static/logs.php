<?php
/*
App Title: System Log
App Parent: system
App Order: 4
App Description:
App Size: 1
App Style:
App Icon: exchange
*/
$list = new WP_CRM_List ('WP_CRM_Log');
if (!$list->is ('empty')) {
	$list->sort ('time', 'desc');
	$view = new WP_CRM_View ($list);
	unset ($view);
	}
?>
