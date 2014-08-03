<?php
class WP_CRM_Table {
	const Rows	= 0;
	const Cols	= 1;
		
	private $rows;
	private $cols;

	public function __construct ($data = null) {
		$this->rows = array ();
		$this->cols = array ();
		}

	public function get ($key = null, $opts = null) {
		switch ((string) $key) {
			case 'rows':
				return $this->rows;
				break;
			case 'cols':
				return $this->cols;
				break;
			}
		}

	private static function insert ($array, $data = null, $position = null) {
		$position = is_null ($position) ? 0 : $position;
		if (empty($array)) return array ($data);

		$copy = array (); $d = 0;
		for ($c = 0; $c<count ($array); $c++) {
			if ($c == $position) $copy[$d++] = $data;
			$copy[$d++] = $array[$c];
			}
		}

	public function number ($label = '', $column = null) {
		if (is_null ($column)) $column = 1;
		$column --;

		$this->cols = self::insert ($this->cols, $label, $column);

		$rows = array (); $c = 1;
		foreach ($this->rows as $row)
			$row = self::insert ($row, $c++, $column);
		}

	public function push ($data = null, $where = WP_CRM_Table::Rows) {
		if ($where == WP_CRM_Table::Rows) $this->rows[] = $data;
		if ($where == WP_CRM_Table::Cols) $this->cols[] = $data;
		}
	}
?>
