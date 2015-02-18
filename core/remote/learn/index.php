<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
if (session_id()) {
	if ($_POST['session'] && (session_id() != $_POST['session'])) {
		session_unset ();
		session_destroy ();
		session_id ($_POST['session']);
		session_start ();
		}
	}
else {
	if ($_POST['session']) session_id ($_POST['session']);
	session_start ();
	}

switch ((string) $_POST['a']) {
	case 'attend':
		break;
	case '':
		break;
	default:
		die ('{"error":1}');
	}
?>
