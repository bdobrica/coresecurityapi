<?php
class WP_CRM_MailAgent extends WP_CRM_Model {
	public static $T = 'mailagent';
	protected static $K = array (
		'lid',
		'first_name',
		'last_name',
		'phone',
		'email',
		'mailagent'
		);
	protected static $Q = array (
		'`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT',
		'`lid` int(11) NOT NULL DEFAULT 0',
		'`first_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`last_name` varchar(64) NOT NULL DEFAULT \'\'',
		'`phone` varchar(16) NOT NULL DEFAULT \'\'',
		'`email` varchar(64) NOT NULL DEFAULT \'\' UNIQUE',
		'`mailagent` int(11) NOT NULL DEFAULT 0',
		'UNIQUE (`lid`,`email`)'
		);

	public function set ($key = null, $value = null) {
		switch ((string) $key) {
			case 'mailagent':
		                $client = new SoapClient("http://www.mailagent.ro/MailAgentService.wsdl");
				try {
					$answer = $client->addSubscriber ('9b82909c30456ac902e14526e63081d4', 9843, $this->data['email'],
						array (
							'name' => $this->data['last_name'],
							'surename' => $this->data['first_name'] )
						);
					}
				catch (SoapFault $e) {
					}
			default:
				parent::set ($key, $value);
			}
		}
	}
?>
