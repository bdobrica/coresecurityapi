<?php
/*
Action Title: Newsletters
Action Description: Sends Newsletters
Action Events: timer
Action Objects: *
Action Filter:
*/

function wp_crm_action_newsletters ($data = null) {
	$newsletters = new WP_CRM_List ('WP_CRM_Contact', array ('status=\'queued\''));
	if ($newsletters->is ('empty')) return TRUE;
	foreach ($newsletters->get () as $newsletter) {
		if ($newsletter->get ('stamp') < $data)
			$newsletter->send ();
		}
	}
?>
