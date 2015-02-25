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
		/**
		 * $actions = array (
		 *	array (
		 *		'type' => 'toolbar|column',
		 *		'label' => 'Column Label',
		 *		'items' => array (
		 *			'key' => array (
		 *				'label' => 'Label',
		 *				'context' => 'WP_CRM_Model-N',
		 *				),
		 *			'key' => array (
		 *				'label' => 'Label',
		 *				'context' => 'WP_CRM_Model-N',
		 *				'items' => array (
		 *					'key' => array (
		 *						'label' => 'Label',
		 *						'context' => 'WP_CRM_Model-M',
		 *						...
		 *						),
		 *					...
		 *					),
		 *				),
		 *			...
		 *			),
		 *		),
		 *	...
		 *	);
		 */
		$this->out = '';
		$this->class = 'wp-crm-view';
		$this->actions = array ('toolbars' => array (), 'columns' => array ());

		if (!empty ($actions))
			foreach ($actions as $action_group)
				switch ($action_group['type']) {
					case 'toolbar':
						$this->actions['toolbars'][] = $action_group;
						break;
					case 'switch':
					case 'column':
						$this->actions['columns'][] = $action_group;
						break;
					}

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
					case 'WP_CRM_Tree':
						$this->out .= '<div class="' . $this->class . '-tree-wrapper">';
						$this->out .= '<button class="wp-crm-view-nodeadd" rel="WP_CRM_Task-0"><i class="fa fa-plus"></i></button>';
						$this->out .= '<div class="' . $this->class. '-tree" width="558" height="400"></div>' . "\n";
						$this->out .= '<div class="' . $this->class . '-tree-menu btn-group open"><ul class="dropdown-menu">
<li><a class="wp-crm-view-nodeedit">Editeaza</a></li>
<li><a class="wp-crm-view-nodedelete">Sterge</a></li>
<li><a class="wp-crm-view-nodelink">Leaga</a></li>
<li><a class="wp-crm-view-nodeunlink">Dezleaga</a></li>
</ul></div>';
						$this->out .= '</div>';
						$this->out .= '<script type="text/javascript">$wpcrmui.tree(\'.' . $this->class. '-tree\', ' . $object->get () . ');</script>';
						break;
					case 'WP_CRM_List':
						$list = $object->get ();
						$class = $object->get ('class');
						$cols = array ();
						$rows = array ();

						if (current_user_can ('wp_crm_work') || current_user_can ('wp_crm_shop') || current_user_can ('add_users'))
							$fields = $class::$F['view'];
						else
							$fields = $class::$F['safe'];
						/**
						 * Display a toolbar, if we have.
						 */
						if (!empty ($this->actions['toolbars'])) {
							$this->out .= self::render (array (
								'id' => $object->get ('class') . '-0',
								'filter' => $object->get ('filter'),
								'class' => $this->class,
								'toolbars' => $this->actions['toolbars']), 'toolbars');
							}
						/**
						 * No fields, nothing to display. No more errors.
						 */
						if (empty ($fields)) break;

						$field_keys = array_keys ($fields);
						$cols[] = '#';
						$cols = array_merge ($cols, array_values ($fields));

						if (!empty ($this->actions['columns']))
							foreach ($this->actions['columns'] as $actions_column) {
								$cols[] = $actions_column['label'];
								}

						if (!empty($list)) {
							$c = 0;
							foreach ($list as $id => $item) {
								$rows[$c] = array (($c+1).'. <input type="hidden" name="object" class="' . $this->class . '-object-id" value="' . $object->get('class') . '-' . $item->get() . '" />');
								foreach ($field_keys as $key_type) {
									$key = $key_type;
									if (!empty($key_type) && (strpos ($key_type, '?') !== FALSE)) list ($key_type, $cond) = explode ('?', $key_type);
									list ($key, $type) = explode (':', $key_type);
									if (!empty($type) && (strpos ($type, ';') !== FALSE)) list ($type, $opts) = explode (';', $type);

									$type = $type == 'buyer' ? $item->get ('buyer') : $type;
									$type = $type == 'safebuyer' ? ('safe' . $item->get ('buyer')) : $type;
									$rows[$c][] = '<span class="' . $this->class . '-keyname-' . $key . '">' . self::render ($item->get ($key), $type, $opts ? $item->get ($opts) : null, 32) . '</span>';
									}

								foreach ($this->actions['columns'] as $actions_column) {
									$rows[$c][] = self::render (array (
										'id' => $item->get ('self'),
										'object' => $item,
										'class' => $this->class,
										'actions' => $actions_column ), 'actions');
									}
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
							$items = array ();
							foreach (($object->get()) as $slug => $app) {
								$items[] = (object) array (
									'slug' => $slug,
									'ord' => $app->get ('ord'),
									'parent' => $app->get ('parent'),
									'app' => $app
									);
								}

							$children = array ();
							foreach ($items as $item)
								$children[$item->parent ? $item->parent : 'root'][] = $item;

							foreach (array_keys ($children) as $parent)
								WP_CRM_App::sort ($children[$parent]);

							foreach ($items as $item)
								if (isset ($children[$item->slug]))
									$item->children = $children[$item->slug];

							$tree = $children['root'];
							$this->out .= self::dropdown ($tree);

							/*
							foreach ($tree as $item) {
								$this->out .= '<li>';
								if (!empty ($item->children)) {
									$this->out .= '<a href="/' . $item->app->get ('slug') . '" class="dropmenu">' .
											'<i class="fa fa-' . $item->app->get ('icon') . '"></i>' .
											'<span class="hidden-sm text"> ' . $item->app->get ('title') . '</span>' .
											'<span class="chevron closed"></span>' .
											'</a>';
									$this->out .= '<ul>';
									foreach ($item->children as $_item) {
										$this->out .= '<li>';
										$this->out .= '<a href="/' . $_item->app->get ('slug') . '">' .
												'<i class="fa fa-' . $c_item->app->get ('icon') . '"></i>' .
												'<span class="hidden-sm text"> ' . $c_item->app->get ('title') . '</span>' .
												'</a>';
										$this->out .= '</li>';
										}
									$this->out .= '</ul>';
									}
								else {
									$this->out .= '<a href="/' . $item->app->get ('slug') . '">' .
											'<i class="fa fa-' . $item->app->get ('icon') . '"></i>' .
											'<span class="hidden-sm text"> ' . $item->app->get ('title') . '</span>' .
											'</a>';
									}
								$this->out .= '</li>';
								}
							*/

							/*


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
								}*/
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
							$out = $object->gview ();
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
					case 'WP_CRM_Folder':
						$this->out .= '<div class="' . $this->class . '-file-manager"></div>' . "\n";
						/*
						$this->out .= '<div class="' . $this->class . '-folder-wrapper">' . "\n";

						if (isset($this->actions['toolbar']) && !empty($this->actions['toolbar'])) $this->out .= self::render (array (
							'id' => 'WP_CRM_Folder-0',
							'parent' => 'WP_CRM_Folder-' . $object->get ('parent'),
							'filter' => $object->get ('filter'),
							'class' => $this->class,
							'toolbar' => $this->actions['toolbar']), 'toolbar');

						$children = $object->get ('children');

						$this->out .= '<a href="#" rel="WP_CRM_Folder-' . ($object->get('parent') ? $object->get('parent') : $object->get()) . '" class="' . $this->class . '-actions ' . $this->class . '-folder fa fa-folder">' . $object->get ('parent_title') . '</a>' . "\n";
						if ($object->get ('parent'))
							$this->out .= '<a href="#" rel="WP_CRM_Folder-' . $object->get() . '" class="' . $this->class . '-actions ' . $this->class . '-folder fa fa-folder">' . $object->get ('title') . '</a>' . "\n";
						$this->out .= '<hr />' . "\n";
						if (!$children->is ('empty'))
						foreach ($children->get () as $child) {
							$this->out .= "\t" . '<a href="#" rel="WP_CRM_Folder-' . $child->get() . '" class="' . $this->class . '-actions ' . $this->class . '-folder fa fa-folder">' . $child->get ('title') . '</a>' . "\n";
							}
						$this->out .= '</div>' . "\n";
						$this->out .= '<div class="' . $this->class . '-separator"></div>' . "\n";
						*/
						break;
					case 'WP_CRM_Course':
						$this->out = $object->render ($this->class);
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

	public static function render ($data, $type = null, $opts = null, $length = null) {
		$out = '';
		switch ((string) $type) {
			case 'string':
				$out = self::cut ($data, $length);
				break;
			case 'html':
				$out = '';
				break;
			case 'bool':
				$out = $data ? '<span class="wp-crm-view-yes">Da</span>' : '<span class="wp-crm-view-no">Nu</span>';
				break;
			case 'float':
				if ($data == 0)
					$out = '0';
				else
					$out = number_format ($data, 2);
				break;
			case 'date':
				if (!$data)
					$out = 'N/A';
				else
					$out = date ('d-m-Y', $data);
				break;
			case 'person':
			case 'safeperson':
				try {
					$wp_crm_person = new WP_CRM_Person ($data);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					break;
					}
				$data = $type == 'safeperson' ? preg_replace ('/[a-z]/', '*', ucwords( strtolower( $wp_crm_person->get ('name')))) : $wp_crm_person->get ('name');
				$out = self::cut ($data, $length);
				break;
			case 'company':
			case 'safecompany':
				try {
					$wp_crm_company = new WP_CRM_Company ($data);
					}
				catch (WP_CRM_Exception $wp_crm_error) {
					break;
					}
				$data = $type == 'safecompany' ? preg_replace ('/[a-z]/', '*', ucwords( strtolower( $wp_crm_company->get ('name')))) : $wp_crm_company->get ('name');
				$out = self::cut ($data, $length);
				break;
			case 'entity':
				if ($data == 'person') $out = '[P]';
				if ($data == 'company') $out = '[C]';
				break;
			case 'array':
				$out = $opts[$data];
				break;
			case 'products':
				$out = array ();
				if (!empty($data) && is_array($data))
					foreach ($data as $key => $val) $out[] = $val . 'x' . $key;
				$out = implode (', ', $out);
				break;
			case 'safeproducts':
				if (empty($data) || !is_array($data)) return '??';
				$out = 0;
				foreach ($data as $key => $val) $out += $val;
				break;
			case 'add':
				$out = '<button class="btn btn-xs btn-block btn-primary ' . $data['class'] . '-actions ' . $data['class'] . '-add" rel="' . $data['id'] . ';' . urlencode($data['filter']) . '">' . $data['value'] . '</button>';
				break;
			case 'toolbars':
				/**
				 * Usually, toolbar actions apply in general and / or selected items
				 */
				$out = '';
				if (!empty ($data)) {
					if (!empty ($data['toolbars'])) {
						$out .= '<div class="' . $data['class'] . '-buttons inbox">' . "\n" . '<div class="buttons">' . "\n";

						foreach ($data['toolbars'] as $toolbar) {
							if (!empty ($toolbar['items'])) {
								$action = '';
								foreach ($toolbar['items'] as $key => $button) {
									if (!empty ($button['items'])) {
										$out .= '<span class="btn-group">' . "\n";
										$out .= '<button class="btn dropdown-toggle" data-toggle="dropdown">' . $button['label'] . '<span class="caret"></span></button>' . "\n";
										$out .= '<ul class="dropdown-menu">';

										$action = '';
										foreach ($button['items'] as $_key => $_button)
											$out .= '<li><a href="#" class="' . $data['class'] . '-actions ' . $data['class'] . '-' . ($action ? : $_key) . '" rel="' . $data['id'] . ';' . urlencode($data['filter']) . ( $action ? (';' . $_key) : '' ) . '">' . $_button['label'] . '</a></li>';
										$out .= '</ul>';
										$out .= '</span>' . "\n";
										}
									else
										$out .= '<button class="' . $data['class'] . '-actions ' . $data['class'] . '-' . ($action ? : $key) . ' btn" rel="' . $data['id'] . ';' . urlencode($data['filter']) . ( $action ? (';' . $key) : '' ) . '">' . $button['label'] . '</button>' . "\n";
									}
								}
							}
						$out .= '</div>' . "\n" . '</div>' . "\n";
						}
					}
				break;
			case 'actions':
				/**
				 * Actions apply on specific items.
				 */
				$out = '';

				switch ($data['actions']['type']) {
					case 'switch':
						if (empty ($data['actions']['items'])) break;
						foreach ($data['actions']['items'] as $key => $switch) {
							$out .= '<label class="switch pull-right">' . "\n";
							$out .= '<input name="' . $key . '" class="switch-input ' . $data['class'] . '-actions ' . $data['class'] . '-' . $key . '" rel="' . $data['id'] . ':' . $switch['object']->get('self') . '" type="checkbox" ' . ($data['object']->link ($switch['object']) ? 'checked' : '') . ' ' . ($switch['unique'] ? ('data-unique="' . $data['object']->get ($switch['unique']) . '"') : '') . '/>' . "\n";
							$out .= '<span class="switch-label" data-off="Off" data-on="On"></span>' . "\n";
							$out .= '<span class="switch-handle"></span>' . "\n";
							$out .= '</label>' . "\n";
							}
						break;
					default:
						if (empty ($data['actions']['items'])) break;
						if (count ($data['actions']['items']) == 1) {
							foreach ($data['actions']['items'] as $key => $button)
								$out .= '<button class="btn btn-xs btn-block ' . $data['class'] . '-actions ' . $data['class']. '-' . $key . '" rel="' . $data['id'] . '">' . $button['label'] . '</button>' . "\n";
							break;
							}
						$group = '';
						foreach ($data['actions']['items'] as $key => $button) {
							$group .= '<li><a class="' . $data['class'] . '-actions ' . $data['class']. '-' . $key . '" rel="' . $data['id'] . '" href="#">' . $button['label'] . '</a></li>' . "\n";
							}

						$out .= '<div class="btn-group">' . "\n";
						$out .= '<button class="btn btn-primary btn-xs">' . $data['actions']['label'] . '</button>';
						$out .= '<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
						$out .= '<span class="dropdown-arrow dropdown-arrow-inverse"></span>' . "\n";
						$out .= '<ul class="dropdown-menu dropdown-inverse pull-right ' . $data['class'] . '-actions-wrap">' . "\n" . $group . "\n" . '</ul>' . "\n";
						$out .= '</div>';
					}
				break;
			case 'referer':
				$url = parse_url ($data);
				$out = '<a href="' . $data . '" target="_blank" title="' . $data . '">' . (isset ($url['host']) ? $url['host'] : '') . '</a>';
				break;
			case 'series':
				$series = WP_CRM_Model::parse ('series', $data);
				$number = WP_CRM_Model::parse ('number', $data);
				$out = '<span class="object-series' . (strpos ($series, 'P') === 0 ? ' object-meta-series' : '') . '">' . $series . '</span><span class="object-number">' . $number . '</span>';
				break;
			case 'invoice':
				try {
					$invoice = new WP_CRM_Invoice ((int) $data);
					$out = $invoice->get ('series');
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					}
				break;
			case 'product':
				try {
					$product = new WP_CRM_Product ((int) $data);
					$out = $product->get ('code');
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					}
				break;
			case 'vars':
				$out = nl2br (print_r (unserialize ($data), TRUE));
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
				$out = '<div class="wp-crm-percent' . $class . '"><div class="wp-crm-percent-bar" style="width: ' . $data . '; background-color: rgb(' . $red . ',' . $green . ',0);"></div><div class="wp-crm-percent-data">' . $data . '</div></div>';
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
				$out = '<div class="wp-crm-seats">' . $out . '</div>';
				break;
			default:
				$out = '<span class="wp-crm-dblclickable">' . $data . '</span>';
				break;
			}
		return $out;
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
		return '<table class="table table-striped table-condensed ' . (is_null($class) ? '' : ( ' ' . $class )) . '">' . "\n" . $html . '</table>' . "\n";
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
						//$out_row .= WP_CRM_View::td ($col, $this->class . '-table-row ' . $this->class . ($c%2 ? '-table-even' : '-table-odd') . ($pos+1 == count($row) ? (' ' . $this->class . '-last') : '') . ($c+1 == count($rows) ? (' ' . $this->class . '-table-last') : '') . ($c == 0 ? (' ' . $this->class . '-table-first') : ''));
						$out_row .= WP_CRM_View::td ($col, $this->class . '-table-row');
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

				if (!empty($field) && (strpos ($field, '?') !== FALSE)) list ($field, $cond) = explode ('?', $field);
				list ($key, $type) = explode (':', $field);
				if (!empty($type) && (strpos ($type, ';') !== FALSE)) list ($type, $opts) = explode (';', $type);

				$out .= '<div class="' . $class . '-' . $type . ' ' . $class . '-excerpt-field">' . ($label ? ('<label>' . $label . '</label>') : '') . '<span>' . self::render ($object->get ($slug), $type, $opts ? $object->get($opts) : null) . '</span></div>' . "\n";
				}
		$out .= '<div class="' . $class . '-inline-actions ' . $class . '-excerpt-field"><button rel="' . get_class ($object) . '-' . $object->get () . '">&raquo;</button></div>';
		$out .= '</div>' . "\n";
		return $out;
		}

	private static function dropdown ($tree) {
		$out = '';
		if (!empty ($tree))
		foreach ($tree as $item) {
			$out .= '<li>';
			if (!empty ($item->children)) {
				$out .= '<a href="/' . $item->app->get ('slug') . '" class="dropmenu">' .
						'<i class="fa fa-' . $item->app->get ('icon') . '"></i>' .
						'<span class="hidden-sm text"> ' . $item->app->get ('title') . '</span>' .
						'<span class="chevron closed"></span>' .
						'</a>';
				$out .= '<ul>';
				$out .= self::dropdown ($item->children);
				$out .= '</ul>';
				}
			else {
				$out .= '<a href="/' . $item->app->get ('slug') . '">' .
						'<i class="fa fa-' . $item->app->get ('icon') . '"></i>' .
						'<span class="hidden-sm text"> ' . $item->app->get ('title') . '</span>' .
						'</a>';
				}
			$out .= '</li>';
			}
		return $out;
		}

	public function __destruct () {
		if ($this->echo) echo $this->out;
		}
	};
?>
