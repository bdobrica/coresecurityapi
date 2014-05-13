<?php
global
	$wp_crm_buyer,
	$wp_crm_state,
	$wp_crm_menu;

$wp_crm_buyer = new WP_CRM_Buyer ();
$wp_crm_state = new WP_CRM_State ();

if (!is_user_logged_in()) {
	$wp_crm_state->set ('state', WP_CRM_State::Login);

	$structure = new WP_CRM_Form_Structure ($wp_crm_state->get());
	$form = new WP_CRM_Form ($structure);
	$form->set ('state', $wp_crm_state->get());

	$form->action ();
	if (!empty($_POST)) { header ('Location: /'); exit (1); }
	}

$current_user = wp_get_current_user ();

get_header ();

if ($current_user->ID) {
	$wp_crm_offices = get_user_meta ($current_user->ID, '_wp_crm_offices', TRUE);
	$wp_crm_office_query = is_numeric ($wp_crm_offices) ? sprintf ('oid=%d', $wp_crm_offices) : !empty($wp_crm_offices) ? sprintf ('oid in (%s)', implode (',', $wp_crm_offices)) : '';

	$wp_crm_menu = new WP_CRM_Menu ($current_user->ID);
	
	$static = WP_CRM_Theme::parse_url ();

	include (dirname (__FILE__) . '/template/user-header.tpl');

	if (!is_null ($static) && file_exists (dirname(__FILE__) . '/static/' . $static . '.php') && (current_user_can ('wp_crm_work') || current_user_can ('add_users'))) { ?>
		<div class="wp-crm-view-data">
		<?php
			ob_flush ();
			include (dirname(__FILE__) . '/static/' . $static . '.php');
		?>
			<div class="wp-crm-view-separator"></div>
		</div>
		<?php }
	else {
		//$view = new WP_CRM_View ($wp_crm_menu);
		//unset ($view);

		include (dirname (__FILE__) . '/template/dashboard.tpl');
		}

	include (dirname (__FILE__) . '/template/user-footer.tpl');
	}
else {
	$static = WP_CRM_Theme::client_url ();

	if (!is_null ($static) && file_exists (dirname(__FILE__) . '/clients/' . $static . '.php')) { ?>
		<div class="wp-crm-view-client">
		<?php
			ob_flush ();
			include (dirname(__FILE__) . '/clients/' . $static . '.php');
		?>
			<div class="wp-crm-view-separator"></div>
		</div>
		<?php }
	else {
		include (dirname (__FILE__) . '/template/login.tpl');
/*?>
		<div class="wp-crm-form-login">
			<a href=""><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" /></a>
<?php
	$form->render (TRUE);
?>
		</div>
<?php*/
		}
	}

get_footer ();
?>
