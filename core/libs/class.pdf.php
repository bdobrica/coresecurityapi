<?php
class PDF extends FPDF {
	private $font;

	function __construct ($o = 'P') {
		parent::__construct($o);
		$this->AddFont ('arial');
		$this->AddFont ('arial', 'B');
		$this->AddFont ('arial', 'BI');
		$this->AddFont ('arial', 'I');

		$this->font = 'arial';
		$this->SetFont($this->font, '', 9);
		$this->addPage();
		}

	public function fix ($str) {
		$str = str_replace (array (
			"\xc3\x82",
			"\xc4\x82",
			"\xc3\x8e",
			"\xc8\x98",
			"\xc8\x9a",

			"\xc3\xa2",
			"\xc4\x83",
			"\xc3\xae",
			"\xc8\x99",
			"\xc8\x9b",
			), array (
			chr(194), #226 a
			chr(195), #227 a
			chr(206), #238 i
			chr(170), #186 s
			chr(222), #254 t
			
			chr(226),
			chr(227),
			chr(238),
			chr(186),
			chr(254)
			), $str);
		return $str;
		}

	private function cut ($string, $length) {
		if ($this->GetStringWidth($string) < $length) return array ($string , '');
		$string = explode (' ', $string);
		$cutted = '';
		$reminder = '';
		$size = 0;
		$over = 0;
		foreach ($string as $word) {
			$width = $this->GetStringWidth($word.' ');
			if ($over) $reminder .= $word . ' ';
			else {
				if ($size + $width < $length) {
					$size += $width;
					$cutted .= $word . ' ';
					}
				else {
					$over = 1;
					$reminder .= $word . ' ';
					}
				}
			}
		return array (trim($cutted), trim($reminder));
		}

	public function style ($html = 'p') {
		if ($html == 'p') {
			$this->SetTextColor (0, 0, 0);
			$this->SetFont($this->font, '', 8);
			}
		if ($html == 'diploma:h1') {
			$this->SetFont('copperplate-light', '', 55);
			}
		if ($html == 'diploma:h2') {
			$this->SetFont('copperplate-light', '', 30);
			}
		if ($html == 'diploma:h3') {
			$this->SetFont('copperplate-light', '', 20);
			}
		if ($html == 'diploma:h4') {
			$this->SetFont('copperplate-light', '', 14);
			}
		if ($html == 'diploma:em') {
			$this->SetFont('broadway', '', 25);
			}
		if ($html == 'large')
			$this->SetFont($this->font, '', 11);
		if ($html == 'cnfpa')
			$this->SetFont($this->font, '', 11);
		if ($html == 'cnfpa:name')
			$this->SetFont($this->font, '', 15);
		if ($html == 'opis')
			$this->SetFont($this->font, '', 11);
		if ($html == 'badge')
			$this->SetFont($this->font, 'B', 25);
		if ($html == 'badge:special')
			$this->SetFont('bookman-old-style', '', 25);
		if ($html == 'badge:special-small')
			$this->SetFont('bookman-old-style', '', 15);
		if ($html == 'h1' || $html == 'badge:small')
			$this->SetFont($this->font, 'B', 15);
		if ($html == 'h2')
			$this->SetFont($this->font, 'B', 13);
		if ($html == 'h3' || $html == 'opis:strong')
			$this->SetFont($this->font, 'B', 11);
		if ($html == 'strong')
			$this->SetFont($this->font, 'B', 8);
		if ($html == 'em')
			$this->SetFont($this->font, 'I', 8);
		if ($html == 'small')
			$this->SetFont($this->font, '', 7);
		if ($html == 'color: red')
			$this->SetTextColor (204, 0, 0);
		if ($html == 'color: green')
			$this->SetTextColor (0, 204, 0);
		if ($html == 'color: blue')
			$this->SetTextColor (0, 0, 204);
		}

	public function columns ($height = 6, $data = array()) {
		$number = count($data);
		$width = floor(190 / $number);
		for ($c = 0; $c < $number; $c++)
			$this->Cell ($width, $height, $data[$c]);
		$this->Ln ();
		}
	
	public function table ($cols, $rows, $height = 6, $border = '1') {
		$table_height = 0;
		if (is_array($cols[0])) {
			$head_rows = count($cols);
			$table_height += $head_rows;
			$cols_numb = count($cols[0]);
			for ($c = 0; $c < $head_rows; $c++) {
				$d = 0;
				foreach ($cols[0] as $col => $length) {
					$attr = explode (';', $col);
					if (count($attr) == 1) $attr = null;
					else {
						$col = $attr[count($attr) - 1];
						$attr[count($attr) - 1] = null;
						}

					
					if (!empty($attr))
						foreach ($attr as $html) $this->style ($html);			

					if ($c == 0)
						$this->Cell($length, $height, trim($col), 'TLR', 0, 'C');
					else
					if ($c < ($head_rows - 1))
						$this->Cell($length, $height, trim($cols[$c][$d++]), 'LR', 0, 'C');
					else
						$this->Cell($length, $height, trim($cols[$c][$d++]), 'LRB', 0, 'C');
		
					if (!empty($attr))
						$this->style();
					}
				$this->Ln ();
				}

			$cols = $cols[0];
			}
		else {
			$table_height ++;
			foreach ($cols as $col => $length) {
				$attr = explode (';', $col);
				if (count($attr) == 1) $attr = null;
				else {
					$col = $attr[count($attr) - 1];
					$attr[count($attr) - 1] = null;
					}

				$align = 'L';				
				if (!empty($attr))
					foreach ($attr as $html) {
						if ($html == 'align: right') $align = 'R';
						else
						if ($html == 'align: center') $align = 'C';
						else
						$this->style ($html);
						}

				$this->Cell($length, $height, trim($col), $border, 0, $align);

				if (!empty($attr))
					$this->style();
				}
			$this->Ln();
			}
		if (!empty($rows))
		foreach ($rows as $row) {
			$overtext = array ();
			$overflow = 1;
			$overline = 0;
			while ($overflow) {
				$overflow = 0;
				$overline++;
				reset ($row);
				reset ($cols);

				foreach ($row as $key => $col) {
					$length = current ($cols);
					$over = '';
					if ($length > 4) {
						list ($text, $over) = $this->cut (trim($col), $length - 4);
						if (trim($text) && trim($over)) {
							$row[$key] = $text;
							$overflow = 1;
							}
						}
					$overtext[$key] = $over;
					next ($cols);
					}

				reset ($row);
				reset ($cols);
				foreach ($row as $col) {
					$length = current ($cols);
					$this->Cell($length, $height, $col, ($overline == 0 ? 'T' : '').($overflow == 0 ? 'B' : '').'LR' );
					next ($cols);
					}
				$this->Ln();
				$row = $overtext;
				}
			$table_height += $overline;
			}
		return $table_height;
		}

	public function out ($path = '', $dest = 'I') {
		$this->Output($path, $dest);
		}
	};

?>
