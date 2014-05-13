<?php
class WP_CRM_Math_Set {
	private $elements;

	public function __construct () {
		$this->elements = array ();
		}

	private function has ($element) {
		$out = FALSE;
		foreach ($this->elements as $_element)
			if ((string) $_element === (string) $element) $out = TRUE;
		return $out;
		}

	public function add ($element) {
		if ($this->has ($element))
			return FALSE;

		$this->elements[] = $element;
		return TRUE;
		}

	public function del ($element) {
		$out = FALSE;
		foreach ($this->elements as $_address => $_element)
			if ((string) $_element === (string) $element) {
				$this->elements[$_address] = null;
				unset ($this->elements[$_address]);
				$out = TRUE;
				}
		return TRUE;
		}

	public function intersect ($set) {
		}

	public function difference ($set) {
		}

	public function union ($set) {
		}

	public function __destruct () {
		}
	}
?>
