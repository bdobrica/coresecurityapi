<?php
define ('WP_USE_THEMES', false);
include (dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-blog-header.php');

global
	//$wp_crm_buyer,
	$wp_crm_state,
	$wp_crm_menu,
	$wp_crm_user,
	$current_user;

if (is_null ($wp_crm_user)) {
	try {
		$wp_crm_user = new WP_CRM_User (FALSE);
		}
	catch (WP_CRM_Exception $exception) {
		$wp_crm_user = null;
		}
	}

//$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();

/**
 * Check if the user is logged in.
 */
if (!is_user_logged_in()) {
	$static = $_GET['static'];
	switch ($static) {
		case 'signup':
			$wp_crm_state->set ('state', WP_CRM_State::SignUp);
			break;
		case 'reset':
		case 'forgot':
			$wp_crm_state->set ('state', WP_CRM_State::Forgot);
			break;
		default:
			$wp_crm_state->set ('state', WP_CRM_State::Login);
		}

	$structure = new WP_CRM_Form_Structure ($wp_crm_state->get());
	$form = new WP_CRM_Form ($structure);
	$form->set ('state', $wp_crm_state->get());

	$form->action ();

	if ($current_user->ID && !in_array ($wp_crm_state->get(), array (
                WP_CRM_State::SignUp
                ))) { $_POST = array (); }
	}

if (is_user_logged_in()) {
	//$wp_crm_buyer = new WP_CRM_Buyer ();
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

	if (!empty($_POST)) {
		$form->action();
		unset ($structure);

		$wp_crm_state->set ('data', $form->get ('payload'));
		$wp_crm_state->set ('state', $form->get ('state'));

		$structure = new WP_CRM_Form_Structure ($wp_crm_state->get ());
		$form->set ('structure', $structure);

		$form->render (TRUE);

		$wp_crm_log = new WP_CRM_Log ();
		$wp_crm_log->save ();

		exit (0);
		}
	}
/**
 * Header goes here:
 */
?><!DOCTYPE html>
<html lang="en">
	<head>
		<!-- start: Meta -->
		<meta charset="utf-8">
		<title><?php bloginfo('name'); ?></title>
		<meta name="description" content="<?php bloginfo('name'); echo ' - '; bloginfo('description'); ?>">
		<meta name="author" content="Bogdan Dobrica / Core Security Advisers">
		<meta name="keyword" content="Complete E-Commerce Solution for SMEs">
		<!-- end: Meta -->

		<link rel="stylesheet" type="text/css" href="<?php bloginfo ('stylesheet_directory'); ?>/assets/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo plugins_url ('api.css', __FILE__); ?>" />
		<style type="text/css">
		.container { width: 560px; }
		</style>

		<script src="https://api.acreditate.ro/wp-includes/js/jquery/jquery.js?ver=1.11.1"></script>
		<script src="https://api.acreditate.ro/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.2.1"></script>
		<script src="<?php bloginfo ('stylesheet_directory') ?>/assets/js/flatui-radio.js"></script>
		<script src="<?php bloginfo ('stylesheet_directory') ?>/assets/js/bootstrap.min.js"></script>
		<script src="<?php echo plugins_url ('api.js', __FILE__); ?>"></script>
	</head>

	<body>
		<div class="wp-crm-form-body">
<?php
if (!is_null ($wp_crm_user) && !in_array ($wp_crm_state->get(), array (
		WP_CRM_State::SignUp
		))) {
	$form->render (TRUE);
	/**
	 * Add things to the cart and other stuff.
	 */
?>
<?php } else { ?>
			<div class="row">
				<div id="content" class="col-sm-12 full front-page">
					<div class="container-signin">
						<section>
							<div class="row">
								<div class="col-sm-12 col-md-12">
									<div class="login-box">
										<?php $form->render (TRUE); ?>

										<a class="pull-left" href="#" rel="reset">Ai uitat parola?</a>
										<a class="pull-right" href="#" rel="signup">Inregistreaza-te!</a>
										
										<div class="clearfix"></div>				
									</div>
								</div>
							</div><!--/row-->
						</section>
					</div><!-- /container-signin -->
				</div>
			</div><!--/row-->		
<?php
		} /** endif */
/**
 * Footer goes here:
 */
?>
		</div><!--/container-->
		<div class="wp-crm-form-shadow"></div>
		<div class="tos-link-window"></div>
	</body>
</html>
