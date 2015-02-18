<?php
class WP_CRM_Task extends WP_CRM_Model {
	public static $version = '1.0.1';
	public static $T = 'tasks';
	protected static $K = array (
		'oid',					/** office id */
		'cid',					/** company id */
		'pid',					/** product id */
		'parent',				/** previous task id; if 0, this is the first task */
		'uid',					/** the user that generated this task */
		'rid',					/** the employee responsible, id */
		'title',
		'description',
		'employees',				/** the employee list for this task */
		'resources',				/** the resources used for this task */
		'machines',				/** the machines used for this task */
		'factor',
		'importance',
		'urgency',
		'duration_min',
		'duration_opt',
		'duration_max',
		'deadline'
		);
	public static $F = array (
		'new' => array (
			'title' => 'Denumire',
			'pid:hidden' => 'Produs',
			'description:textarea' => 'Descriere',
			'rid:array;employees_list' => 'Responsabil',
			'employees:inventory;employees_list' => 'Angajati',
			'machines:inventory;machines_list' => 'Echipamente',
			'resources:inventory;resources_list' => 'Resurse',
			#'importance' => 'Importanta',
			#'urgency' => 'Urgenta',
			#'duration_min' => 'Durata minima,
			'factor:bool' => 'Task multiplicativ?',
			'duration_opt:duration' => 'Durata',
			#'duration_max' => 'Durata maxima',
			#'deadline:date' => 'Termen Limita'
			),
		'view' => array (
			'title' => 'Denumire',
			'rid' => 'Responsabil',
			'description:textarea' => 'Descriere',
			'resources:inventory' => 'Resurse',
			#'importance' => 'Importanta',
			#'urgency' => 'Urgenta',
			#'duration_min' => 'Durata minima',
			'duration_opt:duration' => 'Durata optima',
			#'duration_max' => 'Durata maxima',
			#'deadline:date' => 'Termen Limita'
			),
		'edit' => array (
			'title' => 'Denumire',
			'pid:hidden' => 'Produs',
			'description:textarea' => 'Descriere',
			'rid:array;employees_list' => 'Responsabil',
			'employees:inventory;employees_list' => 'Angajati',
			'machines:inventory;machines_list' => 'Echipamente',
			'resources:inventory;resources_list' => 'Resurse',
			#'importance' => 'Importanta',
			#'urgency' => 'Urgenta',
			#'duration_min' => 'Durata minima',
			'factor:bool' => 'Task multiplicativ?',
			'duration_opt:duration' => 'Durata optima',
			#'duration_max' => 'Durata maxima',
			#'deadline:date' => 'Termen Limita'
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`cid` int(11) NOT NULL DEFAULT 0',
		'`pid` int(11) NOT NULL DEFAULT 0',
		'`parent` int(11) NOT NULL DEFAULT 0',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`rid` int(11) NOT NULL DEFAULT 0',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`resources` text NOT NULL',
		'`factor` float(9,2) NOT NULL DEFAULT 0.00',
		'`importance` int(2) NOT NULL DEFAULT 0',
		'`urgency` int(2) NOT NULL DEFAULT 0',
		'`duration_min` float(5,2) NOT NULL DEFAULT 0.00',
		'`duration_opt` float(5,2) NOT NULL DEFAULT 0.00',
		'`duration_max` float(5,2) NOT NULL DEFAULT 0.00',
		'`deadline` int(11) NOT NULL DEFAULT 0'
		);

	public function get ($key = null, $opts = null) {
		global $wpdb;

		if (is_string ($key))
			switch ($key) {
				case 'duration':
					return 0.1666 * ($this->data['duration_min'] + 4*$this->data['duration_opt'] + $this->data['duration_max']);
					break;
				case 'leaf':
					return array (
						'oid' => __CLASS__ . '-' . $this->ID,
						'name' => $this->data['title'],
						'duration_min' => $this->data['duration_min'],
						'duration_opt' => $this->data['duration_opt'],
						'duration_max' => $this->data['duration_max']
						);
					break;
				case 'employees_list':
				case 'responsibles_list':
					if ($this->data['cid']) {
						$structure = new WP_CRM_Company_Structure ($this->data['cid']);
						return $structure->get ('list');
						}

					$structure = new WP_CRM_Company_Structure (1);
					return $structure->get ('list');
					break;
				case 'resources_list':
					$out = array ();
					$sql = 'select id,title from `' . $wpdb->prefix . WP_CRM_Resource::$T . '`';
					$rows = $wpdb->get_results ($sql);
					if (!empty ($rows))
						foreach ($rows as $row)
							$out[$row->id] = $row->title;
					return $out;
					break;
				case 'resources':
					return self::_unserialize ($this->data['resources']);
					break;
				default:
					return parent::get ($key, $opts);
				}
		}

	public function unlink () {
		if ($this->ID && $this->get ('parent')) {
			$this->set ('parent', 0);
			}
		}

	public function link ($to = null) {
		if (!is_null ($to) && (is_object ($to) || is_numeric ($to)) && $this->ID && !$this->get ('parent'))
			$this->set ('parent', is_object ($to) ? $to->get () : ((int) $to));
		}
	}
?>
