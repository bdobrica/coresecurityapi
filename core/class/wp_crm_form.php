<?php
class WP_CRM_Form {
	private static $GROUPTAG = 'fieldset';
	private static $ITEMSTAG = 'div';

	private $html;
	private $fields;
	private $errors;
	private $class;

	private $payload;
	private $action;
	private $state;

	public function __construct ($data = null) {
		$this->html = '';
		$this->class = 'wp-crm-form';
		$this->errors = array ();
		$this->fields = array ();

		$this->payload = array ();
		$this->state = WP_CRM_State::AddToCart;

		if (is_array ($data))
			$this->fields = $data;
		elseif (is_object ($data) && ($data instanceof WP_CRM_Form_Structure))
			$this->fields = $data->get ();
		elseif (is_string($data)) {
			$structure = new WP_CRM_Form_Structure ($data);
			$this->fields = $structure->get ();
			unset ($structure);
			}
		}

	public function set ($key = null, $value = null) {
		if ($key == 'class') $this->class = trim($value);
		if ($key == 'state') $this->state = ($value instanceof WP_CRM_State) ? $value->get () : ((int) $value);
		if ($key == 'fields' || $key == 'structure') {
			if (is_array ($data))
				$this->fields = $value;
			elseif (is_object ($value) && ($value instanceof WP_CRM_Form_Structure))
				$this->fields = $value->get ();
			elseif (is_string($value)) {
				$structure = new WP_CRM_Form_Structure ($value);
				$this->fields = $structure->get ();
				unset ($structure);
				}


			$this->errors = array ();

			if (!empty($this->fields))
			foreach ($this->fields as $label => $column) {

				$slug = preg_replace('/[^a-z]+/', '-', strtolower(trim($column['label'])));
				$apply_filters = (($column['class'] == 'tab') && ($_POST[$column['name']] != $slug)) ? FALSE : TRUE;

				if (!empty($column['fields']))
				foreach ($column['fields'] as $key => $field) {
					if (isset($field['next']) && isset($_POST[$key])) $next = $field['next'];
					$this->fields[$label]['fields'][$key]['default'] = $this->_process($key, $field['type']);
					if ($apply_filters && !empty($field['filters']))
						foreach ($field['filters'] as $filter => $err_message) {
							$error = self::filter ($key, $this->fields[$label]['fields'][$key]['default'], $field['type'], $filter) ? $err_message : null;
							if ($error)
								$this->errors[] = $this->fields[$label]['fields'][$key]['error'] = $error;
							}
					}
				}
			}
		}

	public function get ($key = null, $options = null) {
		switch ((string) $key) {
			case 'errors':
				return $this->errors;
				break;
			case 'state':
				return $this->state;
				break;
			case 'payload':
				return $this->payload;
				break;
			}
		return null;
		}

	private static function _request ($key, $data = null) {
		$val = isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : null);
		if (is_null($val) && isset($data[$key]) && (!empty($data[$key])))
			$val = $data[$key];

		return $val;
		}

	private function _process ($key, $type = null) {
		global
			$wp_crm_state,
			$wp_crm_user;

		$data = $wp_crm_state->get ('data');

		$val = self::_request ($key, $data);

		switch ($type) {
			case 'basket':
				$val = explode ('+', trim($val));
				if ($_POST && !empty($val) && $val[0]) {
					$basket = new WP_CRM_Basket ();
					foreach ($val as $pair) {
						if (empty($pair)) continue;
						$pair = explode ('-', trim($pair));
						if ($pair[0])
							$basket->add ($pair[0], $pair[1], TRUE);
						}
					$wp_crm_state->set ('basket', $basket);
					}
				else
					$basket = $wp_crm_state->get ('basket');
				$out = $basket;
				break;
			case 'date':
				$out = is_numeric ($val) ? ((int) $val) : ((int) strtotime($val));
				break;
			case 'datetime':
				$out = strtotime (self::_request ($key . '-date') . ' ' . self::_request ($key . '-time'));
				break;
			case 'color':
				$out = str_replace ('#', '', strtoupper ($val));
				break;
			case 'seller':
				try {
					$out = new WP_CRM_Company ((int) $val);
					}
				catch (WP_CRM_Exception $wp_crm_exception) {
					$out = null;
					}
				$out = is_null ($out) ? null : $out->get ();
				break;
			case 'buyer':
				list ($type, $id) = explode ('-', self::_request ($key . '-id'));
				/**
				 * TODO: check if type is WP_CRM_Person or WP_CRM_Company or else
				 */
				$out = $type == 'WP_CRM_Person' ? new WP_CRM_Person ((int) $id) : new WP_CRM_Company ((int) $id);
				break;
			case 'payment':
				$out = isset ($data['callback']) && isset ($data['callback']['invoice_ids']) ? $data['callback']['invoice_ids'] : NULL;
				break;
			case 'client':
				if (self::_request ($key . '-type') == 'person') {
					$object = $wp_crm_user->get ('person');
					if (self::_request ($key . '-p-update')) {
						$object->set (array (
							'last_name'	=> self::_request ($key . '-last-name'),
							'first_name'	=> self::_request ($key . '-first-name'),
							'uin'		=> self::_request ($key . '-p-uin'),
							'email'		=> self::_request ($key . '-p-email'),
							'phone'		=> self::_request ($key . '-p-phone')
							));
						}
					}
				if (self::_request ($key . '-type') == 'company') {
					if (($id = self::_request ($key . '-company')) > 0) {
						$object = new WP_CRM_Company ((int) $id);
						if (self::_request ($key . '-c-update')) {
							$object->set (array (
								'name'	=> self::_request ($key . '-name'),
								'uin'	=> self::_request ($key . '-uin'),
								'rc'	=> self::_request ($key . '-rc'),
								'email' => self::_request ($key . '-c-email'),
								'phone' => self::_request ($key . '-c-phone')
								));
							}
						}
					if ($id < 0) {
						$object = new WP_CRM_Company (array (
							'name'	=> self::_request ($key . '-name'),
							'uin'	=> self::_request ($key . '-uin'),
							'rc'	=> self::_request ($key . '-rc'),
							'email' => self::_request ($key . '-c-email'),
							'phone' => self::_request ($key . '-c-phone')
							));
						$object->save ();
						}
					}
				$out = is_object ($object) ? $object->get ('self') : '';
				break;
			case 'file':
				$out = json_decode (stripslashes ($val));
				$out = is_null ($out) ? $val : $out;
				if (!$out)
					break;
				$out = (array) $out[0];
				$out = new $out['class'] ($out['id']);
				break;
			case 'attachment':
				$attachments = self::_request ($key);
				$out = '';
				if (!empty($attachments)) {
					$ids = array ();
					foreach ($attachments as $attachment) {
						list ($class, $id) = explode ('-', $attachment);
						$ids[] = $id;
						}
					$out = $class . '-' . implode (',', $ids);
					}
				break;
			case 'product':
				$out = array ('old' => array (), 'new' => array ());
				foreach ($_POST as $key => $val) {
					if (strpos($key, 'quantity_n_') !== FALSE) {
						$id = (int) str_replace ('quantity_n_', '', $key);
						$out['new'][] = array (
							'price' => (float) $_POST['price_n_' . $id],
							'vat' => (float) $_POST['vat_n_' . $id],
							'quantity' => (int) $val,
							'name' => trim($_POST['name_n_' . $id])
							);
						}
					else
					if (strpos($key, 'quantity_') !== FALSE) {
						$id = (int) str_replace ('quantity_', '', $key);
						$out['old'][] = array (
							'id' => (int) $id,
							'quantity' => (int) $val
							);
						}
					}
				break;
			case 'matrix':
				$_out = array ();
				$_rows = array ();
				foreach ($_POST as $_key => $_val) {
					if (strpos ($_key, $key) !== 0) continue;

					if (strpos ($_key, '_row_') !== FALSE) {
						$_rows[str_replace ($key . '_row_', '', $_key)] = $_val;
						}

					if (strpos ($_key, '_col_') !== FALSE) {
						list ($_row, $_col) = explode ('_', str_replace ($key . '_col_', '', $_key));
						if (isset($_out[$_row]) && is_array ($_out[$_row]))
							$_out[$_row][$_col] = (float) $_val;
						else
							$_out[$_row] = array ($_col => (float) $_val);
						}
					}

				foreach ($_out as $_row => $_cols) $out[$_rows[$_row]] = $_cols;
				break;
			case 'inventory':
				$out = array ();
				foreach ($_POST as $_key => $_val) {
					if (strpos ($_key, $key . '_q') !== 0) continue;
					$id = str_replace ($key . '_q', '', $_key);
					$out[$_POST[$key . '_' . $id]] = $_val;
					}
				$out = serialize ($out);
				break;
			case 'spread':
				$out = array ();
				foreach ($_POST as $_key => $_val) {
					if (strpos ($_key, $key . '_cell_') !== 0) continue;
					list ($id, $prop) = explode ('_', str_replace ($key . '_cell_', '', $_key), 2);
					if (!isset ($out[$id])) $out[$id] = array ();
					$out[$id][$prop] = $_val;
					}
				break;
			case 'children':
				$out = '';
				break;
			case 'switch':
				$out = $val == 'on' ? 1 : 0;
				break;
			case 'contact':
				$out = array ();
				foreach ($_POST as $_key => $_val) {
					if (strpos ($_key, $key) !== 0) continue;
					list ($_key, $_kid) = explode ('-', str_replace ($key . '-', '', $_key));
					if (!empty ($_kid) && (strpos ($_kid, 'n') !== 0))
						$out[$_kid][$_key] = $_val;
					else
						$out[0][(int) str_replace ('n', '', $_kid)][$_key] = $_val;
					}
				if (!empty($out[0]))
					foreach ($out[0] as $_key => $_val)
						$out[0][$_key]['type'] = 'WP_CRM_Person';
				break;
			case 'template':
				
				break;
			default:
				$out = $val;
			}
		return $out;
		}
	
	public static function filter ($key, $data, $type, $filter) {
		switch ((string) $filter) {
			/*
			 * return TRUE to raise the exception;
			 */
			case 'empty':
				return empty($data) ? TRUE : FALSE;
				break;
			case 'Password':
				return preg_match('/[A-Z]+/', $data) ? FALSE : TRUE;
				break;
			case 'password':
				return preg_match('/[a-z]+/', $data) ? FALSE : TRUE;
				break;
			case 'p4ssword':
				return preg_match('/[0-9]+/', $data) ? FALSE : TRUE;
				break;
			case '*assword':
				return preg_match('/[^A-z0-9]+/', $data) ? FALSE : TRUE;
				break;
			case 'phone':
				return preg_match ('/^[0-9 .]+$/', $data) ? FALSE : TRUE;
				break;
			case 'email':
				return preg_match ('/^[a-z0-9._-]{2,}@[a-z0-9.-]{2,}\.[a-z]{2,4}$/', $data) ? FALSE : TRUE;
				break;
			case 'confirm':
				return $_POST[$key] == $_POST[str_replace ('confirm_', '', $key)] ? FALSE : TRUE;
				break;
			case 'username_exists':
				return username_exists ($data) ? TRUE : FALSE;
				break;
			case 'email_exists':
				return email_exists ($data) ? TRUE : FALSE;
				break;
			case 'username_allowed':
				return in_array (strtolower($data), array (
					'adm', 'admin', 'administrator',
					'root',
					'webmaster'
					)) ? TRUE : FALSE;
				break;
			}
		return FALSE;
		}

	public function action () {
		$next = null;
		$func = null;
		$this->payload = array ();

		if (!empty($this->fields))
		foreach ($this->fields as $label => $column) {

			$slug = preg_replace('/[^a-z]+/', '-', strtolower(trim($column['label'])));
			$apply_filters = (($column['class'] == 'tab') && ($_POST[$column['name']] != $slug)) ? FALSE : TRUE;

			if (!empty($column['fields']))
			foreach ($column['fields'] as $key => $field) {
				if (isset($field['next']) && isset($_POST[$key])) $next = $field['next'];
				if (isset($field['callback']) && isset($_POST[$key])) $func = $field['callback'];
				$this->payload[$key] = $this->fields[$label]['fields'][$key]['default'] = $this->_process($key, $field['type']);

				if ($apply_filters && !empty($field['filters'])) {
					foreach ($field['filters'] as $filter => $err_message) {
						$error = self::filter ($key, $this->fields[$label]['fields'][$key]['default'], $field['type'], $filter) ? $err_message : null;
						if (!empty($_POST) && $error)
							$this->errors[] = $this->fields[$label]['fields'][$key]['error'] = $error;
						}
					}
				if (($field['type'] == 'basket') && (!empty($field['options'])) && in_array('coupon', $field['options'])) {
					if ($_POST['coupon'])
						$this->payload['coupon'] = $_POST['coupon'];
					if ($_POST['coupondata'])
						$this->payload['coupondata'] = $_POST['coupondata'];
					}
				}
			if ($column['name'] && isset($_POST[$column['name']]))
				$this->payload[$column['name']] = $_POST[$column['name']];
			}

		if (!empty($this->errors)) return array ('error' => TRUE);
		$this->state = $next;
		if (!is_null ($func) && is_callable ($func)) {
			$return = call_user_func ($func, $this->payload);
			if (!empty ($return)) {
				if (!empty ($this->fields))
				foreach ($this->fields as $label => $column) {
					if (!empty ($column['fields']))
					foreach ($column['fields'] as $key => $field) {
						if ($field['type'] != 'message') continue;
						if (isset ($return[$key])) {
							$this->fields[$label]['fields'][$key]['default'] = is_array ($return[$key]) ? $return[$key]['content'] : $return[$key];
							if (is_array ($return[$key]) && isset ($return[$key]['type'])) $this->fields[$label]['fields'][$key]['class'] = 'alert-' . str_replace (array (
								'error'
								), array (
								'danger'
								), $return[$key]['type']);
							}
						}
					}

				$this->payload['callback'] = $return;
				}
			}
		return array ('error' => FALSE, 'return' => $return);
		}

	private function _render ($key, $field, $wrap = null) {
		if (is_null ($wrap)) $wrap = array ('<' . self::$ITEMSTAG . '>', '</' . self::$ITEMSTAG . '>');
		global
			$current_user,
			$wp_crm_state,
			$wp_crm_offices;


		$data = $wp_crm_state->get ('data');

		if (is_array($field)) {
			$out = '';

			if (isset($field['label'])) $field['label'] = WP_CRM::_ ($field['label']);

			switch ($field['type']) {
				case 'basket':
					if ($field['label']) $out .= '<label>'.$field['label'].':</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$products = $field['default'] instanceof WP_CRM_Basket ? $field['default']->get ('products') : array ();
					if (!empty($products)) {
						$out .= '<' . self::$GROUPTAG . ' class="'.$this->class.'-basket">';
						$total = 0;
						$hidden = '';

						try {
							$wp_crm_coupon = $data['coupon'] ? new WP_CRM_Coupon ($data['coupon']) : null;
							}
						catch (WP_CRM_Exception $wp_crm_exception) {
							$wp_crm_coupon = null;
							$data['coupon'] = null;
							$wp_crm_state->set ('data', $data);
							}
						$coupon_discount = 0;

						foreach ($products as $product => $value) {
							$wp_crm_product = new WP_CRM_Product ((string) $product);
							$full_price =  $wp_crm_product->get('price', array ('quantity' => $value))->get('full price');

							if (is_object($wp_crm_coupon) && ($discount = $wp_crm_coupon->discount ($wp_crm_product, $value))) {
								$coupon_discount += (strpos ($discount, '%') !== FALSE) ?
									0.01 * $value * $full_price * ((float) str_replace ('%', '', $discount)) :
									(float) $discount;	
								}

							$out .= '<' . self::$ITEMSTAG . '><span><select name="'.$key.'-'.$product.'" rel="' . $wp_crm_product->get () . '">';
							for ($c = 0; $c < (WP_CRM_Client::Max_Clients + 1); $c++)
								$out .= '<option value="'.$c.'"'.($c == $value ? ' selected' : '').' rel="' . $wp_crm_product->get ('price', array('quantity' => $c))->get ('full price') . '">'.$c.'</option>';
							$out .= '</select> x</span> <a href="">' . $wp_crm_product->get ('short name') . '</a><br />';
							$out .= '<span rel="'.$wp_crm_product->get('code').'">' . $value . ' x ' . $full_price . ' lei = ' . sprintf('%.2f', $value * $full_price) . ' lei</span></' . self::$ITEMSTAG . '>';

							$total += $value * $full_price;
							}

						if (!empty ($field['options']) && in_array ('coupon', $field['options'])) {
							if ($data['coupon']) {
								$out .= '<' . self::$ITEMSTAG . '><input class="wp-crm-form-coupon-data" name="coupondata" type="hidden" value="'.htmlspecialchars((string) $wp_crm_coupon).'" /><label>Cupon de discount: <strong>' . $data['coupon'] . '</strong></label><div style="clear: both;"></div></' . self::$ITEMSTAG . '>';
								if ($coupon_discount) {
									$out .= '<' . self::$ITEMSTAG . ' class="wp-crm-form-coupon-discount"><label>Discount cupon:</label> <strong>-' . sprintf('%.2f', $coupon_discount) . ' lei</strong><div style="clear: both;"></div></' . self::$ITEMSTAG . '>';
									$total -= $coupon_discount;
									}
								}
							else {
								$out .= '<' . self::$ITEMSTAG . '><label>Am un cupon pentru discount:</label></' . self::$ITEMSTAG . '>';
								$out .= '<' . self::$ITEMSTAG . '><input type="hidden" class="' . $this->class . '-coupon-data" name="coupondata" value="" /><input type="text" name="coupon" value="" /> <button name="couponquery" class="btn btn-sm btn-success ' . $this->class . '-coupon-query">Activeaza</button></' . self::$ITEMSTAG . '>';
								}
							}

						$out .= '<' . self::$ITEMSTAG . '><hr /></' . self::$ITEMSTAG . '>';
						$out .= '<' . self::$ITEMSTAG . ' class="'.$this->class.'-basket-total">Total: ' . sprintf('%.2f', $total) . ' lei</' . self::$ITEMSTAG . '>';
						$out .= '</' . self::$GROUPTAG . '>';
						$out = '<input type="hidden" name="'.$key.'" value="'.((string) $field['default']).'" />' . $out;
						}
					break;
				
				case 'checkbox':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					if (!$field['wrap']) $out .= '<' . self::$GROUPTAG . ' class="'.$this->class.'-list">';
					foreach ($field['options'] as $k => $v)
						if (!$field['wrap']) $out .= '<' . self::$ITEMSTAG . '><label class="checkbox ' . $this->class . '-label"><input type="checkbox" name="' . $key . '_' . $k . '" value="' . $k . '" ' . ($field['default'] ? 'checked' : '') . ' /> ' . $v . '</label></' . self::$ITEMSTAG . '>';
					$out .= '</' . self::$GROUPTAG . '>';
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'radio':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					if (!$field['wrap']) $out .= '<' . self::$GROUPTAG . ' class="'.$this->class.'-list">';
					foreach ($field['options'] as $k => $v)
						if (!$field['wrap']) $out .= '<' . self::$ITEMSTAG . '><label class="radio ' . $this->class . '-label"><input type="radio" name="' . $key . '" value="' . $ki . '" data-toggle="radio" ' . ($field['default'] === $k ? ' checked' : '') . ' /> ' . $v . '</label></' . self::$ITEMSTAG . '>';
					$out .= '</' . self::$GROUPTAG . '>';
					break;
				case 'switch':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<label class="switch pull-right">
<input name="' . $key . '" class="switch-input" type="checkbox"' . ($field['default'] ? ' checked' : '') . '></input>
<span class="switch-label" data-off="Off" data-on="On"></span>
<span class="switch-handle"></span>
</label>';
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'select':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					if (!is_array($field['default'])) $field['default'] = array ($field['default']);

					$out .= '<div class="controls"><select name="' . $key . (isset($field['multiple']) ? '[]' : '') . '" class="form-control ' . $this->class . '-select"' . (isset($field['multiple']) ? ' multiple' : '') . ' data-placeholder="Alege ...">';

					foreach ($field['options'] as $k => $v) {
						if (isset ($v['items']) && is_array ($v['items']) && !empty ($v['items'])) {
							$out .= '<optgroup label="' . $v['title'] . '">';
							foreach ($v['items'] as $_k => $_v)
								$out .= '<option value="' . $_k . '"' . (in_array ($_k, $field['default']) ? ' selected' : '' ) . '>' . $_v . '</option>';
							$out .= '</optgroup>';
							}
						else
							$out .= '<option value="' . $k . '"' . (in_array ($k, $field['default']) ? ' selected' : '' ) . '>' . $v . '</option>';
						}
					$out .= '</select>';
					
					if ($field['add'])
						$out .= '<button class="upd-select wp-crm-view-actions wp-crm-view-add btn btn-sm btn-block" name="newoption" rel="' . $field['add']['object'] . '"><i class="fa fa-plus"></i> ' . $field['add']['label'] . '</button>';
					$out .= '</div>';

					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'button':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<div class="' . $this->class . '-button">';
					$out .= '<button ' . (isset($field['class']) ? ('class="' . $field['class'] . '" ') : '') . 'name="' . $key . '">' . $field['label'] . '</button>';
					$out .= '</div>';
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'submit':
					$out .= '<input class="btn btn-primary' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" type="submit" name="'.$key.'" value="'.$field['label'].'" />';
					break;
				case 'close':
					$out .= '<input class="btn btn-danger '.$this->class.'-button-close" type="button" name="'.$key.'" value="'.$field['label'].'" />';
					break;
				case 'textarea':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<textarea class="'.$this->class.'-textarea form-control" rows="10" name="'.$key.'">'.$field['default'].'</textarea>';
					break;
				case 'rte':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<textarea class="'.$this->class.'-textarea-rte form-control" rows="10" name="'.$key.'">'.$field['default'].'</textarea>';
					break;
				case 'email':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<input class="'.$this->class.'-email" type="text" name="'.$key.'" value="'.$field['default'].'" />';
					break;
				case 'password':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<input class="form-control input-sm ' . $this->class . '-password" type="password" name="'.$key.'" value="'.$field['default'].'" />';
					break;
				case 'hidden':
				case 'object':
					$out .= '<input type="hidden" name="' . $key . '" value="' . $field['default'] . '" />';
					break;
				case 'label':
					$out .= '<label>' . $field['label'] . '</label><div style="clear: both;"></div>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					break;
				case 'tos':
					$rnd = rand ();
					$out .= '<label for="tos-'.$rnd.'"><input type="checkbox" name="'.$key.'" value="1" id="tos-'.$rnd.'" ' . ($field['default'] ? 'checked ' : '') . '/> ' . $field['label'] . '</label>';
					break;
				case 'date':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<div class="input-group date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
					$out .= '<input type="text" name="'.$key.'" value="'.$field['default'].'" class="form-control input-sm ' . $this->class . '-date" />';
					$out .= '</div>';
					break;
				case 'datetime':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					list ($date, $time) = explode (' ', $field['default'], 2);
					$out .= '<div class="row">';
					$out .= '<div class="col-lg-6"><div class="input-group date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" name="'.$key.'-date" value="'.$date.'" class="form-control input-sm date-picker ' . $this->class . '-date" data-date-format="dd/mm/yyyy" /></div></div>';
					$out .= '<div class="col-lg-6"><div class="input-group bootstrap-timepicker"><span class="input-group-addon"><i class="fa fa-clock-o"></i></span><input type="text" name="'.$key.'-time" value="'.$time.'" class="form-control input-sm ' . $this->class . '-time" /></div></div>';
					$out .= '</div>';
					break;
				case 'color':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<input type="text" name="'.$key.'" value="'.$field['default'].'" class="form-control input-sm ' . $this->class . '-color" />';
					break;
				case 'seller':
					if ($field['label']) $out .= '<label>' . $field['label'] . '</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					
					$out .= '<div class="controls"><select name="' . $key . '" class="form-control ' . $this->class . '-select">';

					$list = new WP_CRM_List ('WP_CRM_Company', array('id=1'));// current_user_can ('add_users') ? null : array ($wp_crm_office_query ? $wp_crm_office_query : sprintf ('uid=%d', $current_user->ID)));
					if (!$list->is ('empty'))
						foreach ($list->get() as $wp_crm_company) {
							$out .= '<option value="' . $wp_crm_company->get() . '"' . ($wp_crm_company->get() == $field['default'] ? ' selected' : '') . '>' . $wp_crm_company->get ('name') . '</option>';
							}

					$out .= '</select></div>';
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'buyer':
					if ($field['label']) $out .= '<label>' . $field['label'] . '</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<input type="text" class="form-control input-sm ' . $this->class . '-buyer ' . (isset($field['class']) ? (' '.$field['class']) : '') . '" name="'.$key.'" value="' . (is_object ($field['default']) ? $field['default']->get ('name') : '') . '" rel="' . (is_object ($field['default']) ? (($field['default'] instanceof WP_CRM_Company ? 'company' : 'person') . '-' . $field['default']->get()) : 0). '" />';
					break;
				case 'client':
					global $wp_crm_user;

					if (!is_object ($wp_crm_user))
						$wp_crm_user = new WP_CRM_User (FALSE);

					$rnd = rand ();

					if ($field['label']) $out .= '<label>' . $field['label'] . '</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					$out .= '<div>';
					$out .= '<' . self::$ITEMSTAG . '><label class="radio ' . $this->class . '-label"><input type="radio" name="' . $key . '-type" value="person" data-selected="radio" data-target="#' . $key . '_person_' . $rnd . '"' . ($field['default'] === $k ? ' checked' : '') . ' /> ' . WP_CRM::_ ('Person') . '</label></' . self::$ITEMSTAG . '>';

					$out .= '<div id="' . $key . '_person_' . $rnd . '" class="collapse">';
					$out .= '<label>' . WP_CRM::_ ('Last Name') . '</label>';
					$out .= '<input type="text" placeholder="' . WP_CRM::_ ('Popescu') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-last-name" value="'. $wp_crm_user->get ('last_name') .'" />';
					$out .= '<label>' . WP_CRM::_ ('First Name') . '</label>';
					$out .= '<input type="text" placeholder="' . WP_CRM::_ ('Vasile') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-first-name" value="'. $wp_crm_user->get ('first_name') .'" />';
					$out .= '<label>' . WP_CRM::_ ('Person UIN') . '</label>';
					$out .= '<input type="text" placeholder="' . WP_CRM::_ ('xxxxxxxxxxxxx') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-p-uin" value="' . $wp_crm_user->get ('uin') . '" />';
					$out .= '<label>' . WP_CRM::_ ('E-Mail') . '</label>';
					$out .= '<input type="text" placeholder="' . WP_CRM::_ ('email@adress') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-p-email" value="' . $wp_crm_user->get ('email') . '" />';
					$out .= '<label>' . WP_CRM::_ ('Phone') . '</label>';
					$out .= '<input type="text" placeholder="' . WP_CRM::_ ('07xx xxx xxx') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-p-phone" value="' . $wp_crm_user->get ('phone') . '" />';
					$out .= '<' . self::$ITEMSTAG . '><label class="checkbox ' . $this->class . '-label"><input type="checkbox" name="' . $key . '-p-update" value="1" /> ' . WP_CRM::_ ('Update Your Profile Data') . '</label></' . self::$ITEMSTAG . '>';
					$out .= '</div>';
					$out .= '</div>';


					$out .= '<div>';
					$out .= '<' . self::$ITEMSTAG . '><label class="radio ' . $this->class . '-label"><input type="radio" name="' . $key . '-type" value="company" data-selected="radio" data-target="#' . $key . '_company_' . $rnd . '"' . ($field['default'] === $k ? ' checked' : '') . ' /> ' . WP_CRM::_ ('Company') . '</label></' . self::$ITEMSTAG . '>';

					$out .= '<div id="' . $key . '_company_' . $rnd . '" class="collapse">';
					$out .= '<div class="controls"><select name="' . $key . '-company" class="form-control ' . $this->class . '-select" data-placeholder="Alege ..." data-autofill="ajax" data-requestobject="WP_CRM_Company-" data-target="#' . $key . '_company_' . $rnd . '">';

					$company_list = $wp_crm_user->get ('company_list');
					$companies = array ('0' => 'Choose A Company ...');
					if (!$company_list->is ('empty'))
					foreach ($company_list->get () as $company)
						$companies[$company->get ()] = $company->get ('name');
					$companies['-1'] = 'Add New ...';
					foreach ($companies as $k => $v) {
						if (isset ($v['items']) && is_array ($v['items']) && !empty ($v['items'])) {
							$out .= '<optgroup label="' . WP_CRM::_ ($v['title']) . '">';
							foreach ($v['items'] as $_k => $_v)
								$out .= '<option value="' . $_k . '"' . (in_array ($_k, $field['default']) ? ' selected' : '' ) . '>' . WP_CRM::_ ($_v) . '</option>';
							$out .= '</optgroup>';
							}
						else
							$out .= '<option value="' . $k . '"' . (is_array ($field['default']) && in_array ($k, $field['default']) ? ' selected' : '' ) . '>' . WP_CRM::_ ($v) . '</option>';
						}
					$out .= '</select>';
					
					$out .= '<label>' . WP_CRM::_ ('Company Name') . '</label>';
					$out .= '<input type="text" data-autofill="name" placeholder="' . WP_CRM::_ ('My Company Ltd.') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-name" value="" />';
					$out .= '<label>' . WP_CRM::_ ('Company UIN') . '</label>';
					$out .= '<input type="text" data-autofill="uin" placeholder="' . WP_CRM::_ ('xxxxxxxx') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-uin" value="" />';
					$out .= '<label>' . WP_CRM::_ ('Registration Number') . '</label>';
					$out .= '<input type="text" data-autofill="rc" placeholder="' . WP_CRM::_ ('Jxx/xxxx/xxxx') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-rc" value="" />';
					$out .= '<label>' . WP_CRM::_ ('E-Mail') . '</label>';
					$out .= '<input type="text" data-autofill="email" placeholder="' . WP_CRM::_ ('email@adress') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-c-email" value="' . $wp_crm_user->get ('email') . '" />';
					$out .= '<label>' . WP_CRM::_ ('Phone') . '</label>';
					$out .= '<input type="text" data-autofill="phone" placeholder="' . WP_CRM::_ ('07xx xxx xxx') . '" class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="' . $key . '-c-phone" value="' . $wp_crm_user->get ('phone') . '" />';
					$out .= '<' . self::$ITEMSTAG . '><label class="checkbox ' . $this->class . '-label"><input type="checkbox" name="' . $key . '-c-update" value="1" /> ' . WP_CRM::_ ('Update Your Company Data') . '</label></' . self::$ITEMSTAG . '>';

					$out .= '</div>';
					$out .= '</div>';
					$out .= '</div>';

					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'payment':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					if (empty ($field['default'])) break;

					$invoices = array ();
					foreach ($field['default'] as $invoice_id) {
						try {
							$invoice = new WP_CRM_Invoice ((int) $invoice_id);
							}
						catch (WP_CRM_Exception $wp_crm_exception) {
							continue;
							}
						$invoices[] = $invoice;
						}

					if (empty ($invoices)) break;

					$out .= '</form>';

					$invoice = current ($invoices);
					
					$out .= '<div class="row"><div class="col-md-6">';

					/** PayPal */
					$out .= WP_CRM_Payment::paypal ($invoice);
					$out .= '</div><div class="col-md-6">';
					/** MobilPay */
					$out .= WP_CRM_Payment::mobilpay ($invoice);
					$out .= '</div></div>';

					$out .= '<form action="" method="post">';
					break;
				case 'product':
					global $wpdb;
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					if (!empty ($field['default'])) {
						$out .= '<div class="ui-widget-product-basket">';
						foreach ($field['default'] as $product => $quantity) {
							try {
								$wp_crm_product = new WP_CRM_Product ($product);
								$wp_crm_price = $wp_crm_product->get ('price', array ('quantity' => $quantity));
								$wp_crm_reference = $wp_crm_product->get ('price', array ('quantity' => 1));
								}
							catch (WP_CRM_Exception $wp_crm_exception) {
								$sql = $wpdb->prepare ('select product,price,vat from `' . $wpdb->prefix . WP_CRM_Basket::$T . '` where iid=%d and code=%s and quantity=%d;', array (
									$field['options']['iid'],
									$product,
									$quantity
									));
								$product = $wpdb->get_row ($sql);
								if (is_null ($product)) continue;
								$wp_crm_price = $wp_crm_reference = new WP_CRM_Price (array (
									'price' => $product->price,
									'vat' => $product->vat
									));
								$wp_crm_product = $product->product;
								}
							$out .= '<div><input type="text" class="form-control input-sm" name="quantity_n_' . '" value="' . $quantity . '" /><input type="hidden" name="price_n_' . '" value="' . ';' . '"><input type="hidden" name="name_n_' . '" value="' . ($wp_crm_product instanceof WP_CRM_Product ? $wp_crm_product->get ('title') : $wp_crm_product) . '"> x <span>' . ($wp_crm_product instanceof WP_CRM_Product ? $wp_crm_product->get ('title') : $wp_crm_product) . '</span><button class="btn btn-danger btn-sm fui-cross" rel="n_' . '"></button></div>';
							}
						$out .= '</div><div class="' . $this->class . '-separator"></div>';
						}
					$out .= '<button class="btn btn-block btn-primary ' . $this->class . '-product' . ($field['class'] ? (' ' . $field['class']) : '') . '" name="' . $key . '"><i class="fa fa-plus"></i> ' . $field['label'] . '</button>';
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'spread':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<div class="' . $this->class . '-separator"></div>';


					$spread = $field['default'];

					$cols = array_values ($spread[0]);
					$cols[] = '<button class="btn btn-sm ' . $this->class . '-spread-add-row btn-success fa fa-plus"></button>';
					$rows = array ();

					if (!empty ($spread[0])) {
						/**
						 * List existing data:
						 */
						foreach ($spread as $id => $data) {
							$row = array ();
							if (!$id) continue;

							foreach ($spread[0] as $col => $label) {
								$row[] = '<input class="form-control input-sm" name="' . $key . '_cell_' . $id . '_' . $col . '" value="' . $data[$col] . '" type="text" />';
								}
							$row[] = '<button class="btn btn-sm ' . $this->class . '-spread-del-row btn-danger fa fa-times"></button>';
							$rows[] = $row;
							}
						/**
						 * Add a new row:
						 */
						if (sizeof ($spread) < 2) {
							$row = array ();
							foreach ($spread[0] as $col => $label) {
								$row[] = '<input class="form-control input-sm" name="' . $key . '_cell_n0_' . $col . '" value="" type="text" />';
								}
							$row[] = '<button class="btn btn-sm ' . $this->class . '-spread-del-row btn-danger fa fa-times"></button>';

							$rows[] = $row;
							}
						}
					$out .= self::table ($cols, $rows, $this->class);
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'matrix':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<div class="' . $this->class . '-separator"></div>';
					$out .= '<div class="' . $this->class . '-matrix-wrapper">';
					$matrix = $field['default'];
					$rows = 0;
					if (!empty($matrix)) {
						$out .= '<' . self::$GROUPTAG . ' class="' . $this->class . '-matrix">';
						foreach ($matrix as $row => $data) {
							if (!$rows) {
								$out .= '<' . self::$ITEMSTAG . '>';
								$out .= '<' . self::$GROUPTAG . ' class="' . $this->class . '-matrix-row">';
								$del = '<' . self::$GROUPTAG . ' class="' . $this->class . '-matrix-row">';
								$out .= '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell-date">&nbsp;</' . self::$ITEMSTAG . '>';
								$del .= '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell-add-row"><button class="btn btn-sm btn-success ' . $this->class . '-matrix-add-row fa fa-plus"></button></' . self::$ITEMSTAG . '>';
								$cols = 0;
								foreach ($data as $col => $value) {
									$out .= '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell"><input type="text" name="' . $key . '_col_' . ($cols + 1) . '" class="form-control input-sm" value="' . $col . '" /></' . self::$ITEMSTAG . '>';
									$del .= $cols ? '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell-delete-col"><button class="btn btn-sm btn-danger ' . $this->class . '-matrix-del-col fa fa-times"></button></' . self::$ITEMSTAG . '>' : '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell-delete-col">&nbsp;</' . self::$ITEMSTAG . '>';
									$cols ++;
									}
								$out .= '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell-add-col"><button class="btn btn-sm btn-success ' . $this->class . '-matrix-add-col fa fa-plus"></button></' . self::$ITEMSTAG . '>';
								$out .= '</' . self::$GROUPTAG . '>';
								$del .= '</' . self::$GROUPTAG . '>';
								$out .= '</' . self::$ITEMSTAG . '>';
								}
							$out .= '<' . self::$ITEMSTAG . '>';
							if (!empty($data)) {
								$out .= '<' . self::$GROUPTAG . ' class="' . $this->class . '-matrix-row">';
								$out .= '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell-date"><input type="text" name="' . $key . '_row_' . ($rows + 1) . '" class="form-control input-sm ' . $this->class . '-date" value="' . $row . '" /></' . self::$ITEMSTAG . '>';
								$cols = 0;
								foreach ($data as $col => $value) {
									$out .= '<' . self::$ITEMSTAG . '><input class="form-control input-sm" type="text" name="' . $key . '_cell_' . ($rows + 1) . '_' . ($cols + 1) . '" value="' . $value . '" /></' . self::$ITEMSTAG . '>';
									}
								if ($rows) $out .= '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-cell-delete-row"><button class="btn btn-sm btn-danger fa fa-times"></button></' . self::$ITEMSTAG . '>';
								$out .= '</' . self::$GROUPTAG . '>';
								}
							$out .= '</' . self::$ITEMSTAG . '>';
							$rows ++;
							}
						
						$out .= '<' . self::$ITEMSTAG . ' class="' . $this->class . '-matrix-row-delete">' . $del . '</' . self::$ITEMSTAG . '>';
						$out .= '</' . self::$GROUPTAG . '>';
						$out .= '<div class="' . $this->class . '-separator"></div>';
						$out .= '</div>';
						}
					break;
				case 'nested':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					$out .= '<div class="' . $this->class . '-nested">';
	
					$out .= '<input type="text" class="form-control input-sm ' . $this->class . '-nested-input" name="' . $key . '" value="" />';
					$out .= '<button class="btn btn-primary btn-sm btn-block ' . $this->class . '-nested-item-add wp-crm-view-add" rel="WP_CRM_Task-0"><i class="fa fa-plus"></i> Activitate Noua</button>';

					$out .= '<div class="dd">';
					$out .= '<ol class="dd-list ' . $this->class . '-nested-list">';
					/*$out .= '<li class="dd-item" data-id="2"><div class="dd-handle"><span>Task #2</span>
						<div class="dd-actions pull-right">
							<button class="btn btn-sm btn-primary fa fa-edit ' . $this->class . '-nested-item-edit"></button>
							<button class="btn btn-sm btn-danger fa fa-times ' . $this->class . '-nested-item-delete"></button>
						</div></div></li>';*/
					$out .= '</ol>';
					$out .= '</div>';

					$out .= '</div>';
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'gantt':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					$out .= '<div class="' . $this->class . '-gantt">';
					$out .= '<div class="row"><div class="col-md-3">Task #1</div><div class="col-md-2"></div><div class="col-md-7">';
					$out .= '<div class="' . $this->class . '-sliderrange slider small sliderRange sliderBlue"></div>';
					$out .= '</div>';
					$out .= '</div>';
					$out .= '<div class="row"><div class="col-md-3">Task #1</div><div class="col-md-2"></div><div class="col-md-7">';
					$out .= '<div class="' . $this->class . '-sliderrange slider small sliderRange sliderBlue"></div>';
					$out .= '</div>';
					$out .= '</div>';
					$out .= '<div class="row"><div class="col-md-3">Task #1</div><div class="col-md-2"></div><div class="col-md-7">';
					$out .= '<div class="' . $this->class . '-sliderrange slider small sliderRange sliderBlue"></div>';
					$out .= '</div>';
					$out .= '</div>';
	
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;
				case 'duration':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					$out .= '<div class="' . $this->class . '-duration">';
					$out .= '<div class="row">';
					$out .= '<div class="col-md-1">1</div>';
					$out .= '<div class="col-md-8">';
					$out .= '<div class="' . $this->class . '-slider slider small"></div>';
					$out .= '</div>';
					$out .= '<div class="col-md-3">';
					$out .= '<select name="" class="form-control ' . $this->class . '-select">';
					$out .= '<option value="m">Minute</option>';
					$out .= '<option value="h">Ore</option>';
					$out .= '<option value="d">Zile</option>';
					$out .= '<option value="w">Saptamani</option>';
					$out .= '</select>';
					$out .= '</div>';
					$out .= '</div>';
				
					$out .= '<div class="' . $this->class . '-separator"></div>';
					break;	
				case 'inventory':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					
					$out .= '<div class="' . $this->class. '-inventory">';
					$out .= '<input type="text" class="form-control input-sm ' . $this->class . '-inventory-input" name="' . $key . '" value="" />';
					$out .= '<button class="btn btn-primary btn-sm btn-block ' . $this->class . '-inventory-add"><i class="fa fa-plus"></i> Adauga Resurse</button>';
					/*
					$out .= '<div class="' . $this->class. '-inventory"><div class="row">
						<div class="col-sm-2">
						<input type="text" name="' . $key . 'q" value="" class="' . $this->class . '-inventory-quantity form-control input-sm" />
						</div>
						<div class="col-sm-1">
						<button class="btn btn-danger btn-sm fa fa-times"></button>
						</div>
						<div class="col-sm-8">
						<select name="' . $key . '" class="form-control ' . $this->class . '-select"' . (isset($field['multiple']) ? ' multiple' : '') . '>';
					foreach ($field['options'] as $k => $v) {
						if (isset ($v['items']) && is_array ($v['items']) && !empty ($v['items'])) {
							$out .= '<optgroup label="' . $v['title'] . '">';
							foreach ($v['items'] as $_k => $_v)
								$out .= '<option value="' . $_k . '">' . $_v . '</option>';
							$out .= '</optgroup>';
							}
						else
							$out .= '<option value="' . $k . '">' . $v . '</option>';
						}
					$out .= '</select></div><div class="col-sm-1"><button class="' . $this->class . '-inventory-add btn btn-primary btn-sm fa fa-plus"></button></div></div>';
					if (!empty ($field['default'])) {
						$c = 1;
						foreach ($field['default'] as $_key => $_val) {
							$resource = new WP_CRM_Resource ((int) $_key);
							$out .= '<div class="col-md-2"><input type="text" name="' . $key . '_q' . $c . '" value="' . $_val . '" class="form-control" /></div><div class="col-md-1">x</div><div class="col-md-8"><input type="hidden" value="' . $_key . '" name="' . $key . '_' . $c . '" /><span>' . $resource->get ('title') . '</span></div><div class="col-md-1"><button class="form-control wp-crm-form-inventory-delete"><i class="fa fa-minus"></i></button></div>';
							$c ++;
							}
						}
					*/
					$out .= '</div><div class="' . $this->class . '-separator"></div>';
					break;
				case 'template':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					$out .= '<div class="row"><div class="col-md-9 controls">';
					$out .= '<select name="' . $key . '_template" class="form-control ' . $this->class . '-select wp-crm-view-template">';
					
					$list = new WP_CRM_List ('WP_CRM_Template', array ('oid in (' . implode (',', $wp_crm_offices) . ')'));
					$out .= '<option value="WP_CRM_Template-0">Un nou model ...</option>';
					if (!$list->is ('empty'))
					foreach ($list->get () as $template) {
						$out .= '<option value="WP_CRM_Template-' . $template->get() . '">' . $template->get ('subject') . '</option>';
						}
					$out .= '</select>';
					$out .= '</div>';
					$out .= '<div class="col-md-3"><span class="btn-group pull-right">';
					$out .= '<button class="btn wp-crm-view-actions wp-crm-view-loadtemplate" title="Incarca"><span class="fa fa-upload"></span></button>';
					$out .= '<button class="btn wp-crm-view-actions wp-crm-view-savetemplate" title="Salveaza"><span class="fa fa-download"></span></button>';
					$out .= '<button class="btn wp-crm-view-actions wp-crm-view-deletetemplate" title="Sterge"><span class="fa fa-times"></span></button>';
					$out .= '</span></div></div>';
					/*
					$out .= '<label>Subiect</label>';
					$out .= '<div class="row"><div class="col-md-12">';
					$out .= '<input class="form-control" type="text" name="' . $key . '_subject" value="" />';
					$out .= '</div></div>';
					$out .= '<div class="row"><div class="col-md-12">';
					$out .= '<textarea class="form-control" name="' . $key . '_message"></textarea>';
					$out .= '</div></div>';
					*/
					break;
				case 'shopcart':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$nav = '';
					$tab = '';
					if (!empty ($field['options'])) {
						$c = 0;
						foreach ($field['options'] as $okey => $options) {
							$nav .= '<' . self::$ITEMSTAG . '' . ($c ? '' : ' class="active"') . '><a href="#' . $key . $c . '">' . $options['title'] . '</a></' . self::$ITEMSTAG . '>';
							$tab .= '<div id="' . $key . $c . '" class="tab-pane active">';
							if (!empty ($options['sections']))
								foreach ($options['sections'] as $skey => $section) {
									$tab .= '<textarea class="'.$this->class.'-textarea" name="' . $skey . '">' . $field['default'][$skey] . '</textarea>';
									}
							$tab .= '</div>';
							$c ++;
							}
						}
					else
						$tab = 'none';
					$out .= '<div class="' . $this->class . '-tabs"><' . self::$GROUPTAG . ' class="nav nav-tabs nav-append-content">' . $nav . '</' . self::$GROUPTAG . '><div class="tab-content">' . $tab . '</div></div>';
					break;
				case 'logo':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<div class="' . $this->class . '-file"><div class="' . $this->class . '-file-view">' . ($field['default'] ? '<img src="' . $field['default'] . '" />' : '') . '</div><input type="hidden" name="' . $key . '" value="' . $field['default'] . '" /><input type="button" name="' . $key . '-select" value="Select" class="' . $this->class . '-file-select btn btn-warning" /> <input type="button" name="' . $key . '-upload" value="Upload" class="' . $this->class . '-file-upload btn btn-success" /><div class="' . $this->class . '-file-progress"><div class="' . $this->class . '-file-bar"></div></div></div>';
					break;
				case 'file':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';

					$out .= '<div class="' . $this->class . '-file">';
					$out .= '<div class="' . $this->class . '-file-view"></div>';
					$out .= '<input type="hidden" name="' . $key . '" value="' . ($field['default'] ? str_replace ('"', '&quot;', (json_encode ($field['default']))) : '') . '" />';
					
					if (is_array ($field['default']) && !empty($field['default']))
						$field['default'] = $field['default'][0];
					if (is_string ($field['default']) && $field['default'])
						$field['default'] = new WP_CRM_File ($field['default']);

					$out .= '<div class="row">
					<div class="col-md-4">
						<span class="' . $this->class . '-file-name">' . (is_object ($field['default']) ?
							(in_array ($field['default']->get ('type'), array ('jpg', 'png')) ?
								('<a href="' . $field['default']->get ('url') . '" target="_blank"><img src="' . $field['default']->get ('url', 'c32x32') . '" alt="' . $field['default']->get ('title') . '" /></a>') :
								('<a href="' . $field['default']->get ('url') . '" target="_blank">' . $field['default']->get ('title') . ' <i class="fa fa-external-link"></i></a>')
								) :
							'Apasa butonul Select pentru a alege un fisier, urmat de Upload pentru a-l incarca.'
						) . '</span>
					</div>
					<div class="col-md-2">
						<input type="button" name="' . $key . '-select" value="Select" class="' . $this->class . '-file-select form-control btn btn-warning" />
					</div>
					<div class="col-md-2">
						<input type="button" name="' . $key . '-upload" value="Upload" class="' . $this->class . '-file-upload form-control btn btn-success" data-path="' . $field['path'] . '" />
					</div>
					<div class="col-md-4">
						<div class="' . $this->class . '-file-progress progress slim ui-progressbar progressGreen simpleProgress"><div class="' . $this->class . '-file-bar ui-progressbar-value"></div></div>
					</div>';

					$out .= '</div></div>';

					break;
				case 'attachment':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					
					$out .= '<div class="' . $this->class . '-filedrop" rel="' . $key . '">';
					$out .= '<input class="' . $this->class . '-filedrop-default" type="hidden" name="' . $key . '" value="' . $field['default'] . '" />';

					$out .= '<div class="row">
					<div class="col-md-6">
						<ul class="' . $this->class . '-filedrop-list">';

					if (!empty ($field['options']))
					foreach ($field['options'] as $file => $prop) {
						$out .= '<li><input type="checkbox" name="' . $key . '[]" value="' . $file . '" checked>' . $prop['title'] . '<i class="fa fa-times"></i></li>';
						}
					$out .= '
						</ul>
					</div>
					<div class="col-md-2">
						<input type="button" name="' . $key . '-select" value="Select" class="' . $this->class . '-filedrop-select form-control btn btn-warning" />
					</div>
					<div class="col-md-4">
						<div class="' . $this->class . '-filedrop-progress progress slim ui-progressbar progressGreen simpleProgress"><div class="' . $this->class . '-file-bar ui-progressbar-value"></div></div>
					</div>';

					$out .= '</div></div>';
					
					
					//$out .= '<div class="' . $this->class . '-filedrop" rel="' . $key . '"><input type="hidden" name="' . $key . '" value="' . $field['default'] . '" /><div class="' . $this->class . '-filedrop-preview"><span class="' . $this->class . '-filedrop-preview-image"><img src="' . $field['default'] . '" alt="" title="" /><span class="' . $this->class . '-filedrop-uploaded"></span></span><div class="' . $this->class . '-filedrop-progress"><div class="' . $this->class . '-filedrop-progressbar"></div></div></div><span class="' . $this->class . '-filedrop-message">Pentru upload, trage fisierul peste acest text!</span></div>';

					/*
					$out .= '<div class="' . $this->class . '-file">';
					$out .= '<div class="' . $this->class . '-file-view"></div>';
					$out .= '<input type="hidden" name="' . $key . '" value="' . ($field['default'] ? str_replace ('"', '&quot;', (json_encode ($field['default']))) : '') . '" />';

					if (is_array ($field['default']))
						$field['default'] = $field['default'][0];

					$out .= '<div class="row">
					<div class="col-md-4">
						<span class="' . $this->class . '-file-name">' . (is_object ($field['default']) ? ('<a href="' . $field['default']->url . '" target="_blank" />' . $field['default']->name . ' <i class="fa fa-external-link"></i></a>') : 'Apasa butonul Select pentru a alege un fisier, urmat de Upload pentru a-l incarca.') . '</span>
					</div>
					<div class="col-md-2">
						<input type="button" name="' . $key . '-select" value="Select" class="' . $this->class . '-file-select form-control btn btn-warning" />
					</div>
					<div class="col-md-2">
						<input type="button" name="' . $key . '-upload" value="Upload" class="' . $this->class . '-file-upload form-control btn btn-success" />
					</div>
					<div class="col-md-4">
						<div class="' . $this->class . '-file-progress progress slim ui-progressbar progressGreen simpleProgress"><div class="' . $this->class . '-file-bar ui-progressbar-value"></div></div>
					</div>';

					$out .= '</div></div>';
					*/
					break;
				case 'filedrop':
					//$out .= '<div class="' . $this->class . '-filedrop" rel="' . $key . '"><input type="hidden" name="' . $key . '" value="' . $field['default'] . '" /><div class="' . $this->class . '-filedrop-preview"><span class="' . $this->class . '-filedrop-preview-image"><img src="' . $field['default'] . '" alt="" title="" /><span class="' . $this->class . '-filedrop-uploaded"></span></span><div class="' . $this->class . '-filedrop-progress"><div class="' . $this->class . '-filedrop-progressbar"></div></div></div><span class="' . $this->class . '-filedrop-message">Pentru upload, trage fisierul peste acest text!</span></div>';
					break;
				case 'seats':
					$out .= '<div class="' . $this->class . '-seats-tools">
<button class="btn btn-primary"><i class="fui-plus"></i></button>
<button class="btn btn-danger"><i class="fui-cross"></i></button>
</div><div class="' . $this->class . '-seats-canvas"></div>';
					break;
				case 'children':
					if ($field['label']) $out .= '<label>' . $field['label'] . '</label>';
					if ($field['help']) $out .= '<label>' . $field['help'] . '</label>';

					if ($field['default'] instanceof WP_CRM_List) {
						$view = new WP_CRM_View ($field['default'], array (
								array (
									'type' => 'toolbar',
									'items' => array (
										'add' => array (
											'label' => 'Adauga'
											)
										)
									),
								array (
									'type' => 'column',
									'label' => 'Actiuni',
									'items' => array (
										'edit'	=> array (
											'label' => 'Modifica',
											),
										'delete' => array (
											'label' => 'Sterge'
											),
										)
									)
								));
						$out .= $view->get ();
						unset ($view);
						}
					break;
				case 'contact':
					if ($field['label']) $out .= '<label>' . $field['label'] . '</label>';
					if ($field['help']) $out .= '<label>' . $field['help'] . '</label>';
					$tabs = array (
						);
					$panes = array (
						);


					$first = TRUE;
					foreach ($field['default'] as $cid => $cdata) {
						if ($cid == 0) continue;
						$tabs[] = '<li' . ($first ? ' class="active"' : '') . '><a href="#contact-' . $cid . '">' . (empty ($cdata['title']) ? '<i class="fa fa-user"></i>' : $cdata['title']) . '</a></li>';
						
						$pane = '<div class="tab-pane' . ($first ? ' active' : '') . '" id="contact-' . $cid . '">';
						foreach ($field['default'][0] as $skey => $label) {
							$pane .= '<label>' . $label . '</label>
	<input class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" type="text" value="' . $cdata[$skey] . '" name="' . $key . '-' . $skey . '-' . $cid . '" />';
							}
						$pane .= '<br />
		<input class="' . $this->class . '-tab-del btn btn-danger' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" type="button" value="-" name="tabdel" />
		<div class="' . $this->class . '-separator"></div>
	</div>';
						$panes[] = $pane;
						$first = FALSE;
						}

					$tab = '<li' . ($first ? ' class="active"' : '') . '><a href="#contact-new"><i class="fa fa-plus"></i></a></li>';
					$pane = '<div class="tab-pane' . ($first ? ' active' : '') . '" id="contact-new">';
					foreach ($field['default'][0] as $skey => $label) {
						$pane .= '<label>' . $label . '</label>
<input class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" type="text" value="" name="' . $key . '-' . $skey . '" />';
						}
					$pane .= '<br />
	<input class="' . $this->class . '-tab-add btn btn-primary' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" type="button" value="+" name="tabadd" />
	<div class="' . $this->class . '-separator"></div>
</div>';
					$tabs[] = $tab;
					$panes[] = $pane;

					$out .= '<div class="' . $this->class . '-contacts">
	<ul class="nav nav-tabs">
		' . implode ("\n", $tabs) . '
	</ul>
	<div class="tab-content">
		' . implode ("\n", $panes) . '
	</div>
</div>';
					break;
				case 'structure':
					break;
				case 'tree':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					if ($field['default'] instanceof WP_CRM_Tree) {
						$view = new WP_CRM_View ($field['default'], array (
							'nodeadd' => 'Adauga',
							'nodeedit' => 'Modifica',
							'nodedelete' => 'Sterge'
							));
						$out .= $view->get ();
						}
					break;
				case 'message':
					if (!empty ($field['default']))
					$out .= '<div class="alert ' . (isset($field['class']) ? $field['class'] : 'alert-success'). '">' . $field['default'] . '</div>';
					break;
				default:
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if ($field['help']) $out .= '<small>'.$field['help'].'</small>';
					$out .= '<input type="text"' . (isset($field['placeholder']) ? (' placeholder="' . $field['placeholder'] . '" ') : '') . ' class="form-control input-sm' . (isset($field['class']) ? ' ' . $field['class'] : '') . '" name="'.$key.'" value="'.$field['default'].'" />';
				}

			if ($field['error'])
				$wrap = array (
					str_replace ('>', ' class="' . $this->class . '-error">', $wrap[0]),
					'<div class="alert alert-danger ' . $this->class . '-error-hint">' . $field['error'] . '</div>' . $wrap[1] );


			if ($field['condition']) {
				if (($p = strpos ($wrap[0], 'class="')) !== FALSE)
					$wrap[0] = substr ($wrap[0], 0, $p) . 'rel="' . $field['condition'] . '" class="' . $this->class . '-cond ' . substr ($wrap[0], $p+7);
				else
					$wrap[0] = substr ($wrap[0], 0, -1) . ' rel="' . $field['condition'] . '" class="' . $this->class . '-cond">';
				}

			return $wrap[0] . $out . $wrap[1];
			}
		else
			return '<input type="hidden" name="'.$key.'" value="'.$field.'" />';
		}

	private function _height ($key, $field) {
		return 28;
		}

	public function render ($echo = FALSE) {
		$this->html = '';
		$tabs = '';

		$c = 0;
		if (!empty($this->fields))
		foreach ($this->fields as $column) {
			$height = 0;
			$classes = array ();
			if ($column['label']) $classes[] = $this->class.'-wrapper';
			if ($column['class']) $classes[] = $this->class.'-'.$column['class'];

			$height = 0;
			if (!empty($column['fields'])) {
				if ($column['class'] == 'tab') $height += 38;
				foreach ($column['fields'] as $key => $field)
					$height += $this->_height ($key, $field); 
				}
			$this->html .= '<div'.(!empty($classes) ? (' class="'.implode(' ', $classes).'"') : '').($height ? (' rel="'.$height.'"') : '').'>';
			if ($column['class'] == 'tab') {
				$this->html .= '<p class="'.$this->class.'-collabel">'.$column['label'].'</p>';
				$slug = preg_replace('/[^a-z]+/', '-', strtolower(trim($column['label'])));
				$rnd = rand();
				$tabs .= $this->payload[$column['name']] ? 
					('<label for="tab-'.$rnd.'"><input'.($this->payload[$column['name']] == $slug ? ' checked="checked"' : '').' id="tab-'.$rnd.'" type="radio" name="' . $column['name'] . '" value="'.$slug.'" /> ' . $column['label'] . '</label>') :
					('<label for="tab-'.$rnd.'"><input'.($c ? '' : ' checked="checked"').' id="tab-'.$rnd.'" type="radio" name="' . $column['name'] . '" value="'.$slug.'" />' . $column['label'] . '</label>');
				$c ++;
				}
			if (in_array ($column['class'], array ('label', 'separator'))) {
				if ($column['label'])
					$this->html .= $column['label'];
				}
			else {
				$this->html .= '<' . self::$GROUPTAG . ' class="'.$this->class.'-column'.($column['label'] ? (' '.$this->class.'-tabtarget') : '').'">';
				foreach ($column['fields'] as $key => $field) {
					$this->html .= $this->_render ($key, $field);
					}
				}
			$this->html .= '</' . self::$GROUPTAG . '>';
			$this->html .= '</div>';
			}

		if ($tabs) {
			$tabs = '<' . self::$GROUPTAG . ' class="' . $this->class . '-tabs">' . $tabs . '</' . self::$GROUPTAG . '>';
			$this->html = $tabs . $this->html;
			}

		$this->html = '<form class="'.$this->class.'" action="" method="post">' . $this->html . '</form>';

		if (!$echo) return $this->html;
		echo $this->html;
		}

	public function __toString () {
		return $this->render ();
		}

	public function __destruct () {
		}

	private static function table ($cols, $rows, $class = '') {
		$out .= '<table class="' . $class . '-spread-table">' . "\n";
		$out .= "\t<thead>\n";
		$out .= "\t\t<tr>\n";
		foreach ($cols as $col) {
			$out .= "\t\t\t" . '<th class="' . $class . '-spread-cell">' . $col . '</th>' . "\n";
			}
		$out .= "\t\t</tr>\n";
		$out .= "\t</thead>\n";
		$out .= "\t<tbody>\n";
		foreach ($rows as $row) {
			$out .= "\t\t<tr>\n";
			foreach ($cols as $key => $col)
				$out .= "\t\t\t" . '<td class="' . $class . '-spread-cell">' . $row[$key] . '</td>' . "\n";
			$out .= "\t\t</tr>\n";
			}
		$out .= "\t</tbody>\n";
		$out .= '</table>' . "\n";
		return $out;
		}

	};
?>
