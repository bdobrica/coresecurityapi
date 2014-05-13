<?php
class WP_CRM_State {
	const AddToCart		= 0;
	const Participants	= 1;
	const Payment		= 2;

	private $ID;
	private $basket;

	private function _pack () {
		$_SESSION[__CLASS__] = serialize (array (
			'id' => $this->ID,
			'basket' => $basket
			));
		}

	private function _unpack () {
		if (isset ($_SESSION[__CLASS__]))
			$data = unserialize ($_SESSION[__CLASS__]);
		if (isset($data['id']))
			$this->ID = $data['id'];
		if (isset($data['basket']))
			$this->basket = $data['basket'];
		}

	public function __construct () {
		session_start();

		$this->ID = 0;
		$this->basket = array ();
		}

	public function buy ($product, $quantity = 1) {
		$series = wp_crm_extract_series ($product);
		$number = wp_crm_extract_number ($product);
		
		$product = new WP_CRM_Product (array ('series' => $series, 'number' => $number));
		$code = $product->get ('current code');

		if ($code && !isset($this->basket[$code])) $this->basket[$code] = 1;

		$this->_pack ();
		}

	public function get ($key = null, $options = null) {
		if ($key == 'basket') return $this->basket;
		return $this->ID;
		}

	public function set ($key = null, $value = null) {
		if ($key == 'ID') $this->ID = (int) $value;
		if ($key == 'basket') $this->basket = $value;
		
		$this->_pack ();
		}

	public function __destruct () {
		}
	};

class WP_CRM_Form_Structure {
	private $fields;

	public function __construct ($data = null) {
		global $wp_crm_state;

		switch ((string) $data) {
			case 'participants':
				$this->fields = array ();
				foreach (($wp_crm_state->get('basket')) as $product => $quantity) {
					$wp_crm_product = new WP_CRM_Product (array (
						'series' => wp_crm_extract_series($product),
						'number' => wp_crm_extract_number($product)));
					$this->fields[] = array (
							'class' => 'label',
							'label' => $wp_crm_product->get('short name'),
							);
					for ($c = 1; $c<=$quantity; $c++) {
						$this->fields[] = array (
							'class' => 'participant',
							'fields' => array (
								strtolower($product).'_'.$c.'_last_name' => array (
									'label' => $c.'. Nume'
									),
								strtolower($product).'_'.$c.'_first_name' => array (
									'label' => $c.'. Prenume'
									),
								strtolower($product).'_'.$c.'_email' => array (
									'label' => $c.'. E-mail'
									),
								strtolower($product).'_'.$c.'_phone' => array (
									'label' => $c.'. Telefon'
									),
								strtolower($product).'_'.$c.'_uin' => array (
									'label' => $c.'. CNP'
									),
								)
							);
						}
					$this->fields[] = array ( 'class' => 'separator' );
					}
				$this->fields[] = array (
					'fields' => array (
						'button' => array (
							'type' => 'submit',
							'label' => 'Trimite',
							'method' => 'post',
							'action' => '',
							),
						),
					);
				break;
			case 'payment':
				break;
			default:
				$this->fields = array (
					array (
						'class' => 'basketwrap',
						'fields' => array (
							'basket' => array (
								'type' => 'basket',
								'default' => $wp_crm_state->get('basket')
								),
					)), array (
						'label' => 'Persoana Fizica',
						'class' => 'tab',
						'fields' => array (
							'p_last_name' => array (
								'label' => 'Nume',
								),
							'p_first_name' => array (
								'label' => 'Prenume',
								),
							'p_uin' => array (
								'label' => 'CNP',
								),
							'p_email' => array (
								'label' => 'E-Mail',
								'filters' => array (
									'empty' => 'Adresa de E-mail este obligatorie.',
									'email' => 'Adresa de E-mail nu este valida.',
									)
								),
							'p_phone' => array (
								'label' => 'Telefon',
								'filter' => array (
									'empty' => 'Numarul de telefon este obligatoriu.',
									'phone' => 'Numarul de telefon nu este valid.',
									)
								),
					)), array (
						'label' => 'Persoana Juridica',
						'class' => 'tab',
						'fields' => array (
							'c_name' => array (
								'label' => 'Companie',
								),
							'c_uin' => array (
								'label' => 'Cod Fiscal',
								),
							'c_rc' => array (
								'label' => 'Reg. Com.',
								),
							'c_address' => array (
								'label' => 'Adresa',
								),
							'c_county' => array (
								'label' => 'Judetul',
								),
							'c_bank' => array (
								'label' => 'Banca',
								),
							'c_account' => array (
								'label' => 'Cont IBAN',
								),
							'c_email' => array (
								'label' => 'E-Mail',
								'filter' => array (
									'empty' => 'Adresa de E-mail este obligatorie.',
									'email' => 'Adresa de E-mail nu este valida.',
									),
								),
							'c_phone' => array (
								'label' => 'Telefon',
								'filter' => array (
									'empty' => 'Numarul de telefon este obligatoriu.',
									'phone' => 'Numarul de telefon nu este valid.',
									),
								),
					)), array (
						'fields' => array (
							'button' => array (
								'type' => 'submit',
								'label' => 'Trimite',
								'method' => 'post',
								'action' => '',
								),
							),
						));
			}
		}

	public function get ($key = null, $options = null) {
		return $this->fields;
		}

	public function __toString () {
		return serialize ($this->fields);
		}

	public function __destruct () {
		}
	};

class WP_CRM_Form {
	private $html;
	private $fields;
	private $errors;
	private $class;

	public function __construct ($data = null) {
		$this->html = '';
		$this->class = 'wp-crm-form';
		$this->errors = array ();
		$this->fields = array ();

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
		if ($key == 'fields') {
			if (is_array ($data))
				$this->fields = $value;
			elseif (is_object ($value) && ($value instanceof WP_CRM_Form_Structure))
				$this->fields = $value->get ();
			elseif (is_string($value)) {
				$structure = new WP_CRM_Form_Structure ($value);
				$this->fields = $structure->get ();
				unset ($structure);
				}
			}
		}

	public function get ($key = null, $options = null) {
		}

	private function _process ($data, $type = null) {
		global $wp_crm_state;
		switch ($type) {
			case 'basket':
				$data = explode ('+', trim($data));
				$out = array ();
				if (!empty($data))
					foreach ($data as $pair) {
						$pair = explode ('-', trim($pair));
						if ($pair[0])
							$out[$pair[0]] = $pair[1];
						}
				$wp_crm_state->set ('basket', $out);
				break;
			default:
				$out = $data;
			}
		return $out;
		}
	
	private function _filter ($data, $type, $filter) {
		return FALSE;
		}

	public function action () {
		global $wp_crm_basket;

		if (!empty($this->fields))
		foreach ($this->fields as $label => $column) {
			if (!empty($column['fields']))
			foreach ($column['fields'] as $key => $field) {
				$this->fields[$label]['fields'][$key]['default'] = $this->_process($_POST[$key], $field['type']);
				if (!empty($field['filters']))
					foreach ($field['filters'] as $filter => $err_message) {
						$error = $this->_filter ($this->fields[$label]['fields'][$key]['default'], $field['type'], $filter) ? $err_message : null;
						if ($error) $this->errors[] = $error;
						}
				}
			}

		return $this->errors;
		}

	private function _render ($key, $field, $wrap = array ('<li>', '</li>')) {
		if (is_array($field)) {
			$out = '';

			switch ($field['type']) {
				case 'basket':
					if ($field['label']) $out .= '<label>'.$field['label'].':</label>';
					if (!empty($field['default'])) {
						$out .= '<ul class="'.$this->class.'-basket">';
						$total = 0;
						$hidden = '';
						foreach ($field['default'] as $product => $value) {
							$hidden .= $product.'-'.$value.'+';
							$wp_crm_product = new WP_CRM_Product (array (
								'series' => wp_crm_extract_series ($product),
								'number' => wp_crm_extract_number ($product)
								));
							$out .= '<li><span><select name="'.$key.'-'.$product.'">';
							for ($c = 0; $c<10; $c++)
								$out .= '<option value="'.$c.'"'.($c == $value ? ' selected' : '').'>'.$c.'</option>';
							$out .= '</select> x</span> <a href="">' . $wp_crm_product->get ('short name') . '</a><br />';
							$out .= '<span rel="'.$wp_crm_product->get('current code').'">'.$value.' x '.$wp_crm_product->get('price').' lei = '.($value * $wp_crm_product->get('price')).' lei</span></li>';

							$total += $value * $wp_crm_product->get('price');
							}
						$out .= '<li><hr /></li>';
						$out .= '<li class="'.$this->class.'-basket-total">Total: '.$total.' lei</li>';
						$out .= '</ul>';
						$out = '<input type="hidden" name="'.$key.'" value="'.$hidden.'" />' . $out;
						}
					break;
				
				case 'checkbox':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if (!$field['wrap']) $out .= '<ul class="'.$this->class.'-list">';
					foreach ($field['options'] as $k => $v)
						if (!$field['wrap']) $out .= '<li><input type="checkbox" name="'.$key.'_'.$k.'" /> <label class="'.$this->class.'-label">'.$v.'</label></li>';
					$out .= '</ul>';
					break;
				case 'radio':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					if (!$field['wrap']) $out .= '<ul class="'.$this->class.'-list">';
					foreach ($field['options'] as $k => $v)
						if (!$field['wrap']) $out .= '<li><input type="radio" name="'.$key.'" value="'.$k.'" /> <label class="'.$this->class.'-label">'.$v.'</label></li>';
					$out .= '</ul>';
					break;
				case 'select':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					$out .= '<select class="'.$this->class.'-select" name="'.$key.'">';
					foreach ($field['options'] as $k => $v)
						$out .= '<option value="'.$k.'"'.($k == $field['default'] ? ' selected' : '').'>'.$v.'</option>';
					$out .= '</select>';
					break;
				case 'submit':
					$out .= '<input type="submit" name="'.$key.'" value="'.$field['label'].'" />';
					break;
				case 'textarea':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					$out .= '<textarea class="'.$this->class.'-textarea" name="'.$key.'">'.$field['default'].'</textarea>';
					break;
				case 'email':
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					$out .= '<input class="'.$this->class.'-email" type="text" name="'.$key.'" value="'.$field['default'].'" />';
					break;
				default:
					if ($field['label']) $out .= '<label>'.$field['label'].'</label>';
					$out .= '<input type="text" name="'.$key.'" value="'.$field['default'].'" />';
				}

			return $wrap[0] . $out . $wrap[1];
			}
		else
			return '<input type="hidden" name="'.$key.'" value="'.$field.'" />';
		}

	public function render ($echo = FALSE) {
		$this->html = '';
		$tabs = '';

		$c = 0;
		foreach ($this->fields as $column) {
			$classes = array ();
			if ($column['label']) $classes[] = $this->class.'-wrapper';
			if ($column['class']) $classes[] = $this->class.'-'.$column['class'];
			$this->html .= '<div'.(!empty($classes) ? (' class="'.implode(' ', $classes).'"') : '').'>';
			if ($column['class'] == 'tab') {
				$this->html .= '<p class="'.$this->class.'-collabel">'.$column['label'].'</p>';
				$slug = preg_replace('/[^a-z]+/', '-', strtolower(trim($column['label'])));
				$tabs .= $_POST['tabs'] ? 
					('<li><input'.($_POST['tabs'] == $slug ? ' checked="checked"' : '').' type="radio" name="tabs" value="'.$slug.'" />'.$column['label'].'</li>') :
					('<li><input'.($c ? '' : ' checked="checked"').' type="radio" name="tabs" value="'.$slug.'" />'.$column['label'].'</li>');
				$c ++;
				}
			if (in_array ($column['class'], array ('label', 'separator'))) {
				if ($column['label'])
					$this->html .= $column['label'];
				}
			else {
				$this->html .= '<ul class="'.$this->class.'-column'.($column['label'] ? (' '.$this->class.'-tabtarget') : '').'">';
				foreach ($column['fields'] as $key => $field)
					$this->html .= $this->_render ($key, $field);
				}
			$this->html .= '</ul>';
			$this->html .= '</div>';
			}

		if ($tabs) {
			$tabs = '<ul class="' . $this->class . '-tabs">' . $tabs . '</ul>';
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
	};
?>
