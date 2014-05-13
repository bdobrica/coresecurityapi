<?php
class WP_CRM_TM_Register {
	private $tasks;
	private $actions;
	private $employees;

	public function __construct ($data = '') {
		global $wpdb;
		$this->tasks = array();
		if ($data == 'tasks') {
			$sql = $wpdb->prepare('select id from `'.$wpdb->prefix.'tm_tasks` order by deadline desc;');
			if (WP_CRM_Debug) echo "WP_CRM_TM_Register::construct('tasks')::sql( $sql )\n";
			$tasks = $wpdb->get_col ($sql);
			if (!empty($tasks))
			foreach ($tasks as $task)
				$this->tasks[] = new WP_CRM_TM_Task ($task);
			}
		}

	public function has ($key = '') {
		if ($key == 'tasks') return empty($this->tasks) ? FALSE : TRUE;
		return FALSE;
		}

	public function get ($key = '', $value = '') {
		if ($key == 'tasks') return $this->tasks;
		return array();
		}

	public function __destruct () {
		}
	}
?>