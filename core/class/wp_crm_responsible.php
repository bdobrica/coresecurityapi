<?php
class WP_CRM_Responsible {
	private $ID;
	private $user;

	public function __construct ($data = null) {
		if (is_numeric($data)) {
			$this->ID = $data;
			$this->user = get_userdata($this->ID);
			}
		else {
			$this->ID = get_current_user_id();
			$this->user = get_userdata($this->ID);
			}
		}

	public function get ($key = '') {
		if ($key == 'name') return $this->user->display_name;
		return $this->ID;
		}

	public function __destruct () {
		}
	}
?>