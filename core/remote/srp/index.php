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
	case 'register':
		/* FOR TESTING ONLY! */
		if (!isset($_POST['v']) || (strpos($_POST['v'], '$') === FALSE) || (strlen($_POST['v']) < 20))
			die ('{"error":1.0}');
		try {
			$user = new WP_CRM_User ($_POST['u']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1.1}');
			}

		$user->srp ('register', array ('verifier' => $_POST['v']));
		echo '{"error":0}';
		exit (0);
		break;
	case 'challenge':
		try {
			$user = new WP_CRM_User ($_POST['u']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1}');
			}
		try {
			$answer = $user->srp ('init', array ('A' => $_POST['A']));
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1}');
			}
		echo $answer;
		exit (0);
	case 'check':
		try {
			$user = new WP_CRM_User ($_POST['u']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1}');
			}
		try {
			$answer = $user->srp ('server_check', array ('M' => $_POST['M']));
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			print_r($wp_crm_exception);
			die ('{"error":1}');
			}
		echo $answer;
		exit (0);
	case 'push':
		try {
			$user = new WP_CRM_User ($_POST['u']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1}');
			}
		try {
			$answer = json_encode (array ('error' => 0, 'd' => $user->srp ('decrypt', $_POST['d'])));
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			print_r($wp_crm_exception);
			die ('{"error":1}');
			}
		echo $answer;
		exit (0);
	case 'pull':
		try {
			$user = new WP_CRM_User ($_POST['u']);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1}');
			}
		try {
			$data = json_decode (rtrim ($user->srp ('decrypt', $_POST['d']), "\x00"));
			if ($data === NULL)
				die ('{"error":1}');
			$data = (array) $data;
			if ($data['file'] == 'echo') $blob = 'Am invins!';
			$answer = array ('error' => 0, 'd' => '');
			if ($blob) {
				$answer['d'] = $user->srp ('encrypt', $blob);
				}
			$answer = json_encode ($answer);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			print_r($wp_crm_exception);
			die ('{"error":1}');
			}
		echo $answer;
		exit (0);
	default:
		die ('{"error":1}');
	}
?>
