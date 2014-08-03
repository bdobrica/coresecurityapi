<?php
class WP_CRM_UI {
	private static $PREFIX = 'crm-';
	private static $HEADER_STYLES = array (
		'bootstrap' => 'bootstrap/css/bootstrap.css',
		'flat-ui' => 'css/flat-ui.css'
		);
	private static $HEADER_SCRIPTS = array (
		);
	private static $FOOTER_SCRIPTS = array (
		'jquery' => 'js/jquery-1.8.3.min.js',
		'jquery-ui' => 'js/jquery-ui-1.10.3.custom.min.js',
		'jquery-touch-punch' => 'js/jquery.ui.touch-punch.min.js',
		'bootstrap' => 'js/bootstrap.min.js',
		'bootstrap-select' => 'js/bootstrap-select.js',
		'bootstrap-switch' => 'js/bootstrap-switch.js',
		'flatui-checkbox' => 'js/flatui-checkbox.js',
		'flatui-radio' => 'js/flatui-radio.js',
		'jquery-tagsinput' => 'js/jquery.tagsinput.js',
		'jquery-placeholder' => 'js/jquery.placeholder.js'
		);

	public static function load_header () {
		$URL = plugins_url ('/ui/', dirname(__FILE__));
		if (!empty (self::$HEADER_STYLES))
		foreach (self::$HEADER_STYLES as $handle => $source) {
			wp_enqueue_style (self::$PREFIX . $handle, $URL . $source, array (), 0.1);
			}
		if (!empty (self::$HEADER_SCRIPTS))
		foreach (self::$HEADER_SCRIPTS as $handle => $souurce) {
			wp_enqueue_script (self::$PREFIX . $handle, $URL . $source, array (), 0.1, TRUE);
			}
		}
	public static function load_footer () {
		$URL = plugins_url ('/ui/', dirname(__FILE__));
		if (!empty (self::$FOOTER_SCRIPTS))
		foreach (self::$FOOTER_SCRIPTS as $handle => $source) {
			wp_enqueue_script (self::$PREFIX . $handle, $URL . $source, array (), 0.1, TRUE);
			}
		}
	public function __construct () {
		add_action ('wp_enqueue_scripts', array (__CLASS__, 'load_header'));
		add_action ('wp_enqueue_scripts', array (__CLASS__, 'load_footer'));
		}
	}
?>
