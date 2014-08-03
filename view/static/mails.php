<?php
/*
App Title: Conturi Email
App Description:
App Size: 1
App Style:
App Icon: envelope-o 
*/
$mails = new WP_CRM_List ('WP_CRM_Mail');
$view = new WP_CRM_View ($mails, array (
		'add' => 'Adauga',
		'edit' => 'Modifica',
		'delete' => 'Sterge'
		));
unset ($view);
?>
