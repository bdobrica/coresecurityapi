<?php
/*
 * Should be a TBS wrapper class so it will be able to use all sort of templates: html, xml, open/microsoft office docs, pdfs
 */
class WP_CRM_Template extends WP_CRM_Model {
	const Site	= 0x100;
	const Mail	= 0x200;
	const Document	= 0x400;

	public static $T = 'templates';
	protected static $K = array (
		'cid',
		'subject',
		'content',
		'stamp'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`oid` int(11) NOT NULL DEFAULT 0',			/** office id */
		'`cid` int(11) NOT NULL DEFAULT 0',			/** company id */
		'`mid` int(11) NOT NULL DEFAULT 0',			/** mailer id */
		'`type` int(11) NOT NULL DEFAULT 0',			/** type (see constants) */
		'`comment` text NOT NULL',				/** a description of the template */
		'`subject` text NOT NULL',				/** the subject */ 
		'`content` text NOT NULL',				/** the content */
		'`stamp` int(11) NOT NULL DEFAULT 0',			/** when it was created */
		'FULLTEXT KEY `subject` (`subject`)'			/** full text key on subject */
		);
	public static $F = array (
		'view' => array (
			'cid:company' => 'Companie',
			'mid:mailer' => 'Expeditor',
			'type' => 'Tip',
			'comment' => 'Comentariu',
			'subject' => 'Subiect/Titlu',
			'content:html' => 'Continut',
			'stamp:date' => 'Actualizare'
			),
		'public' => array (
			'cid:company' => 'Companie',
			'mid:mailer' => 'Expeditor',
			'type' => 'Tip',
			'comment' => 'Comentariu',
			'subject' => 'Subiect/Titlu',
			'content:html' => 'Continut',
			'stamp:date' => 'Actualizare'
			),
		'extended' => array (
			),
		'private' => array (
			)
		);

	public function __construct ($data = null) {
		global $wpdb;

		if (is_string($data) && !is_numeric($data)) {
			$sql = $wpdb->prepare ('select * from `' . $wpdb->prefix . static::$T . '` where match(subject) against(%s);', $data);
			$data = $wpdb->get_row ($sql, ARRAY_A);
			}

		parent::__construct ($data);
		}

	public function assign ($role = null, $object = null) {
		if (is_object($object) && ($object instanceof WP_CRM_Model)) {
			$keys = $object->get ('keys');
			if (!empty($keys))
				foreach ($keys as $key) {
					$var = '{' . $role . '.' . parent::slug ($key) . '}';
					$val = $object->get ($key);
					$this->data['subject'] = str_replace ($var, $val, $this->data['subject']);
					$this->data['content'] = str_replace ($var, $val, $this->data['content']);
					}
			}
		else
		if (is_string($role) && is_string($object)) {
			$this->data['subject'] = str_replace ($role, $object, $this->data['subject']);
			$this->data['content'] = str_replace ($role, $object, $this->data['content']);
			}
		}

	public function __toString () {
		return $this->data['content'];
		}
	}
?>
