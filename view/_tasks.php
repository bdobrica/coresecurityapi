<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>?v=0.1" />

		<style type="text/css">
<?php $metro = new WP_CRM_Metro ();

//WP_CRM_Memo::install ();

echo $metro->style (); ?>
		</style>

<?php wp_head(); ?>
	</head>

	<body>
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
	}

$current_user = wp_get_current_user ();

if ($current_user->ID) {
	$wp_crm_menu = new WP_CRM_Menu ($current_user->ID);
	
	$static = WP_CRM_Theme::parse_url ();

	if (!is_null ($static) && file_exists (dirname(__FILE__) . '/static/' . $static . '.php')) { ?>
		<div class="wp-crm-view-data">
		<?php include (dirname(__FILE__) . '/static/' . $static . '.php'); ?>
			<div class="wp-crm-view-separator"></div>
		</div>
		<?php }
	else {
		$view = new WP_CRM_View ($wp_crm_menu);
		unset ($view);
		} ?>

		<div class="wp-crm-view-bar">
			<ul class="wp-crm-view-bar-left">
				<li><a href="<?php echo home_url('/'); ?>">Prima Pagina</a></li>
				<li><a href="<?php echo home_url('/'); ?>">Grup <span></span></a>
					<ul>
						<li><a href="/" class="wp-crm-view-group-view">Detalii</a></li>
						<li><a href="/" class="wp-crm-view-group-delete">Sterge</a></li>
						<li><a href="/" class="wp-crm-view-group-selall">Selecteaza tot</a></li>
						<li><a href="/" class="wp-crm-view-group-selnone">Sterge selectia</a></li>
					</ul>
				</li>
			</ul>
			<div class="wp-crm-view-bar-right">
				<a href="<?php echo wp_logout_url('/'); ?>">Logout</a>
			</div>
		</div>

		<div class="wp-crm-side-menu">
		</div>
<?php
	}
else {
	$static = WP_CRM_Theme::client_url ();

	if (!is_null ($static) && file_exists (dirname(__FILE__) . '/clients/' . $static . '.php')) { ?>
		<div class="wp-crm-view-client">
		<?php include (dirname(__FILE__) . '/clients/' . $static . '.php'); ?>
			<div class="wp-crm-view-separator"></div>
		</div>
		<?php }
	else {
?>
<div class="wp-crm-form-login">
<?php
	$form->render (TRUE);
?>
</div>
<?php
		}
	}

wp_footer(); ?>
	</body>
</html>
