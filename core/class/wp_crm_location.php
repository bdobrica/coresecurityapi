<?php
class WP_CRM_Location {
	private $ID;
	private $title;
	private $address;
	private $city;
	private $directions;
	private $map;

	public function __construct ($data = null) {
		global $wpdb;
		if (is_numeric ($data)) {
			$this->ID = intval ($data);
			$location = $wpdb->get_row ($wpdb->prepare ('select * from `'.$wpdb->prefix.'product_locations` where id=%d;', $data));
			if ($location) {
				$this->title = $location->title;
				$this->address = $location->address;
				$this->city = $location->city;
				$this->directions = $location->directions;
				$this->map = $location->map;
				}
			}
		elseif (is_array ($data)) {
			$this->title = $data['title'];
			$this->address = $data['address'];
			$this->city = $data['city'];
			$this->directions = $data['directions'];
			$this->map = $data['map'];
			}
		}

	public function get ($key = '', $value = null) {
		global $wpdb;
		if ($key == 'name' || $key == 'title') return $this->title;
		if ($key == 'address') return $this->address;
		if ($key == 'city') return $this->city;
		if ($key == 'directions') return $this->directions;
		if ($key == 'map') return $this->map;
		return $this->ID;
		}

	public function set ($key = '', $value = null) {
		global $wpdb;
		if ($key == 'title' || $key == 'name') $this->title = $value;
		if ($key == 'address') $this->address = $value;
		if ($key == 'city') $this->city = $value;
		if ($key == 'directions') $this->directions = $value;
		if ($key == 'map') $this->map = $value;
		if ($this->ID) {
			if ($key == 'title' || $key == 'name') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set title=%s;', $this->title));
			if ($key == 'address') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set address=%s;', $this->title));
			if ($key == 'city') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set city=%s;', $this->title));
			if ($key == 'directions') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set directions=%s;', $this->title));
			if ($key == 'map') $wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'product_locations` set map=%s;', $this->title));
			}
		return TRUE;
		}

	public function save () {
		global $wpdb;
		if ($this->ID) return FALSE;
		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'product_locations` (title,address,city,directions,map,flags) values (%s,%s,%s,%s,%d);', array (
			$this->title,
			$this->address,
			$this->city,
			$this->directions,
			$this->map,
			0
			));
		if (WP_CRM_Debug) echo "WP_CRM_Location::save::sql( $sql )\n";
		}

	public function __destruct () {
		}
	}
?>