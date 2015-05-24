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
		foreach ($wp_crm_menu->get () as $slug => $app)
			$search[$slug] = $app->get ('title');

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

	public static function breadcrumbs ($url = null) {
		global $wp_crm_menu;

		$url = is_null ($url) ? $_SERVER['REQUEST_URI'] : $url;
		if (($sep = strpos($url, '?')) !== FALSE)
			$url = substr ($url, 0, $sep);
		$url_pieces = explode ('/', trim ($url, '/'));

		$search = array ();
		foreach ($wp_crm_menu->get () as $slug => $app)
			if (in_array ($slug, $url_pieces))
				$search[$slug] = $app->get ('title');
		
		return $search;
		}

	public static function functions () {
		remove_action ('wp_head', 'rsd_link');
		remove_action ('wp_head', 'wlwmanifest_link');
		remove_action( 'wp_head', 'wp_shortlink_wp_head');
		remove_action ('wp_head', 'wp_generator');
		
		}

	public static function init () {
		$url = get_bloginfo ('stylesheet_directory');

		wp_enqueue_style ('genius-bootstrap', $url . '/assets/css/bootstrap.min.css', array (), '0.1');
		wp_enqueue_style ('genius-style', $url . '/assets/css/style.min.css', array (), '0.1');
		wp_enqueue_style ('genius-retina', $url . '/assets/css/retina.min.css', array (), '0.1');
		wp_enqueue_style ('genius-print', $url . '/assets/css/print.css', array (), '0.1', 'print');

		#wp_enqueue_style ('wp-crm-converse', $url . '/assets/css/converse.min.css');
		wp_enqueue_style ('candy', $url . '/assets/candy/res/default.css');

		wp_enqueue_style ('wp-crm', $url . '/style.css', array (), '0.2');

		wp_enqueue_style ('roboto-font', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300italic,400,400italic,600,600italic,700,700italic&subset=latin-ext,latin');		
		wp_enqueue_style ('tiny-editor', $url . '/script/tinyeditor/tinyeditor.css', array (), '0.1');
		wp_enqueue_style ('flowplayer-skin', $url . '/script/flowplayer/skin/minimalist.css', array (), '5.5');

		wp_enqueue_script ('jquery');
		wp_enqueue_script ('filedrop-min', $url . '/script/filedrop-min.js', array (), '0.1');
		wp_enqueue_script ('tiny-editor', $url . '/script/tinyeditor/tiny.editor.js', array (), '0.1');

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
		wp_enqueue_script ('wp-crm-jquery-flot', $url . '/assets/js/jquery.flot.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-pie', $url . '/assets/js/jquery.flot.pie.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-stack', $url . '/assets/js/jquery.flot.stack.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-resize', $url . '/assets/js/jquery.flot.resize.min.js');
		wp_enqueue_script ('wp-crm-jquery-flot-time', $url . '/assets/js/jquery.flot.time.min.js');
		wp_enqueue_script ('wp-crm-jquery-autosize', $url . '/assets/js/jquery.autosize.min.js');
		wp_enqueue_script ('wp-crm-jquery-placeholdeer', $url . '/assets/js/jquery.placeholder.min.js');
		wp_enqueue_script ('wp-crm-jquery-chosen', $url . '/assets/js/jquery.chosen.min.js');
		wp_enqueue_script ('wp-crm-jquery-nestable', $url . '/assets/js/jquery.nestable.min.js');
		wp_enqueue_script ('wp-crm-moment', $url . '/assets/js/moment.min.js');
		wp_enqueue_script ('wp-crm-datepicker', $url . '/assets/js/bootstrap-datepicker.min.js');
		wp_enqueue_script ('wp-crm-timepicker', $url . '/assets/js/bootstrap-timepicker.min.js');
		wp_enqueue_script ('wp-crm-daterangepicker', $url . '/assets/js/daterangepicker.min.js');
		//wp_enqueue_script ('wp-crm-jquery-easy-pie-chart', $url . '/assets/js/jquery.easy-pie-chart.min.js');
		wp_enqueue_script ('wp-crm-fullcalendar', $url . '/assets/js/fullcalendar.min.js');

		wp_enqueue_script ('wp-crm-jquery-datatables', $url . '/assets/js/jquery.dataTables.min.js');
		wp_enqueue_script ('wp-crm-datatables-bootstrap', $url . '/assets/js/dataTables.bootstrap.min.js');
		wp_enqueue_script ('wp-crm-custom', $url . '/assets/js/custom.min.js');
		wp_enqueue_script ('wp-crm-core', $url . '/assets/js/core.min.js');
		//wp_enqueue_script ('wp-crm-arbor', $url . '/assets/js/arbor.js');
		wp_enqueue_script ('wp-crm-jquery-elfinder', $url . '/assets/js/jquery.elfinder.min.js');
		wp_enqueue_script ('wp-crm-jquery-elfinder-custom', $url . '/assets/js/jquery.elfinder.custom.js');

		wp_enqueue_script ('google-jsapi', 'https://www.google.com/jsapi');

		wp_enqueue_script ('flowplayer', $url . '/script/flowplayer/flowplayer.min.js', array ('jquery'), '5.5');

		#wp_enqueue_script ('wp-crm-converse', $url . '/assets/js/converse.nojquery.min.js');
		wp_enqueue_script ('wp-crm-candy-libs', $url . '/assets/candy/libs/libs.min.js', array ('jquery'), '1.7.1');
		wp_enqueue_script ('wp-crm-candy', $url . '/assets/candy/candy.min.js', array ('jquery'), '1.7.1');

		if (current_user_can ('manage_options')) {
			wp_enqueue_script ('jquery-iris', $url . '/assets/js/iris.min.js', array ('jquery'), '1.0.7');
			wp_enqueue_script ('wp-crm', $url . '/script/wp-crm-administrator.js', array ('jquery'), '0.7.8');
			}
		else {
			wp_enqueue_script ('jquery-iris', $url . '/assets/js/iris.min.js', array ('jquery'), '1.0.7');
			wp_enqueue_script ('wp-crm', $url . '/script/wp-crm.js', array ('jquery'), '0.7.8');
			}
		//wp_enqueue_script ('wp-crm-footer', $url . '/script/wp-crm-footer.js', array ('wp-crm'), '0.1', TRUE);
	
/*		wp_enqueue_script ('wp-crm-salsa20', $url . '/assets/xmpp/script/salsa20.js');	
		wp_enqueue_script ('wp-crm-cryptojs-core', $url . '/assets/xmpp/script/cryptojs/core.js');	
		wp_enqueue_script ('wp-crm-cryptojs-enc-base64', $url . '/assets/xmpp/script/cryptojs/enc-base64.js');	
		wp_enqueue_script ('wp-crm-cryptojs-md5', $url . '/assets/xmpp/script/cryptojs/md5.js');	
		wp_enqueue_script ('wp-crm-cryptojs-evpkdf', $url . '/assets/xmpp/script/cryptojs/evpkdf.js');	
		wp_enqueue_script ('wp-crm-cryptojs-chipher-core', $url . '/assets/xmpp/script/cryptojs/chipher-core.js');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');	
		wp_enqueue_script ('', $url . '/assets/xmpp/script/');*/
		}

	public static function post () {
		load_theme_textdomain (__CLASS__, get_template_directory() . '/languages');
		}

	public static function head () {
		}

	public static function logout () {
		setcookie ('WP_CRM_BOSH_COOKIE', '', time() - 86400);
		}

	public static function __ ($text) { return __ ($text, __CLASS__); }
	public static function _e ($text) { _e ($text, __CLASS__); }

	public function __destruct () {
		}
	}
?>
