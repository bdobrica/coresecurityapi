<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
include (dirname(__FILE__) . '/common.php');

global
	$wp_crm_buyer,
	$wp_crm_state;

#var_dump ($_GET);
#var_dump ($_POST);

$data = $_GET['object'] ? $_GET['object'] : $_POST['object'];

list ($data_o, $filter) = explode (';', $data);
list ($class, $id) = explode ('-', $data_o);

if (!class_exists ($class)) die ('{"error":1}');
if ($class != 'WP_CRM_Template') die ('{"error":1}');
if (!is_numeric($id)) die ('{"error":1}');
$id = (int) $id;

$action = $_GET['action'] ? $_GET['action'] : $_POST['action'];

switch ((string) $action) {
	case 'save':
		$template = new WP_CRM_Template ($id ? $id : null);

		$template->set (array (
			'subject' => stripslashes ($_GET['subject'] ? $_GET['subject'] : $_POST['subject']),
			'content' => stripslashes ($_GET['content'] ? $_GET['content'] : $_POST['content'])
			));

		try {
			$template->save ();
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			}

		echo json_encode ((object) array ('object' => 'WP_CRM_Template-' . $template->get (), 'subject' => $template->get ('subject')));
		exit (0);
		break;
	case 'load':
		try {
			$template = new WP_CRM_Template ($id);
			echo json_encode ((object) array (
				'subject' => $template->get ('subject'),
				'content' => $template->get ('content')
				));
			exit (0);
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1}');
			}
		break;
	case 'delete':
		$confirm = $_GET['confirm'] ? $_GET['confirm'] : $_POST['confirm'];
		if ($confirm != 'yes') die ('{"error":1}');

		try {
			$template = new WP_CRM_Template ($id);
			$template->delete ();
			}
		catch (WP_CRM_Exception $wp_crm_exception) {
			die ('{"error":1}');
			}
		die ('{"ok":1}');
		break;
	default:
		die ('{"error":1}');
	}
?>
