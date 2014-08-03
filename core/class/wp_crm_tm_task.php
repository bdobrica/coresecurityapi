<?php
class WP_CRM_TM_Task {
	private $ID;
	private $start;
	private $deadline;
	private $importance;
	private $urgency;
	private $assignedby;
	private $responsible;
	private $title;
	private $description;
	private $arrays;

	public function __construct ($data = '') {
		global $wpdb;
		if (is_numeric($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'tm_tasks` where id=%d;', (int) $data);
			if (WP_CRM_Debug) echo "WP_CRM_TM_Task::construct::sql( $sql )\n";
			$data = $wpdb->get_row ($sql);
			$this->planned = (int) $data->planned;
			$this->deadline = (int) $data->deadline;
			$this->importance = (int) $data->importance;
			$this->urgency = (int) $data->urgency;
			$this->assignedby = new WP_CRM_TM_Employee ((int) $data->aid);
			$this->responsible = new WP_CRM_TM_Employee ((int) $data->rid);
			$this->title = trim($data->title);
			$this->description = trim($data->description);
			$this->ID = (int) $data->id;
			}
		else
		if (is_array($data)) {
			$this->planned = is_numeric($data['planned']) ? (int) $data['planned'] : strtotime($data['planned']);
			$this->deadline = is_numeric($data['deadline']) ? (int) $data['deadline'] : strtotime($data['deadline']);
			$this->importance = (int) $data['importance'];
			$this->importance = $this->importance < 0 ? 0 : $this->importance;
			$this->importance = $this->importance > 10 ? 10 : $this->importance;
			$this->urgency = (int) $data['urgency'];
			$this->urgency = $this->urgency < 0 ? 0 : $this->urgency;
			$this->urgency = $this->urgency > 10 ? 10 : $this->urgency;
			if ($data['assignedby'])
				$this->assignedby = new WP_CRM_TM_Employee ((int) $data['assignedby']);
			else
				$this->assignedby = new WP_CRM_TM_Employee ();
			$this->responsible = new WP_CRM_TM_Employee ((int) $data['responsible']); 
			$this->title = trim($data['title']);
			$this->description = trim($data['description']);
			}
		}

	public function get ($key = '', $value = '') {
		if ($key == 'title') return $this->title;
		if ($key == 'responsible') return $this->responsible;
		if ($key == 'planned') return $this->planned;
		if ($key == 'deadline') return $this->deadline;
		if ($key == 'importance') return $this->importance;
		if ($key == 'urgency') return $this->urgency;
		return $this->ID;
		}

	public function set ($key = '', $value = '') {
		}

	public function add ($key = '', $value = '') {
		if ($key == 'array') {
			$this->arrays[] = $value;
			}
		}

	public function save () {
		global $wpdb;
		if ($this->ID) return FALSE;
		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'tm_tasks` (planned,deadline,importance,urgency,aid,rid,title,description) values (%d,%d,%d,%d,%d,%s,%s);', array (
			$this->planned,
			$this->deadline,
			$this->importance,
			$this->urgency,
			$this->assignedby->get(),
			$this->responsible->get(),
			$this->title,
			$this->description
			));
		if (WP_CRM_Debug) echo "WP_CRM_TM_Task::save::sql( $sql )\n";
		$wpdb->query ($sql);
		$this->ID = (int) $wpdb->insert_id;
		return TRUE;
		}

	public function __destruct () {
		}
	}
?>