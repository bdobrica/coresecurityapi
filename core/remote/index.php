<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');
#include (dirname(__FILE__).'/card/mobilpay.php');

global
	$wp_crm_buyer,
	$wp_crm_state,
	$wp_crm_site,
	$wp_crm_cookie;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State (WP_CRM_State::CartActions);
$wp_crm_site  = null;
$wp_crm_cookie = new WP_CRM_Cookie (WP_CRM_Cookie::Referer);
$templates    = null;

#echo '<!-- -->';

if (isset($_GET['p']))
	$wp_crm_state->buy ($_GET['p']);

if ($_GET['u']) {
	$wp_crm_site = new WP_CRM_Site ($_GET['u']);
	$wp_crm_state->set ('site', $wp_crm_site->get ());
	echo ''; # Safari bug! :?
	}
else
if ($wp_crm_state->get ('site')) {
	$wp_crm_site = new WP_CRM_Site ($wp_crm_state->get ('site'));
	echo ''; # Safari bug! :?
	}

if (is_object($wp_crm_site))
	$templates = $wp_crm_site->get ('templates');

$structure = new WP_CRM_Form_Structure ($wp_crm_state->get());
$form = new WP_CRM_Form ($structure);

$form->action();

if (!empty($_POST)) {
	unset ($structure);

	$wp_crm_state->set ('data', $form->get ('payload'));
	$wp_crm_state->set ('state', $form->get ('state'));
	$structure = new WP_CRM_Form_Structure ($wp_crm_state->get ());
	$form->set ('structure', $structure);

	$wp_crm_log = new WP_CRM_Log ();
	$wp_crm_log->save ();

	if ($templates[$wp_crm_state->get ()]['u'])
		echo '<div class="wp-crm-form-info-head">' . ((string) (new WP_CRM_Template ($templates[$wp_crm_state->get ()]['u']))) . '</div>';
	$form->render (TRUE);
	if ($templates[$wp_crm_state->get ()]['d'])
		echo '<div class="wp-crm-form-info-foot">' . ((string) (new WP_CRM_Template ($templates[$wp_crm_state->get ()]['d']))) . '</div>';

	exit (0);
	}

$wp_crm_log = new WP_CRM_Log ();
#print_r($wp_crm_state->get ());
$wp_crm_log->save ();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Bilete de Succes &raquo;</title>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
		<script src="<?php echo plugins_url ('api.js', __FILE__); ?>?v=0.5"></script>

		<link rel="stylesheet" type="text/css" href="<?php echo plugins_url ('api.css', __FILE__); ?>?v=0.5" />
	</head>
	<body>
		<div class="wp-crm-form-body">
			<div>
				<?php
	if ($templates[$wp_crm_state->get ()]['u'])
		echo '<div class="wp-crm-form-info-head">' . ((string) (new WP_CRM_Template ($templates[$wp_crm_state->get ()]['u']))) . '</div>';
				?>
			</div>
		<?php $form->render (TRUE); ?>
			<div>
				<?php
	if ($templates[$wp_crm_state->get ()]['d'])
		echo '<div class="wp-crm-form-info-foot">' . ((string) (new WP_CRM_Template ($templates[$wp_crm_state->get ()]['d']))) . '</div>';
				?>
			</div>
		<?php unset ($wp_crm_state); ?>
		</div>
		<div class="wp-crm-form-shadow"></div>
		<div class="tos-link-window"></div>
	</body>
</html>
