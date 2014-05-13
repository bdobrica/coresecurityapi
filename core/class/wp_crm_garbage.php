<?php
class WP_CRM_Garbage extends WP_CRM_Model {
	public static $T = 'garbage';
	protected static $K = array (
		'uid',
		'oid',
		'reason',
		'class',
		'object',
		'stamp'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`uid` int(11) NOT NULL DEFAULT 0',
		'`oid` int(11) NOT NULL DEFAULT 0',
		'`reason` text NOT NULL',
		'`class` varchar(64) NOT NULL DEFAULT \'\'',
		'`object` text NOT NULL',
		'`stamp` int(11) NOT NULL DEFAULT 0'
		);
	public static $F = array (
		'new' => array (
			'uid:hidden' => 'User ID',
			'oid:hidden' => 'Object ID',
			'reason:textarea' => 'Argument',
			'class:hidden' => 'Object Class',
			'stamp:hidden' => 'Time Stamp'
			),
		'view' => array (
			'oid' => 'Object ID',
			'reason' => 'Argument',
			'class' => 'Object Class',
			),
		'public' => array (
			'uid:hidden' => 'User ID',
			'oid:hidden' => 'Object ID',
			'reason' => 'Argument',
			'class:hidden' => 'Object Class',
			'stamp:hidden' => 'Time Stamp'
			),
		'extended' => array (
			),
		'private' => array (
			)
		);

	protected $objects;

	public function __construct ($data = null, $opts = null) {
		global
			$current_user;

		if (is_null ($data)) throw new WP_CRM_Exception (__CLASS__ . ' :: Cannot throw NULL objects!', WP_CRM_Exception::NullData);;
		if (is_object ($data)) {
			$this->data['uid']	= (int) $current_user->ID;
			$this->data['oid']	= (int) $data->get ();
			$this->data['reason']	= (string) $opts;
			$this->data['class']	= get_class ($data);
			$this->data['object']	= serialize ($data);
			$this->data['stamp']	= (int) time ();

			$this->objects		= array ($data);
			}
		else
		if (is_array ($data) && !empty ($data) && is_object (current($data))) {
			$this->data['uid']	= (int) $current_user->ID;
			$this->data['oid']	= 0;
			$this->data['reason']	= (string) $opts;
			$this->data['class']	= '';
			$this->data['object']	= '';
			$this->data['stamp']	= (int) time ();

			$this->objects		= $data;
			}
		else
		if (is_numeric ($data)) {
			try {
				$this->object	= new $this->data['class'] ($this->data['oid']);
				}
			catch (WP_CRM_Exception $wp_crm_exception) {
				$this->object	= null;
				}
			parent::__construct ((int) $data);
			}
		}

	public function restore () {
		}

	public function save ($data = null) {
		/*
		INFO: save a copy of the object in the databases
		*/
		$out = array ();

		if (!empty($this->objects))
			foreach ($this->objects as $object) {
				$this->data['oid'] = (int) $object->get();
				$this->data['class'] = get_class ($object);
				$this->data['object'] = serialize ($object);
				parent::save ();

				/*
				INFO: remove the original from the database
				*/
				$object->delete ();
				if ($this->ID) $out[] = $this->ID;
				$this->ID = null;
				}

		return $out;
		}
	}
?>
