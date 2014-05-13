<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-blog-header.php');
include (dirname(__FILE__).'/card/mobilpay.php');

spl_autoload_register (function ($class) {
	include (dirname(dirname(dirname(__FILE__))) . '/libs/class/' . strtolower($class) . '.php');
	});

global $wp_crm_state;

$wp_crm_state = new WP_CRM_State ();
if ($_GET['p']) $wp_crm_state->buy ($_GET['p'], 1);

$structure = new WP_CRM_Form_Structure ($wp_crm_state->get());
$form = new WP_CRM_Form ($structure);

$form->action();

if (!empty($_POST)) {
		unset ($structure);

		$wp_crm_state->set ('state', $form->get ('state'));
		$wp_crm_state->set ($form->get ('state'), $form->get ('payload'));
		$structure = new WP_CRM_Form_Structure ($wp_crm_state->get());
		$form->set ('structure', $structure);

	$form->render (TRUE);
	$errors = $form->get('errors');
	exit (0);
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Conforming XHTML 1.0 Strict Template</title>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="http://www.traininguri.ro/wp-content/plugins/wp-crm/ajax/shop/remote/api.js"></script>

		<link rel="stylesheet" type="text/css" href="http://www.traininguri.ro/wp-content/plugins/wp-crm/ajax/shop/remote/api.css" />
	</head>
	<body>
		<div class="wp-crm-form-body">
		<?php $form->render (TRUE); ?>
		</div>
		<div class="wp-crm-form-shadow"></div>
	</body>
</html>
