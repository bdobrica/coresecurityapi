<?php
header ('HTTP/1.1 200 OK');

global
	$current_user,
	$wp_crm_offices;

$current_user = wp_get_current_user ();
if (!$current_user->ID) die ('UNAUTHORIZED: "Access is denied due to invalid credentials."');

$wp_crm_offices = get_user_meta ($current_user->ID, '_wp_crm_offices', TRUE);
$wp_crm_offices = is_numeric ($wp_crm_offices) ? array (0, $wp_crm_offices) : (is_array ($wp_crm_offices) ? array_merge (array (0), $wp_crm_offices) : array (0));

function _die ($message) { die ('<div class="clearfix" style="text-align: center;">' . $message . '</div><div class="clearfix" style="margin-top: 32px; text-align: center;"><button class="btn btn-sm btn-danger wp-crm-form-button-close"><i class="fa fa-times"></i> Inchide</button></div>'); }
?>
