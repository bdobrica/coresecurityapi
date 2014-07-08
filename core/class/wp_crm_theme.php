<?php
class WP_CRM_Theme {
	public function __construct () {
		}

	public static function client_url ($url = null) {
		$url = is_null ($url) ? $_SERVER['REQUEST_URI'] : $url;
		if (($sep = strpos($url, '?')) !== FALSE)
			$url = substr ($url, 0, $sep);
		$parsed = explode ('/', trim ($url, '/'));

		return empty ($parsed) ? null : implode ('-', $parsed);
		}

	public static function parse_url ($url = null) {
		global $wp_crm_menu;

		$url = is_null ($url) ? $_SERVER['REQUEST_URI'] : $url;
		if (($sep = strpos($url, '?')) !== FALSE)
			$url = substr ($url, 0, $sep);
		$url_pieces = explode ('/', trim ($url, '/'));

		$search = array ();
		foreach ($wp_crm_menu->get () as $app)
			$search[$app->get ('slug')] = $app->get ('title');

		$parsed = array ();
		foreach ($url_pieces as $url_order => $url_piece) {
			if (!empty ($search) && in_array ($url_piece, array_keys ($search))) {
				$parsed[] = $url_piece;
				$search = is_array ($search[$url_piece]) ? $search[$url_piece] : array ();
				}
			}

		if (!empty ($parsed))
			$_GET['filter'] = implode (':', array_diff ((array) $url_pieces, (array) $parsed));

		return empty ($parsed) ? null : implode ('-', $parsed);
		}

	public static function init () {
		$url = get_bloginfo ('stylesheet_directory');
		
		wp_enqueue_style ('tiny-editor', $url . '/script/tinyeditor/tinyeditor.css', array (), '0.1');

		wp_enqueue_script ('jquery');
		wp_enqueue_script ('jquery-filedrop', $url . '/script/jquery.filedrop.js', array ('jquery'), '0.1.0');
		wp_enqueue_script ('tiny-editor', $url . '/script/tinyeditor/tiny.editor.packed.js', array (), '0.1');

		/*
		wp_enqueue_style ('tiny-editor', $url . '/script/tinyeditor/tinyeditor.css', array (), '0.1');
		//wp_enqueue_style ('jquery-ui-metro', $url . '/script/jMetro/css/jquery-ui.css', array (), '0.24');

		wp_enqueue_style ('twitter-bootstrap', $url . '/ui/bootstrap/css/bootstrap.css', array (), '0.1');
		wp_enqueue_style ('twitter-prettify', $url . '/ui/bootstrap/css/prettify.css', array (), '0.1');
		wp_enqueue_style ('flat-ui', $url . '/ui/css/flat-ui.css', array (), '0.1');

		wp_enqueue_script ('jquery');
		//wp_enqueue_script ('jquery-ui-metro', $url . '/script/jMetro/js/jquery-ui.1.10.1.min.js', array ('jquery'), '1.10.1');
		wp_enqueue_script ('jquery-filedrop', $url . '/script/jquery.filedrop.js', array ('jquery'), '0.1.0');
		wp_enqueue_script ('tiny-editor', $url . '/script/tinyeditor/tiny.editor.packed.js', array (), '0.1');

		wp_enqueue_script ('jquery-ui-flat', $url . '/ui/js/jquery-ui-1.10.3.custom.min.js', array ('jquery'), '0.1');
		wp_enqueue_script ('jquery-ui-touch', $url . '/ui/js/jquery.ui.touch-punch.min.js', array (), '0.1');

		wp_enqueue_script ('twitter-bootstrap', $url . '/ui/js/bootstrap.min.js', array (), '0.1');
		wp_enqueue_script ('twitter-bootstrap-select', $url . '/ui/js/bootstrap-select.js', array (), '0.1');
		wp_enqueue_script ('twitter-bootstrap-switch', $url . '/ui/js/bootstrap-switch.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-checkbox', $url . '/ui/js/flatui-checkbox.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-radio', $url . '/ui/js/flatui-radio.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-tagsinput', $url . '/ui/js/jquery.tagsinput.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-placeholder', $url . '/ui/js/jquery.placeholder.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-stackable', $url . '/ui/js/jquery.stackable.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-application', $url . '/ui/js/application.js', array (), '0.1');
		wp_enqueue_script ('twitter-booster-prettify', $url . '/ui/js/bootstrap/js/google-code-prettify/prettify.js', array (), '0.1');
		*/

	
		wp_enqueue_script ('twitter-bootstrap-select', $url . '/assets/js/bootstrap-select.js', array (), '0.1');
		wp_enqueue_script ('twitter-bootstrap-switch', $url . '/assets/js/bootstrap-switch.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-checkbox', $url . '/assets/js/flatui-checkbox.js', array (), '0.1');
		wp_enqueue_script ('jquery-ui-radio', $url . '/assets/js/flatui-radio.js', array (), '0.1');

		wp_enqueue_script ('wp-crm-jquery-migrate', $url . '/assets/js/jquery-migrate-1.2.1.min.js');
		wp_enqueue_script ('wp-crm-bootstrap', $url . '/assets/js/bootstrap.min.js');
		wp_enqueue_script ('wp-crm-icheck', $url . '/assets/js/jquery.icheck.min.js');
		wp_enqueue_script ('wp-crm-jquery-ui', $url . '/assets/js/jquery-ui-1.10.3.custom.min.js');
		wp_enqueue_script ('wp-crm-jquery-touch-punch', $url . '/assets/js/jquery.ui.touch-punch.min.js');
		wp_enqueue_script ('wp-crm-jquery-sparkline', $url . '/assets/js/jquery.sparkline.min.js');
		wp_enqueue_script ('wp-crm-fullcalendar', $url . '/assets/js/fullcalendar.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot', $url . '/assets/js/jquery.flot.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-pie', $url . '/assets/js/jquery.flot.pie.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-stack', $url . '/assets/js/jquery.flot.stack.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-resize', $url . '/assets/js/jquery.flot.resize.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-time', $url . '/assets/js/jquery.flot.time.min.js');
		wp_enqueue_script ('wp-crm-jquery-autosize', $url . '/assets/js/jquery.autosize.min.js');
		wp_enqueue_script ('wp-crm-jquery-placeholdeer', $url . '/assets/js/jquery.placeholder.min.js');
		wp_enqueue_script ('wp-crm-jquery-chosen', $url . '/assets/js/jquery.chosen.min.js');
		wp_enqueue_script ('wp-crm-moment', $url . '/assets/js/moment.min.js');
		wp_enqueue_script ('wp-crm-daterangepicker', $url . '/assets/js/daterangepicker.min.js');
		//wp_enqueue_script ('wp-crm-jquery-easy-pie-chart', $url . '/assets/js/jquery.easy-pie-chart.min.js');
		wp_enqueue_script ('wp-crm-jquery-datatables', $url . '/assets/js/jquery.dataTables.min.js');
		wp_enqueue_script ('wp-crm-datatables-bootstrap', $url . '/assets/js/dataTables.bootstrap.min.js');
		wp_enqueue_script ('wp-crm-custom', $url . '/assets/js/custom.min.js');
		wp_enqueue_script ('wp-crm-core', $url . '/assets/js/core.min.js');
		//wp_enqueue_script ('wp-crm-arbor', $url . '/assets/js/arbor.js');

		wp_enqueue_script ('google-jsapi', 'https://www.google.com/jsapi');

		wp_enqueue_script ('wp-crm', $url . '/script/wp-crm.js', array ('jquery'), '0.5');
		}

	public static function head () {
		}

	public function __destruct () {
		}
	}
?>
