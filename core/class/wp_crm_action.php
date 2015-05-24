<?php
/**
 * Actions are linked to Events. Every time an event is fired, it's attached actions are checked and run.
 * This class is linked to the /actions folder which contains action templates. Need to run ::scan() static
 * method to update the action list.
 */
class WP_CRM_Action extends WP_CRM_Model {
	public static $T = 'actions';
	protected static $K = array (
		'eid',					/** event id for the event firing this action */
		'exec',					/** the action slug */
		'title',				/** the title of this action */
		'description',				/** a short description for this action */
		'flags'
		);
	public static $F = array (
		'public' => array (
			),
		'extended' => array (
			),
		'private' => array (
			)
		);
	protected static $U = array (
		'eid',
		'exec'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`eid` int(11) NOT NULL DEFAULT 0',
		'`exec` varchar(64) NOT NULL DEFAULT \'\'',
		'`title` text NOT NULL',
		'`description` text NOT NULL',
		'`flags` int(11) NOT NULL DEFAULT 0'
		);

	private $event;
	private $actions;

	public function run ($data = null) {
		if (!function_exists ($this->data['exec']) && file_exists (dirname(dirname(__FILE__)) . '/actions/' . $this->data['exec'] . '.php')) 
			include (dirname(dirname(__FILE__)) . '/actions/' . $this->data['exec'] . '.php');
		if (!function_exists ($this->data['exec']))
			throw new WP_CRM_Exception (WP_CRM_Exception::Action_Missing);
		if (call_user_func_array ($this->data['exec'], $data) === FALSE)
			throw new WP_CRM_Exception (WP_CRM_Exception::Action_Failure);
		}

	public static function scan () {
		$actions = array ();
		$path = dirname (dirname (__FILE__));
		if (file_exists ( $path . '/actions/' )) {
			if ($d = opendir ($path . '/actions/')) {
				while ($n = readdir ($d)) {
					if (!preg_match ('/\.php$/', $n)) continue;
					if ($f = fopen ( $path . '/actions/' . $n, 'r')) {
						$act = array (	'exec' => '',
								'title' => '',
								'description' => '',
								'events' => '',
								'objects' => '',
								'filter' => '');
						$flag = 0;
						$key = '';
						while ($l = fgets ($f)) {
							switch ($flag) {
								case 1:
									if (strpos ($l, '*/') === 0) {
										$flag = 2;
										if ($act['exec'] && $act['title']) {
											$actions[$act['exec']] = $act;
											}
										}
									else
									if (strpos ($l, 'Action') === 0) {
										$c = strpos ($l, ':');
										if ($c !== FALSE) {
											$key = strtolower (substr ($l, 7, $c - 7));
											$act[$key] = trim (substr ($l, $c + 1));
											}
										}
									else {
										if ($key) $act[$key] .= "\n" . trim ($l);
										}
									break;
								default:
									if (strpos ($l, '/*') === 0) {
										$flag = 1;
										$act['exec'] = str_replace ('.php', '', $n);
										}
								}
							if ($flag == 2) break;
							}
						fclose ($f);
						unset ($act);
						}
					}
				closedir ($d);
				}
			}

		$out = array ();

		foreach ($actions as $action_data) {
			if (isset ($action_data['events']) && ($action_data['events'] == '*')) {
				unset ($action_data['events']);
				$action_data['eid'] = 0;
				}
			if (isset ($action_data['events'])) {
				$event_slugs = explode (',', $action_data['events']);
				unset ($action_data['events']);

				if (!empty ($event_slugs))
				foreach ($event_slugs as $event_slug) {
					echo "Event: $event_slug\n";
					try {
						$event = new WP_CRM_Event ($event_slug);
						}
					catch (WP_CRM_Exception $exception) {
						$event = null;
						echo "Null event.\n";
						}
					if (is_null ($event)) continue;

					$action_data['eid'] = $event->get ();

					$action = new WP_CRM_Action ($action_data);
					try {
						$action->save ();
						$out[$action->get()] = $action;
						}
					catch (WP_CRM_Exception $exception) {
						echo "Action failed.\n";
						}
					}
				}
			}
		return $out;
		}
	}
?>
