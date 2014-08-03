<?php
class WP_CRM_TM_Array {
	private $ID;
	private $title;
	private $description;
	private $tasks;
	private $importance;
	private $color;

	public function __construct ($data = '') {
		global $wpdb;
		if (is_numeric($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'tm_arrays` where id=%d;', (int) $data);
			if (WP_CRM_Debug) echo "WP_CRM_TM_Array::construct::sql( $sql )\n";
			$data = $wpdb->get_row ($sql);
			
			$this->ID = (int) $data->id;
			$this->title = $data->title;
			$this->description = $data->description;
			
			$this->tasks = array ();

			$this->importance = (int) $data->importance;
			$this->color = $data->color;
			}
		else
		if (is_array($data)) {
			$this->tasks = array ();

			$this->title = strtoupper(trim($data['title']));
			$this->description = trim($data['description']);
			$this->importance = (int) $data['importance'];
			$this->color = $data['color'];
			}
		}

	public function get ($key = '', $value = '') {
		if ($key == 'title') return $this->title;
		if ($key == 'description') return $this->description;
		if ($key == 'importance') return $this->importance;
		if ($key == 'color') return $this->color;
		return $this->ID;
		}

	public function set ($key = '', $value = '') {
		global $wpdb;
		if ($key == 'title') {
			$this->title = strtoupper(trim($value));
			if ($this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'tm_arrays` set title=%s where id=%d;', array (
				$this->title,
				$this->ID)));
			}
		if ($key == 'description') {
			$this->description = trim($value);
			if ($this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'tm_arrays` set description=%s where id=%d;', array (
				$this->description,
				$this->ID)));
			}
		if ($key == 'importance') {
			$this->importance = (int) $value;
			$this->importance = $this->importance <  0 ?  0 : $this->importance;
			$this->importance = $this->importance > 10 ? 10 : $this->importance;
			if ($this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'tm_arrays` set importance=%s where id=%d;', array (
				$this->importance,
				$this->ID)));
			}
		if ($key == 'color') {
			$this->colors = $value;
			if ($this->ID)
			$wpdb->query ($wpdb->prepare ('update `'.$wpdb->prefix.'tm_arrays` set color=%s where id=%d;', array (
				$this->color,
				$this->ID)));
			}
		}

	public function add ($key = '', $value = '') {
		if ($key == 'task') {
			$this->tasks[] = $value;
			}
		}

	public function save () {
		global $wpdb;
		if ($this->ID) return FALSE;
		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'tm_arrays` (title,description,importance,color) values (%s,%s,%d,%s);', array (
			$this->title,
			$this->description,
			$this->importance,
			$this->color
			));
		if (WP_CRM_Debug) echo "WP_CRM_TM_Array::save::sql( $sql )\n";
		$wpdb->query ($sql);
		$this->ID = $wpdb->insert_id;
		return TRUE;
		}

	public function __destruct () {
		}
	}
?>