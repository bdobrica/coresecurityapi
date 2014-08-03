<?php
/**
 * Chart is a vizualization class for various data. It's a wrapper for Google Charts.
 * Intended to be used in View class.
 */
class WP_CRM_Chart {
	private $id;
	private $title;
	private $x;
	private $y;
	private $z;
	private $size;
	private $data;

	public function __construct ($z = null) {
		$this->data = array ();
		$this->z = is_string($z) ? array ($z) : $z;
		}

	public function push ($value = null, $opts = null) {
		if ((count($this->z) == 1) && is_string($value)) $value = array ($value);
		switch ((string) $opts) {
			case 'add':
			case '+':
				$found = FALSE;
				foreach ($this->data as $key => $data) {
					if ($data[0] == $value[0]) {
						$found = TRUE;
						for ($c = 1; $c<count($this->z); $c++)
							$this->data[$key][$c] = (int) $data[$c] + (int) $value[$c];
						}
					}
				if (!$found) $this->data[] = $value;
				break;
			default:
				$this->data[] = $value;
			}
		}
	
	public function set ($key = null, $value = null) {
		switch ((string) $key) {
			case 'id':
				$this->id = (string) $value;
				break;
			case 'title':
				$this->title = (string) $value;
				break;
			case 'size':
				$this->size = $value;
				break;
			case 'size_x':
				$this->size['x'] = $value;
				break;
			case 'size_y':
				$this->size['y'] = $value;
				break;
			}
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'id':
				return $this->id;
				break;
			case 'title':
				return $this->title;
				break;
			case 'size_x':
				return $this->size['x'];
				break;
			case 'size_y':
				return $this->size['y'];
				break;
			default:
				return array_merge (array ($this->z), $this->data);
			}
		}
	}
?>
