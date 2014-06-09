<?php
class WP_CRM_View {
	const WP_CRM_VIEW_EXCERPT	= 1;
	const WP_CRM_VIEW_CONTENT	= 2;
	const WP_CRM_VIEW_EXPORTS	= 4;

	private $out;
	private $class;
	private $actions;
	private $echo;

	public function __construct ($object, $actions = null, $type = null) {
		$this->out = '';
		$this->class = 'wp-crm-view';
		$this->actions = $actions;
		$this->echo = TRUE;

		$type = is_null ($type) ? self::WP_CRM_VIEW_CONTENT : $type;

		switch ((int) $type) {
			case self::WP_CRM_VIEW_CONTENT:
				if (is_array ($object)) {
					$first = $object[0];
					unset ($object[0]);
					$cols = array_values ($first);
					$keys = array_keys ($first);
					$rows = array ();
					if (!empty ($object))
						foreach ($object as $data) {
							$row = array ();
							foreach ($keys as $key)
								$row[$key] = $data[$key];
							$rows[] = $row;
							}
					$this->table ($cols, $rows);
					}
				else
				switch (get_class ($object)) {
					case 'WP_CRM_List':
						/*
						* NEW DISPLAY
						*/
						if (FALSE && current_user_can ('add_users') && (get_current_user_id() == 1)) {
							$table = $object->table ();
							$view = new WP_CRM_View ($table);
							$this->out = $view->get ();
							


							/*
							$groups = array ();

							$excerpts = array ();
							foreach ($list as $item) $excerpts[] = self::excerpt ($item, $this->class);
							

							$this->out = '<div class="' . $this->class . '-list-wrapper">' . "\n" .
									'<div class="' . $this->class . '-list-container">' . "\n";
							$this->out .= '<div class="' . $this->class . '-list-groups">' . "\n" .
									'<ul class="nav nav-list">' . "\n" . '<li>' . implode ("</li>\n<li>", $groups) . '</li>' . "\n" . '</ul>' . "\n" .
									'</div>' . "\n";
							$this->out .= '<div class="' . $this->class . '-list-excerpts">' . "\n" .
									'<ul class="nav nav-list">' . "\n" . '<li>' . implode ("</li>\n<li>", $excerpts) . '</li>' . "\n" . '</ul>' . "\n" .
									'</div>' . "\n";
							$this->out .= '<div class="' . $this->class . '-list-content"></div>' . "\n";
							$this->out .= '<div class="' . $this->class . '-list-action"></div>' . "\n";
							$this->out .= '</div>' . "\n" .
									'<div class="' . $this->class . '-separator"></div>' . "\n" .
									'</div>' . "\n";
							*/
							break;
							}
						/*
						* OLD DISPLAY
						*/
						$list = $object->get ();
						$class = $object->get ('class');
						$cols = array ();
						$rows = array ();

						if (current_user_can ('wp_crm_work') || current_user_can ('wp_crm_shop') || current_user_can ('add_users'))
							$fields = $class::$F['view'];
						else
							$fields = $class::$F['safe'];

						if (empty ($fields)) {
							echo '<p><small>Warning: Class &laquo;' . $class . '&raquo; uses obsolete $F[\'public\']. Upgrade to $F[\'view\'].</small></p>';
							$fields = $class::$F['public'];
							}
						$field_keys = array_keys ($fields);
						$cols[] = '#';
						$cols = array_merge ($cols, array_values ($fields));
						if (!empty($this->actions)) $cols[] = isset($this->actions['add']) ? self::render (array (
							'id' => $object->get ('class') . '-0',
							'filter' => $object->get ('filter'),
							'class' => $this->class,
							'value' => $this->actions['add']), 'add') : '';

						if (!empty($list)) {
							$c = 0;
							foreach ($list as $id => $item) {
								$rows[$c] = array (($c+1).'. <input type="hidden" name="object" class="' . $this->class . '-object-id" value="' . $object->get('class') . '-' . $item->get() . '" />');
								$debug = 0;
								foreach ($field_keys as $key_type) {
									if (strpos ($key_type, ':') !== FALSE) {
										list ($key, $type) = explode (':', $key_type);
										}
									else {
										$key = $key_type;
										$type = '';
										}
									$type = $type == 'buyer' ? $item->get ('buyer') : $type;
									$type = $type == 'safebuyer' ? ('safe' . $item->get ('buyer')) : $type;
									#echo $key . ' ' . $type . "\n";
									$debug ++;
									$rows[$c][] = self::render ($item->get ($key), $type, 32);
									}

								try {
									$owned = method_exists ($item, 'is') ? $item->is ('owned') : NULl;
									}
								catch (WP_CRM_Exception $wp_crm_exception) {
									$owned = NULL;
									}

								if (!empty($this->actions))
									$rows[$c][] = self::render (array (
										'id' => get_class($item) . '-' . $item->get(),
										'class' => $this->class,
										'owned' => $owned,
										'actions' => $this->actions ), 'actions');
								$c++;
								}
							}
						$this->table ($cols, $rows);
						break;
					case 'WP_CRM_App':
						$this->out = '<div class="' . $this->class . '-app ' . $this->class . '-app-size-' . $object->get('size') . '">' . $object->render () . '</div>';
						break;
					case 'WP_CRM_Menu':
						if ($object->get ('render') == WP_CRM_Menu::WP_CRM_Menu_Dashboard) {
							$this->out = '<div class="' . $this->class . '-menu-wrap">';
							$this->out .= '<div class="' . $this->class . '-menu">';
							foreach (($object->get()) as $app) {
								$wp_crm_view = new WP_CRM_View ($app);
								$this->out .= $wp_crm_view->get ();
								unset ($wp_crm_app);
								}
							$this->out .= '<div class="' . $this->class . '-separator"></div>';
							$this->out .= '</div>';
							$this->out .= '</div>';
							}
						else {
							$this->out = '<ul class="nav main-menu">';
							$list = array ();
							foreach (($object->get()) as $app) {
								if ($app->get ('pid')) {
									if (!isset ($list[$app->get ('pid')])) $list[$app->get ('pid')] = array ();
									$list[$app->get ('pid')][$app->get ('id')] = '<a class="submenu" href="/' . $app->get ('slug') . '">' .
										'<i class="fa fa-' . $app->get ('icon') . '"></i>' .
										'<span class="hidden-sm text"> ' . $app->get ('title') . '</span>' .
										'</a>';
									}
								else {
									$list[$app->get ('id')] = array ('href="/' . $app->get ('slug') . '">' .
										'<i class="fa fa-' . $app->get ('icon') . '"></i>' .
										'<span class="hidden-sm text"> ' . $app->get ('title') . '</span>');
									}
								}
							foreach ($list as $id => $submenu) {
								ksort ($submenu);
								$this->out .= '<li>';
								$first = array_shift ($submenu);
								if (!empty ($submenu))
									$this->out .= '<a class="dropdown" ' . $first . ' <span class="chevron closed"></span></a> <ul><li>' . implode ('</li><li>', $submenu) . '</li></ul>';
								else
									$this->out .= '<a ' . $first . ' </a> ';
								$this->out .= '</li>';
								}
							$this->out .= '</ul>';
							}
						break;
					case 'WP_CRM_Chart':
						$this->out = '<div id="' . $object->get('id') . '" style="width:' . $object->get('size_x') . 'px; height:' . $object->get('size_y') . 'px;"></div>';
						$this->out .= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
						$this->out .= '<script type="text/javascript">google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(function(){var data=google.visualization.arrayToDataTable(' . json_encode ($object->get()) . ');var options={};var chart = new google.visualization.ColumnChart(document.getElementById(\'' . $object->get('id') . '\'));chart.draw(data, options);});</script>';
						break;
					case 'WP_CRM_Invoice':
						$this->out = '<iframe style="width: 100%; height: 500px;" src="' . get_bloginfo('stylesheet_directory') . '/tools/view.php?inv=' . $object->get('id') . '"></iframe>';
						break;
					case 'WP_CRM_Group':
						if ($object->needs ('iframe')) {
							$this->out = '<iframe style="width: 100%; height: 500px;" src="' . get_bloginfo('stylesheet_directory') . '/tools/view.php?grp=' . $object->pack() . '"></iframe>';
							}
						else {
							$this->out = '<div class="' . $this->class . '-object-pages">' . "\n";
							$out = $object->view ();
							foreach ($out as $objectid => $objectview) {
								$this->out .= '<div class="' . $this->class . '-object-page" rel="'.$objectid.'">' . "\n";
								$this->out .= $objectview;
								$this->out .= '</div>' . "\n";
								}
							$this->out .= '<div class="' . $this->class . '-separator"></div>' . "\n";
							$this->out .= '<button class="' . $this->class . '-left-arrow"></button>';
							$this->out .= '<button class="' . $this->class . '-right-arrow"></button>' . "\n";
							$this->out .= '</div>' . "\n";
							}
						break;
					case 'WP_CRM_Room':
						$this->out = $object->draw (960, 800);
						break;
					case 'WP_CRM_Participants':
						$this->out = $object->view ($this->class);
						break;
					case 'WP_CRM_Table':
						$this->table ($object->get ('cols'), $object->get ('rows'));
						break;
					}
				break;
			default:
				$this->out = 'error?';
			}
		}

	private static function cut ($data, $length = null) {
		if (is_null ($length)) return $data;
		if (strlen ($data) < $length) return $data;
		$words = explode (' ', $data);
		$out = '';
		foreach ($words as $word) {
			if (!$out) {
				$out =  $word;
				continue;
				}
			if (strlen ($out . ' ' . $word) < $length) $out .= ' ' . $word;
			}
		return $out . ' ...';
		}

	private static function render ($data, $type = null, $length = null) {
		switch ((string) $type) {
			case 'string':
				return self::cut ($data, $length);
				break;
			case 'html':
				return '';
				break;
			case 'bool':
				return $data ? '<span class="wp-crm-view-yes">Da</span>' : '<span class="wp-crm-view-no">Nu</span>';
				break;
			case 'float':
				if ($data == 0) return '0';
				return number_format ($data, 2);
				break;
			case 'date':
				if (!$data) return 'N/A';
				return date ('d-m-Y', $data);
				break;
			case 'person':
			case 'safeperson':
				try {
					$wp_crm_person = new WP_CRM_Person ($data);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					return '';
					}
				$data = $type == 'safeperson' ? preg_replace ('/[a-z]/', '*', ucwords( strtolower( $wp_crm_person->get ('name')))) : $wp_crm_person->get ('name');
				return self::cut ($data, $length);
				break;
			case 'company':
			case 'safecompany':
				try {
					$wp_crm_company = new WP_CRM_Company ($data);
					}
				catch (WP_CRM_Exception $wp_crm_error) {
					return '';
					}
				$data = $type == 'safecompany' ? preg_replace ('/[a-z]/', '*', ucwords( strtolower( $wp_crm_company->get ('name')))) : $wp_crm_company->get ('name');
				return self::cut ($data, $length);
				break;
			case 'entity':
				if ($data == 'person') return '[P]';
				if ($data == 'company') return '[C]';
				break;
			case 'array':
				return implode (' ', $data);
				break;
			case 'products':
				$out = array ();
				if (!empty($data) && is_array($data))
					foreach ($data as $key => $val) $out[] = $val . 'x' . $key;
				return implode (', ', $out);
				break;
			case 'safeproducts':
				if (empty($data) || !is_array($data)) return '??';
				$out = 0;
				foreach ($data as $key => $val) $out += $val;
				return $out;
				break;
			case 'add':
				return '<button class="btn btn-xs btn-block btn-primary ' . $data['class'] . '-actions ' . $data['class'] . '-add" rel="' . $data['id'] . ';' . urlencode($data['filter']) . '">' . $data['value'] . '</button>';
				break;
			case 'actions':
				$out = array ();
				if (!empty($data['actions'])) {
					$c = 0;
					if (count ($data['actions']) > 2) {
						foreach ($data['actions'] as $key => $val) {
							if ($key == 'add') continue;
							$out[] = '<li><a class="' . $data['class'] . '-actions ' . $data['class']. '-' . $key . '" rel="' . $data['id'] . '" href="#">' . $val . '</a></li>' . "\n";
							$c++;
							}
						return '<div class="btn-group"><button class="btn btn-primary btn-xs">Actiuni</button><button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button><span class="dropdown-arrow dropdown-arrow-inverse"></span><ul class="dropdown-menu dropdown-inverse pull-right ' . $data['class'] . '-actions-wrap">' . "\n" . implode (' ', $out) . "\n" . '</ul></div>';
						}
					else {
						foreach ($data['actions'] as $key => $val) {
							if ($key == 'add') continue;
							$out[] = '<button class="btn btn-xs btn-block btn-' . ($data['owned'] ? 'danger' : 'primary') . ' ' . $data['class'] . '-actions ' . $data['class']. '-' . $key . '" rel="' . $data['id'] . '">' . $val . '</button></li>' . "\n";
							$c++;
							}
						return implode (' ', $out);
						}
					}
				return '';
				break;
			case 'referer':
				$url = parse_url ($data);
				return '<a href="' . $data . '" target="_blank" title="' . $data . '">' . (isset ($url['host']) ? $url['host'] : '') . '</a>';
				break;
			case 'series':
				$series = WP_CRM_Model::parse ('series', $data);
				$number = WP_CRM_Model::parse ('number', $data);
				return '<span class="object-series' . (strpos ($series, 'P') === 0 ? ' object-meta-series' : '') . '">' . $series . '</span><span class="object-number">' . $number . '</span>';
				break;
			case 'invoice':
				try {
					$invoice = new WP_CRM_Invoice ((int) $data);
					return $invoice->get ('series');
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					return '';
					}
				break;
			case 'product':
				try {
					$product = new WP_CRM_Product ((int) $data);
					return $product->get ('code');
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					return '';
					}
				break;
			case 'vars':
				return nl2br (print_r (unserialize ($data), TRUE));
				break;
			case '%':
				if ($data[0] == '+') {
					$class = ' wp-crm-real-invoice';
					$data = substr($data, 1);
					}
				if (!isset ($class)) $class = '';
				$green = floor (2.55 * ((float) $data));
				$green = $green > 255 ? 255 : ($green < 0 ? 0 : $green);
				$red = 255 - $green;
				return '<div class="wp-crm-percent' . $class . '"><div class="wp-crm-percent-bar" style="width: ' . $data . '; background-color: rgb(' . $red . ',' . $green . ',0);"></div><div class="wp-crm-percent-data">' . $data . '</div></div>';
				break;
			case 'seat':
				$wp_crm_metro = new WP_CRM_Metro ();

				$out = '';
				$all = (int) $data['all'];
				for ($c = 0; $c<$data['all']; $c++) {
					$color = $wp_crm_metro->color (null, WP_CRM_Metro::Screen);
					if ($c < (int) $data['registered']) $color = $wp_crm_metro->color (null, WP_CRM_Metro::Screen);
					if ($c < (int) $data['sent']) $color = '#ffcc00';
					if ($c < (int) $data['checked']) $color = '#00cc00';
					$out .= '<div class="wp-crm-seat" style="background-color: ' . $color . ';"></div>';
					}
				return '<div class="wp-crm-seats">' . $out . '</div>';
				break;
			}
		return $data;
		}

	private static function td ($html, $class = null) {
		return "\t\t\t" . '<td' . (is_null($class) ? '' : (' class="' . $class . '"')) . '>' . $html . '</td>' . "\n";
		}

	private static function th ($html, $class = null) {
		return "\t\t\t" . '<th' . (is_null($class) ? '' : (' class="' . $class . '"')) . '>' . $html . '</th>' . "\n";
		}

	private static function tr ($html, $class = null) {
		return "\t\t" . '<tr' . (is_null($class) ? '' : (' class="' . $class . '"')) . '>' . "\n" . $html . "\t" . '</tr>' . "\n";
		}

	private static function thw ($html, $class = null) {
		return "\t" . '<thead' . (is_null($class) ? '' : (' class="' . $class . '"')) . '>' . "\n" . $html . "\t" . '</thead>' . "\n";
		}

	private static function thb ($html, $class = null) {
		return "\t" . '<tbody' . (is_null($class) ? '' : (' class="' . $class . '"')) . '>' . "\n" . $html . "\t" . '</tbody>' . "\n";
		}

	private static function tw ($html, $class = null) {
		return '<table class="table table-striped table-bordered ' . (is_null($class) ? '' : ( ' ' . $class )) . '">' . "\n" . $html . '</table>' . "\n";
		}

	private function table ($cols, $rows, $opts = null) {
		if (!empty ($cols)) {
			$out_head = '';
			foreach ($cols as $pos => $col)
				$out_head .= WP_CRM_View::th ($col . ($pos+1<count($cols) ? ' <img src="' . get_bloginfo ('stylesheet_directory') . '/images/up-arrow.png" alt="" title="" />' : ''), $this->class . '-table-head' . ($pos+1 == count($cols) ? (' ' . $this->class . '-last') : ''));
			$out_head = WP_CRM_View::tr ($out_head);
			$out_head = WP_CRM_View::thw ($out_head);
			}
		if (!empty ($rows)) {
			$out_body = '';
			$c = 0; 
			foreach ($rows as $row) {
				if (!empty ($row)) {
					$out_row = '';
					foreach ($row as $pos => $col)
						$out_row .= WP_CRM_View::td ($col, $this->class . '-table-row ' . $this->class . ($c%2 ? '-table-even' : '-table-odd') . ($pos+1 == count($row) ? (' ' . $this->class . '-last') : '') . ($c+1 == count($rows) ? (' ' . $this->class . '-table-last') : '') . ($c == 0 ? (' ' . $this->class . '-table-first') : ''));
					$out_body .= WP_CRM_View::tr ($out_row);
					
					$c++;
					}
				}
			$out_body = WP_CRM_View::thb ($out_body);
			}

		$this->out .= WP_CRM_View::tw ($out_head . $out_body, $this->class . '-table');
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			default:
				$this->echo = FALSE;
				if ($opts) echo $this->out;
				return $this->out;
			}
		}

	public function set ($key = null, $value = null) {
		switch ((string) $key) {
			case 'echo':
			case 'output':
				$this->echo = (bool) $value;
				break;
			}
		}

	private static function excerpt ($object, $class = '') {
		$out = '<div class="' . $class . '-excerpt">' . "\n";
		if (!empty ($object::$F['excerpt']))
			foreach ($object::$F['excerpt'] as $field => $label) {
				$slug = $field;
				if (strpos ($field, ':') !== FALSE) list ($slug, $type) = explode (':', $field);
				$out .= '<div class="' . $class . '-' . $type . ' ' . $class . '-excerpt-field">' . ($label ? ('<label>' . $label . '</label>') : '') . '<span>' . self::render ($object->get ($slug), $type) . '</span></div>' . "\n";
				}
		$out .= '<div class="' . $class . '-inline-actions ' . $class . '-excerpt-field"><button rel="' . get_class ($object) . '-' . $object->get () . '">&raquo;</button></div>';
		$out .= '</div>' . "\n";
		return $out;
		}

	public function __destruct () {
		if ($this->echo) echo $this->out;
		}
	};
?>
