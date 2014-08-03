<?php
class WP_CRM_TM_Action {
	private $ID;
	private $task;
	private $spawn;
	private $employee;
	private $comment_begin;
	private $comment_end;
	private $date_begin;
	private $date_end;

	public function __construct ($data = '') {
		if (is_numeric ($data)) {
			$sql = $wpdb->prepare ('select * from `'.$wpdb->prefix.'tm_actions` where id=%d;', (int) $data);
			if (WP_CRM_Debug) echo "WP_CRM_TM_Action::construct::sql( $sql )\n";
			$data = $wpdb->get_row ($sql);
			$this->ID = (int) $data->id;
			$this->task = new WP_CRM_TM_Task ((int) $data->tid);
			$this->spawn = new WP_CRM_TM_Action ((int) $data->sid);
			$this->employee = new WP_CRM_TM_Employee((int) $data->eid);
			$this->comment_begin = $data->comment_begin;
			$this->comment_end = $data->comment_end;
			$this->date_begin = (int) $data->date_begin;
			$this->date_end = (int) $data->date_end;
			}
		else
		if (is_array ($data)) {
			$this->task = new WP_CRM_TM_Task ((int) $data['task']);
			$this->employee = new WP_CRM_TM_Employee ((int) $data['employee']);
			}
		}

	public function get ($key = '', $value = '') {
		return $this->ID;
		}
	
	public function set ($key = '', $value = '') {
		}

	public function start ($comment = '') {
		if ($this->ID) return FALSE;
		$this->comment_begin = trim($comment);
		$this->date_begin = time();
		$sql = $wpdb->prepare ('insert into `'.$wpdb->prefix.'tm_actions` (tid,eid,comment_begin,date_begin) values (%d,%d,%s,%d);', array (
			$this->task->get(),
			$this->employee->get(),
			$this->comment_begin,
			$this->date_begin
			));
		if (WP_CRM_Debug) echo "WP_CRM_TM_Action::start::sql( $sql )\n";
		$wpdb->query ($sql);
		$this->ID = $wpdb->insert_id;
		return TRUE;
		}

	public function spawn ($data = '') {
		if (!$this->ID) return FALSE;
		if ($this->spawn) return FALSE;
		$this->spawn = new WP_CRM_TM_Action ($data);
		$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'tm_actions` set sid=%d where id=%d;', array (
			$this->spawn->get(),
			$this->ID
			));
		if (WP_CRM_Debug) echo "WP_CRM_TM_Action::spawn::sql( $sql )\n";
		$wpdb->query ($sql);
		return TRUE;
		}

	public function stop ($comment = '') {
		if (!$this->ID) return FALSE;
		if ($this->date_end) return FALSE;
		$this->date_end = time();
		$this->comment_end = trim($comment);
		$sql = $wpdb->prepare ('update `'.$wpdb->prefix.'tm_actions` set comment_end=%s,date_end=%d where id=%d;', array (
			$this->comment_end,
			$this->date_end,
			$this->ID
			));
		if (WP_CRM_Debug) echo "WP_CRM_TM_Action::stop::sql( $sql )\n";
		$wpdb->query ($sql);
		return TRUE;
		}

	public function __destruct () {
		}
	}
?>