<?php
class WP_CRM_MailAgent_Site extends WP_CRM_Model {
	public static $T = 'mailagent_sites';
	protected static $K = array (
		'name',
		'apikey',
		'campaign',
		'mid'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`name` varchar(32) NOT NULL DEFAULT \'\'',
		'`apikey` varchar(32) NOT NULL DEFAULT \'\'',
		'`campaign` int(11) NOT NULL DEFAULT 0',
		'`mid` int(11) NOT NULL DEFAULT 0'
		);
	}
?>
