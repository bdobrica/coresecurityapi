<?php
/*
INFO: Helper class. Can apply methods on groups of objects. Is able
to make groups from WP_CRM_List, array of objects or packed string.
Packing is of the form Class_A-ID#1,ID#2,ID#3;Class_B-ID#4,ID#5,ID#6
*/
class WP_CRM_Group {
	private $objects;

	public function __construct ($data = null) {
		$this->objects = array ();

		if ($data instanceof WP_CRM_List) {
			$classname = $data->get ('class');
			$this->objects[$classname] = array ();

			if (!$data->is ('empty'))
				foreach ($data->get () as $object)
					$this->objects[$classname][$object->get ('id')] = $object;
			}
		else
		if (is_array ($data)) {
			if (!empty ($data))
				foreach ($data as $object)
					$this->objects[get_class ($object)][$object->get ('id')] = $object;
			}
		else
		if (is_string ($data)) {
			$classes = explode (';', trim($data));
			if (!empty ($classes))
				foreach ($classes as $class) {
					list ($classname, $objects) = explode ('-', $class);
					$classname = trim($classname);
					$objects = explode (',', $objects);
					if (!empty ($objects))
						foreach ($objects as $object)
							$this->objects[$classname][(int) $object] = new $classname ((int) $object);
					}
			}
		}

	private static function groupcall ($objects, $method, $param = null) {
		$out = array ();
		if (in_array ('WP_CRM_Invoice', array_keys ($objects)) && ($method == 'view'))
			$out['pdf'] = $param[1] ? $param[1] : null;


		$c = 0;
		if (!empty ($objects))
			foreach ($objects as $classname => $classes)
				if (!empty ($classes))
					foreach ($classes as $objectid => $object)
						if (is_object ($object) && method_exists ($object, $method)) {
							if (($classname == 'WP_CRM_Invoice') && ($method == 'view')) {
								if (($c++) && is_object($out['pdf'])) $out['pdf']->AddPage();	
								$paramcopy = $param;
								$paramcopy[0] = $paramcopy[0] ? $paramcopy[0] : FALSE;
								$paramcopy[1] = $out['pdf'];
								$out['pdf'] = call_user_func_array (array ($object, $method), (array) $paramcopy);
								}
							else
								$out[$object->get()] = call_user_func_array (array ($object, $method), (array) $param);
							}

		return $out;
		}

	public function pack () {
		$out = array ();
		foreach ($this->objects as $classname => $classes)
			if (!empty ($classes))
				$out[] = $classname . '-' . implode (',', array_keys ($classes));
		return implode (';', $out);
		}

	public function push ($object) {
		$classname = get_class ($object);
		$objectid = $object->get ();
		if (isset ($this->objects[$classname]) && isset ($this->objects[$classname][$objectid])) return FALSE;
		if (isset ($this->objects[$classname])) {
			$objects[$classname][$objectid] = $object;
			return TRUE;
			}
		$this->objects[$classname] = array ($objectid => $object);
		}

	public function needs ($key = null) {
		switch ((string) $key) {
			case 'iframe':
				return in_array ('WP_CRM_Invoice', (array) array_keys ((array) $this->objects)) ? TRUE : FALSE;
				break;
			}
		return FALSE;
		}

	public function save () {
		self::groupcall ($this->objects, 'save');
		}

	public function delete () {
		self::groupcall ($this->objects, 'delete');
		}

	public function set ($key = null, $value = null) {
		self::groupcall ($this->objects, 'set', array (
			$key,
			$value
			));
		}

	public function get ($key = null, $opts = null) {
		return self::groupcall ($this->objects, 'get', array (
			$key,
			$opts
			));
		}

	public function view () {
		return self::groupcall ($this->objects, 'view', func_get_args());
		}
	}
?>
