<?php
class WP_CRM_Room extends WP_CRM_Model {
	public static $T = 'rooms';
	protected static $K = array (
		'name',
		'description',
		'rows',
		'cols',
		'lat',
		'long',
		'address',
		'city',
		'county',
		'country',
		'directions'
		);
	public static $F = array (
		'new' => array (
			'name' => 'Denumire',
			'seats:seats' => 'Locuri'
			),
		'view' => array (
			'name' => 'Denumire',
			),
		'edit' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`name` varchar(64) NOT NULL DEFAULT \'\'',
		'`description` TEXT NOT NULL',
		'`rows` int(11) NOT NULL DEFAULT 0',
		'`cols` int(11) NOT NULL DEFAULT 0',
		'`lat` float(8,5) NOT NULL DEFAULT 0.00',
		'`long` float(8,5) NOT NULL DEFAULT 0.00',
		'`address` TEXT NOT NULL',
		'`city` varchar(64) NOT NULL DEFAULT \'\'',
		'`county` varchar(64) NOT NULL DEFAULT \'\'',
		'`country` varchar(2) NOT NULL DEFAULT \'RO\'',
		'`directions` TEXT NOT NULL'
		);

	private $chart;
	
	private function load ($blocks) {
		$this->chart = array ();
		if (!$blocks->is ('empty'))
		foreach ($blocks->get () as $block) $this->chart[] = array (
			$block->get ('top'),
			$block->get ('left'),
			$block->get ('top') + $block->get ('rows'),
			$block->get ('left') + $block->get ('cols'),
			$block->get ()
			);
		}

	private function block ($x, $y) {
		if ($x > $this->data['rows']) return FALSE;
		if ($y > $this->data['cols']) return FALSE;

		foreach ($this->chart as $block)
			if ($x >= $block[0] && $y >= $block[1] && $x < $block[2] && $y < $block[3]) return $block[4];
		}

	public function draw ($width = null, $height = null, $echo = FALSE) {
		$seat_width = floor ($width / $this->data['cols']) - 4;
		$seat_height = floor ($height / $this->data['rows']) - 4;
		$unit = 'px';

		$offset = floor (($width - ($seat_width + 4) * $this->data['cols']) / 2);

		$total = array ();

		$blocks = new WP_CRM_List ('WP_CRM_Room_Block', array ('rid=' . $this->ID));
		$this->load ($blocks);

		$out = '<div class="wp-crm-room" style="width: ' . $width . $unit . ';">' . "\n";
		for ($row = 0; $row < $this->data['rows']; $row++) {
			$out .= '<div class="wp-crm-room-row" style="margin-left: ' . $offset . $unit . '">';
			for ($col = 0; $col < $this->data['cols']; $col++) {
				$block = $this->block ($row, $col);
				if ($block) $total[0]++;
				$total[$block]++;
				$out .= '<div class="wp-crm-room-seat" style="width: ' . $seat_width . $unit . '; height: ' . $seat_height . $unit . ';">' . $block . '</div>';
				}
			$out .= '</div>';
			}
		$out .= '<ul class="wp-crm-room-legend">' . "\n";

		ksort ($total);

		foreach ($total as $key => $value)
			$out .= '<li><label>' . $key . '</label> x ' . $value . '</li>' . "\n";

		$out .= '</ul>' . "\n";
		$out .= '</div>' . "\n";

		if (!$echo) return $out;
		echo $out;
		}	
	}
?>
