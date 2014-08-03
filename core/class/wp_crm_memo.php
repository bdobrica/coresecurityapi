<?php
class WP_CRM_Memo extends WP_CRM_Model {
	public static $T = 'memos';
	protected static $K = array (
		'uid',
		'oid',
		'class',
		'memo',
		'stamp'
		);
	public static $F = array (
		'new' => array (
			'uid' => 'Utilizator',
			'oid:hidden' => 'Obiect',
			'class:hidden' => 'Clasa',
			'memo:textarea' => 'Memo'
			),
		'view' => array (
			'stamp:date' => 'Data',
			'uid' => 'Utilizator',
			'memo' => 'Memo'
			),
		'edit' => array (
			),
		'self' => array (
			)
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`class` varchar(32) NOT NULL DEFAULT \'\'',
		'`memo` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);

	protected $objects;

	public function __construct ($data) {
		if (is_object ($data)) $data = array ($data);
		if (is_array ($data) && is_object ($data[0])) {
			$this->objects = array ();
			foreach ($data as $object) {
				$this->objects[] = array (
					'oid' => $object->get (),
					'class' => get_class ($object)
					);
				}

			$data = $this->objects[0];
			}
		parent::__construct ($data);
		}

	public function save ($data = null) {
		if (is_array ($data)) {
			$current_user = wp_get_current_user ();
			$data['uid'] = $current_user->ID;
			$data['stamp'] = time ();

			if (!empty ($data))
			foreach ($data as $key => $val)
				if (in_array ($key, static::$K))
					$this->data[$key] = $data[$key];
			}

		if (!empty ($this->objects))
		foreach ($this->objects as $object) {
			$this->data['oid'] = $object['oid'];
			$this->data['class'] = $object['class'];

			parent::save ();
			}
		}
	};
?>
