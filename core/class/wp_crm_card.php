<?php
/**
 * Card is an address book standard wrapper. It's linked to a database table to implement rolodexes.
 */
class WP_CRM_Card extends WP_CRM_Model {
	public static $T = 'cards';
	protected static $K = array (
		'adr',
		'agent',
		'anniversary',
		'bday',
		'begin',
		'caladruri',
		'caluri',
		'categories',
		'class',
		'clientpidmap',
		'email',
		'fburl',
		'fn',
		'gender',
		'geo',
		'impp',
		'key',
		'kind',
		'label',
		'lang',
		'logo',
		'mailer',
		'member',
		'name',
		'nickname',
		'note',
		'org',
		'photo',
		'prodid',
		'related',
		'rev',
		'role',
		'sort-string',
		'source',
		'tel',
		'title',
		'tz',
		'url',
		'xml'
		);
	protected static $Q = array (
		'`adr` varchar(256) NOT NULL DEFAULT \'\'',
		'`agent` varchar(256) NOT NULL DEFAULT \'\'',
		'`anniversary` varchar(8) NOT NULL DEFAULT \'\'',
		'`bday` varchar(8) NOT NULL DEFAULT \'\'',
		'`begin` varchar(256) NOT NULL DEFAULT \'\'',
		'`caladruri` varchar(256) NOT NULL DEFAULT \'\'',
		'`caluri` varchar(256) NOT NULL DEFAULT \'\'',
		'`categories` varchar(256) NOT NULL DEFAULT \'\'',
		'`class` varchar(12) NOT NULL DEFAULT \'\'',
		'`clientpidmap` varchar(256) NOT NULL DEFAULT \'\'',
		'`email` varchar(64) NOT NULL DEFAULT \'\'',
		'`fburl` varchar(256) NOT NULL DEFAULT \'\'',
		'`fn` varchar(256) NOT NULL DEFAULT \'\'',
		'`gender` enum(\'M\',\'F\') NOT NULL DEFAULT \'M\'',
		'`geo` varchar(17) NOT NULL DEFAULT \'\'',
		'`impp` varchar(64) NOT NULL DEFAULT \'\'',
		'`key` text NOT NULL',
		'`kind` enum(\'individual\',\'organization\') NOT NULL DEFAULT \'individual\'',
		'`label` varchar(256) NOT NULL DEFAULT \'\'',
		'`lang` varchar(5) NOT NULL DEFAULT \'\'',
		'`logo` varchar(256) NOT NULL DEFAULT \'\'',
		'`mailer` varchar(256) NOT NULL DEFAULT \'\'',
		'`member` varchar(256) NOT NULL DEFAULT \'\'',
		'`name` varchar(256) NOT NULL DEFAULT \'\'',
		'`nickname` varchar(256) NOT NULL DEFAULT \'\'',
		'`note` text NOT NULL',
		'`org` varchar(256) NOT NULL DEFAULT \'\'',
		'`photo` text NOT NULL',
		'`prodid` varchar(64) NOT NULL DEFAULT \'\'',
		'`related` varchar(256) NOT NULL DEFAULT \'\'',
		'`rev` int(11) NOT NULL DEFAULT 0',
		'`role` varchar(256) NOT NULL DEFAULT \'\'',
		'`sort-string` varchar(256) NOT NULL DEFAULT \'\'',
		'`source` varchar(256) NOT NULL DEFAULT \'\'',
		'`tel` varchar(256) NOT NULL DEFAULT \'\'',
		'`title` varchar(256) NOT NULL DEFAULT \'\'',
		'`tz` varchar(256) NOT NULL DEFAULT \'\'',
		'`url` varchar(256) NOT NULL DEFAULT \'\'',
		'`xml` text NOT NULL'
		);
	
	public static function gather ($objects) {
		if (!is_array ($objects)) {
			if (is_object ($objects))
				$objects = array ($objects);
			else
				$objects = null;
			}
		if (is_null ($objects)) return null;
		if (empty ($objects)) return null;
		$out = array ();
		foreach ($objects as $object) {
			switch ((string) get_class ($object)) {
				case 'WP_CRM_Invoice':
					$out[] = new WP_CRM_Card ($object->buyer);
					$list = new WP_CRM_List ('WP_CRM_Client', array (
						'iid=' . $object->get()
						));
					if (!$list->is ('empty'))
						foreach ($list->get() as $client)
							$out[] = new WP_CRM_Card ($client);
					break;
				}
			}
		return $out;
		}

	public function __construct ($data = null) {
		if (is_object ($data)) {
			$object = $data;
			$data = array ();
			switch ((string) get_class ($object)) {
				case 'WP_CRM_Client':
				case 'WP_CRM_Person':
					$data = array (
						'adr' => $object->get ('address'),
						'email' => $object->get ('email'),
						'fn' => $object->get ('name'),
						'name' => $object->get ('name'),
						'tel' => $object->get ('phone'),
						'rev' => time(),
						'source' => 'class://WP_CRM_Person/' . $object->get ('id')
						);
					break;
				case 'WP_CRM_Company':
					$data = array (
						'adr' => $object->get ('address'),
						'email' => $object->get ('email'),
						'tel' => $object->get ('phone'),
						'rev' => time(),
						'source' => 'class://WP_CRM_Company' . $object->get ('id')
						);
					break;
				case 'WP_CRM_Invoice':
					$data = array (
						);
					break;
				}
			}

		parent::__construct ($data);
		}
	}
?>
