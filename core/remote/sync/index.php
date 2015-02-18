<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

if (isset ($_POST['u']) && isset ($_POST['h'])) {
	$sql = $wpdb->prepare ('select ID from `' . $wpdb->prefix . 'users` where user_login=%s and user_pass=%s', array (
		$_POST['u'],
		$_POST['h']
		));
	$id = $wpdb->get_var ($sql);

	if (is_null ($id)) die ('error:1'); # 1 = invalid user or pass

	$data = array ('test' => 1);

	switch ((string) $_POST['a']) {
		case 'init':
			$servers = new WP_CRM_List ('WP_CRM_SecureServer');
			$data = array (
				'servers' => $servers->get ()
				);
			break;
		}

	$time = time ();
	$data['time'] = $time;

	$data = serialize ($data);
	}
?>
