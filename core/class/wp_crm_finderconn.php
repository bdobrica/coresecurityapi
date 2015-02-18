<?php

/**
 * Default WP_CRM_Finder connector
 *
 * @author Dmitry (dio) Levashov
 * @author Bogdan Dobrica
 **/
class WP_CRM_FinderConn {
	/**
	 * WP_CRM_Finder instance
	 *
	 * @var WP_CRM_Finder
	 **/
	protected $WP_CRM_Finder;
	
	/**
	 * Options
	 *
	 * @var aray
	 **/
	protected $options = array();
	
	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	protected $header = 'Content-Type: application/json';
	
	
	/**
	 * Constructor
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function __construct($WP_CRM_Finder, $debug=false) {
		
		$this->WP_CRM_Finder = $WP_CRM_Finder;
		if ($debug) {
			$this->header = 'Content-Type: text/html; charset=utf-8';
		}
	}
	
	/**
	 * Execute WP_CRM_Finder command and output result
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function run() {
		$isPost = $_SERVER["REQUEST_METHOD"] == 'POST';
		$src    = $_SERVER["REQUEST_METHOD"] == 'POST' ? $_POST : $_GET;
		$cmd    = isset($src['cmd']) ? $src['cmd'] : '';
		$args   = array();
		
		if (!function_exists('json_encode')) {
			$error = $this->WP_CRM_Finder->error(WP_CRM_Finder::ERROR_CONF, WP_CRM_Finder::ERROR_CONF_NO_JSON);
			$this->output(array('error' => '{"error":["'.implode('","', $error).'"]}', 'raw' => true));
		}
		
		if (!$this->WP_CRM_Finder->loaded()) {
			$this->output(array('error' => $this->WP_CRM_Finder->error(WP_CRM_Finder::ERROR_CONF, WP_CRM_Finder::ERROR_CONF_NO_VOL), 'debug' => $this->WP_CRM_Finder->mountErrors));
		}
		
		// telepat_mode: on
		if (!$cmd && $isPost) {
			$this->output(array('error' => $this->WP_CRM_Finder->error(WP_CRM_Finder::ERROR_UPLOAD, WP_CRM_Finder::ERROR_UPLOAD_TOTAL_SIZE), 'header' => 'Content-Type: text/html'));
		}
		// telepat_mode: off
		
		if (!$this->WP_CRM_Finder->commandExists($cmd)) {
			$this->output(array('error' => $this->WP_CRM_Finder->error(WP_CRM_Finder::ERROR_UNKNOWN_CMD)));
		}
		
		// collect required arguments to exec command
		foreach ($this->WP_CRM_Finder->commandArgsList($cmd) as $name => $req) {
			$arg = $name == 'FILES' 
				? $_FILES 
				: (isset($src[$name]) ? $src[$name] : '');
				
			if (!is_array($arg)) {
				$arg = trim($arg);
			}
			if ($req && (!isset($arg) || $arg === '')) {
				$this->output(array('error' => $this->WP_CRM_Finder->error(WP_CRM_Finder::ERROR_INV_PARAMS, $cmd)));
			}
			$args[$name] = $arg;
		}
	
		$args['debug'] = isset($src['debug']) ? !!$src['debug'] : false;
		
		$this->output($this->WP_CRM_Finder->exec($cmd, $args));
	}
	
	/**
	 * Output json
	 *
	 * @param  array  data to output
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function output(array $data) {
		$header = isset($data['header']) ? $data['header'] : $this->header;
		unset($data['header']);
		if ($header) {
			if (is_array($header)) {
				foreach ($header as $h) {
					header($h);
				}
			} else {
				header($header);
			}
		}
		
		if (isset($data['pointer'])) {
			rewind($data['pointer']);
			fpassthru($data['pointer']);
			if (!empty($data['volume'])) {
				$data['volume']->close($data['pointer'], $data['info']['hash']);
			}
			exit();
		} else {
			if (!empty($data['raw']) && !empty($data['error'])) {
				exit($data['error']);
			} else {
				exit(json_encode($data));
			}
		}
		
	}
	
}// END class 
