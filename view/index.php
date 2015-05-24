<?php
global
	$wp_crm_buyer,
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

if (!is_user_logged_in()) {
	$static = WP_CRM_Theme::client_url ();

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

	if ($_GET['h']) {
		$invitation = new WP_CRM_Invitation ($_GET['h']);
		$_POST = array_merge (array (
			'first_name'	=> $invitation->get ('first_name'),
			'last_name'	=> $invitation->get ('last_name'),
			'email'		=> $invitation->get ('email'),
			'phone'		=> $invitation->get ('phone'),
			), $_POST);
		}

	$structure = new WP_CRM_Form_Structure ($wp_crm_state->get());
	$form = new WP_CRM_Form ($structure);
	$form->set ('state', $wp_crm_state->get());

	//if (($static != 'signup') || ($_GET['h']))
		$form->action ();

	if ($current_user->ID && !in_array ($wp_crm_state->get(), array (
                WP_CRM_State::SignUp
                ))) {
		if (!isset ($_COOKIE['WP_CRM_BOSH_COOKIE']) && $_POST['username'] && $_POST['password']) {
			$bosh = new XmppBosh ( $_SERVER['HTTP_HOST'], ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/http-bind/', '', false, false);
			$bosh->connect ($_POST['username'], $_POST['password']);
			setcookie ('WP_CRM_BOSH_COOKIE', json_encode ($bosh->getSessionInfo()), 0, '/');
			}
		header ('Location: /'); exit (1);
		}
	}
else {
	$current_user = wp_get_current_user ();
	$wp_crm_menu = new WP_CRM_Menu ($current_user->ID);
	
	$static = WP_CRM_Theme::parse_url ();
	}

get_header ();

if (!is_null ($wp_crm_user) && !in_array ($wp_crm_state->get(), array (
		WP_CRM_State::SignUp
		))) {
	
	$wp_crm_office_query = $wp_crm_user->get ('office_query');

	include ($wp_crm_user->get ('role_path', 'user-header.tpl'));


	if (!is_null ($static) && file_exists (dirname(__FILE__) . '/static/' . $static . '.php') && (current_user_can ('wp_crm_work') || current_user_can ('wp_crm_shop') || current_user_can ('add_users'))) { ?>
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

		include ($wp_crm_user->get ('role_path', 'dashboard.tpl'));
		}

	include ($wp_crm_user->get ('role_path', 'user-footer.tpl'));
	}
else {
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
		if ($static == 'activate') {
			$sql = $wpdb->prepare ('select ID from `' . $wpdb->prefix . 'users` where user_login=%s;', urldecode($_GET['l']));
			$user_id = $wpdb->get_var ($sql);
			if ($user_id) {
				$user = new WP_User ($user_id);
				if (strtolower ($_GET['h']) == md5 ($user_id . $user->user_email)) {
					if ($user->has_cap ('wp_crm_wakeup')) {
						$user->set_role ('wp_crm_customer');
						$awaken = TRUE;
						}
					}
				}
			else
				$awaken = FALSE;
			}
		include (dirname (__FILE__) . '/template/login.tpl');
		}
	}

get_footer ();
?>
